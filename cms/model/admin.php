<?php
/**
 * model class for admin
 *
 * @since 1.0.6
 * @author Keith Wheatley
 * @package echocms\admin
 */
namespace echocms;

class adminModel
{
    protected $config;
    protected $edit;
    protected $dbh;

    function __construct(\PDO $dbh, $config)
    {
        $this->dbh = $dbh;
        $this->config = $config;
        require CONFIG_DIR. '/model/edit.php';
        $this->edit = new editModel($this->dbh, $this->config);
        require CONFIG_DIR. '/model/edit_update.php';
        $this->editUpdate = new editModelUpdate($this->dbh, $this->config);
    }
    /**
     * Recreate images directory, with sub-directories and images for live and pending items.
     * Users SSE(server sent event). See note on SSE in controller/admin/recreateImages.
     *
     */
    function recreateImages()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $progressPercent = 0;
        $imagesCount = $this->countImages();
        if (empty($imagesCount)) $imagesCount = 1;
        $backupDate = date('Y-m-d-H-i-s');

        // backup images file
        $this->eventMessage('Backup in progress', 5);
        $src = CONFIG_DIR.'/content/images';
        $dst = CONFIG_DIR.'/content/backups/images-' . $backupDate;
        if ($this->copyDirectory($src, $dst))
            $this->eventMessage('Backup complete to directory:  images-'. $backupDate, 9);
        else
            $this->eventMessage('Backing up images FAILED, did not proceed with recreate images', 100);

        // create new folders
        $percentPerImage = floor(90 / $imagesCount);
        $this->eventMessage('Recreating images in progress:', $progressPercent);
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $baseFolder = 'temp-images-' . $backupDate;
        $tempFolder = CONFIG_DIR.'/content/backups/' . $baseFolder;
        if (!mkdir($tempFolder)) {
            $this->reportError('cms/model/edit_update.php recreateImages. Failed to create folder: '. $tempFolder);
        }
        $subFolders = array (
            '/landscape',    '/portrait',    '/panorama',    '/square',
            '/landscape/1x', '/portrait/1x', '/panorama/1x', '/square/1x',
            '/landscape/2x', '/portrait/2x', '/panorama/2x', '/square/2x',
            '/landscape/3x', '/portrait/3x', '/panorama/3x', '/square/3x',
            '/thumbnail', '/uncropped', '/original', '/collage');
        foreach ($subFolders as $subFolder) {
            if (!mkdir($tempFolder . $subFolder)) {
                $this->reportError('cms/model/edit_update.php recreateImages. Failed to create folder: '. $tempFolder . $subFolder);
            }
        }

        // recreate live images
        $items = $this->edit->getContentItemsList();
        foreach ($items as $item) {
            $images = $this->edit->getContentImages($item);
            foreach ($images as $image) {
                $progressPercent += ($percentPerImage);
                $this->eventMessage('&nbsp;&nbsp; &nbsp;' . $image['src'], $progressPercent);
                if (!copy(CONFIG_DIR.'/content/images/original/'.$image['src'], $tempFolder.'/original/'.$image['src']))
                    error_log('model/edit_update recreateImages. copy to original failed');
                if (!copy(CONFIG_DIR.'/content/images/uncropped/'.$image['src'], $tempFolder.'/uncropped/'.$image['src']))
                    error_log('model/edit_update recreateImages. copy to uncropped failed');
                if (!copy(CONFIG_DIR.'/content/images/thumbnail/'.$image['src'], $tempFolder.'/thumbnail/'.$image['src']))
                    error_log('model/edit_update recreateImages. copy to thumbnail failed');
            }
            if (count($images) >0) {
                $this->editUpdate->createWebsiteImages($images, 'backups/'.$baseFolder);
                $this->editUpdate->createCollageImage($images, 'backups/'.$baseFolder);
            }
        }

        // recreate images for pending items
        $items = $this->edit->getPendingItemsList();
        foreach ($items as $item) {
            $images = $this->edit->getPendingImages($item);
            foreach ($images as $image) {
                $progressPercent += $percentPerImage;
                $this->eventMessage('&nbsp;&nbsp; &nbsp;' . $image['src'], $progressPercent);
                if (!copy(CONFIG_DIR.'/content/images/original/'.$image['src'], $tempFolder.'/original/'.$image['src']))
                    error_log('model/edit_update recreateImages. copy to original failed');
                if (!copy(CONFIG_DIR.'/content/images/uncropped/'.$image['src'], $tempFolder.'/uncropped/'.$image['src']))
                    error_log('model/edit_update recreateImages. copy to uncropped failed');
                if (!copy(CONFIG_DIR.'/content/images/thumbnail/'.$image['src'], $tempFolder.'/thumbnail/'.$image['src']))
                    error_log('model/edit_update recreateImages. copy to thumbnail failed');
            }
        }

        // move newly created images to live images directory
        $this->removeDirectory(CONFIG_DIR.'/content/images');
        rename($tempFolder, CONFIG_DIR.'/content/images')
            or error_log('model/edit_update recreateImages. rename and move of temp to live folder failed');
        $this->eventMessage('PROCESS COMPLETE - new images created.', 100);
    }

    /**
     * Send message via SSE(Server-Sent Event)
     *       used by method recreateImages.
     *
     * @param string $message
     * @param int $progress
     *
     */
    function eventMessage($message, $progress=0)
    {
        $d = array('message' => $message , 'progress' => $progress);
        echo "data: " . json_encode($d) . PHP_EOL . PHP_EOL;
        ob_flush();
        flush();
    }

    /**
     *  Count all images, live and pending (but not offline)
     *       used by method recreateImages.
     *
     * @return int $imagesCount
     */
    function countImages()
    {
        $stmt = $this->dbh->prepare('SELECT count(*) FROM imagesTable');
        $stmt->execute();
        $imagesCount = $stmt->fetchColumn();

        $status = 'offline';
        $stmt = $this->dbh->prepare('SELECT count(*) FROM pendingImagesTable
                LEFT JOIN pendingItemsTable ON pendingImagesTable.pending_id=pendingItemsTable.id
                WHERE status != "offline"');
        $stmt->execute();
        $imagesCount = $imagesCount + $stmt->fetchColumn();

        return ($imagesCount);
    }

    /**
     * List backups
     *
     *
     * @return string $backups
     */
    function listBackups() {
        $objects = scandir(CONFIG_DIR.'/content/backups');
        foreach ($objects as $key => $value) {
            if (!is_dir(CONFIG_DIR.'/content/backups/'.$value) || strtolower(substr($value, 0, 6)) != 'images') {
                unset($objects[$key]);
            }
        }
        arsort($objects);
        $backups = array();
        foreach ($objects as $object) {
            $backups[] = array('dir'=>$object, 'size'=>$this->dirSize(CONFIG_DIR.'/content/backups/'.$object));
        }

        return($backups);
    }

    /**
     * Returns the size of a directory
     *
     * @param string $dir
     *
     * @return string $fileSize directory size in human readable format
     */
    function dirSize($dir)
    {
        $size = 0;
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file)
        {
            $size += $file->getSize();
        }

        if ($size >= 1073741824) {
          $fileSize = round($size / 1024 / 1024 / 1024,1) . ' GB';
        } elseif ($size >= 1048576) {
            $fileSize = round($size / 1024 / 1024,1) . ' MB';
        } elseif($size >= 1024) {
            $fileSize = round($size / 1024,1) . ' KB';
        } else {
            $fileSize = $size . ' bytes';
        }
        return $fileSize;
    }

    /**
     *  Create a ZIP archive of directory
     *
     *
     * @param string $dir
     *
     * @return string $zipPath
     */
    function createZipImages($dir)
    {
        $dirPath  = CONFIG_DIR.'/content/backups/'. $dir;
        $zipPath  = CONFIG_DIR.'/content/backups/'. $dir . '.zip';
        $zip = new \ZipArchive();
        $result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($result != true) reportError('Failed to create ZIP archive. zipImages. result: ' . $result);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath),\RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file)
        {
            if (!$file->isDir()) // directories are added automatically)
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dirPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        if (!$zip->close())
            reportError('Failed to close ZIP archive.');

        return $zipPath;
    }

    /**
     * Download a Zip archive
     *
     *
     * @param string $zipPath
     */
    function downloadZip($zipPath) {
        if (file_exists($zipPath)) {
            ob_get_clean();
            header("Pragma: public");   header("Expires: 0");   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);    header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=" . basename($zipPath) . ";" );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($zipPath));
            readfile($zipPath);
        }
        else $this->reportError('downloadZip. Requested ZIP archive does not exist: ' . $zipPath);
    }

    /**
     * Removes a zip archive including its files and sub-directories
     *
     *
     * @param string $zipPath
     */
    function removeZip($zipPath) {
        if (!file_exists($zipPath))
            $this->reportError('removeZip. Requested ZIP does not exist: ' . $zipPath);
        elseif (!unlink($zipPath))
            $this->reportError('removeZip. Unable to unlink requested ZIP: ' . $zipPath);
        else return true;
    }

    /**
     * Copies a directory including its files and sub-directories
     *
     *
     * @param string $src source directory
     * @param string $dst destination directory
     */
    function copyDirectory($src, $dst) {

        if (!is_dir($src))
            $this->reportError('cms/model/admin.php copyDirectory. source is not a directory: '. $src);

        if (!mkdir($dst))
            $this->reportError('cms/model/admin.php copyDirectory. mkdir - Failed to create directory: '. $dst);

        $dir = opendir($src);
        while(false !== ( $object = readdir($dir)) ) {
            if ( substr($object, 0,1) != '.' ) {
                if ( is_dir($src . '/' . $object) ) {
                    $this->copyDirectory($src . '/' . $object, $dst . '/' . $object);
                }
                else {
                    if (!copy($src . '/' . $object, $dst . '/' . $object))
                        $this->admin->reportError('Copy image FAILED from: ' . $src . '/' . $object);
                }
            }
        }
        closedir($dir);

        return true;
    }

    /**
     * Removes a directory, including its files and sub-directories
     *
     *
     * @param string $dir
     */
    function removeDirectory($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                         $this->removeDirectory($dir."/".$object);
                    else unlink($dir."/".$object);
                }
            }
            reset($objects);
            if (!rmdir($dir)) $this->reportError('cms/model/admin.php removeDirectory. FAILED to remove: '. $dir);
        }
        else $this->reportError('cms/model/admin.php removeDirectory. not a directory: '. $dir);
    }


    /**
     * Report a system error.
     *
     * Reports error to error_log then sets header location to error notification page and exits script.
     *
     * @param string $message
     */
    public function reportError($message)
    {
        error_log ('cms/model/admin.php  reportError error message: ' . print_r($message, true) );
        header('location: ' . CONFIG_URL. 'error/notify' );
        exit();
	}
}
