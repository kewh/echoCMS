<?php
/**
 * model class for admin
 *
 * @since 1.0.13
 * @author Keith Wheatley
 * @package echocms\admin
 */
namespace echocms;

use \MySQLDump;
use \mysqli;

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
        require CONFIG_DIR. '/model/edit_input.php';
        $this->editInput = new editModelInput($this->dbh, $this->config);
        require CONFIG_DIR. '/model/edit_update.php';
        $this->editUpdate = new editModelUpdate($this->dbh, $this->config);
    }

    /**
     * createDatabaseBackup
     * Creates a new folder with backup file of all database tables
     * Uses MySQLDump (see https://github.com/dg/MySQL-dump)
     */
    function createDatabaseBackup()
    {
      require CONFIG_DIR . '/assets/MySQLDump/MySQLDump.php';
      require CONFIG_DIR . '/config/db.php';

      $folder = date('Y-m-d-H-i-s-').'database';
      $folderPath = CONFIG_DIR.'/content/backups/'.$folder;
      if (!mkdir($folderPath)) {
          $this->reportError('cms/model/admin.php. Failed to create folder for database backup: '. $folderPath);
      }
      $MySQLDump = new MySQLDump(new mysqli($db_host, $db_user, $db_pass, $db_name));
      $filename = $folderPath.'/'.$db_name.'.sql';
      $handle =  fopen($filename, 'wb');
      $MySQLDump->write($handle);
    }

    /**
     * bulkLoadImages
     * Loops through item folders in the bulkload folder and adds item data and images to cms.
     * Users SSE(server sent event). See note on SSE in controller/admin/recreateImages.
     */
    function bulkLoadImages()
    {
      header('Content-Type: text/event-stream');
      header('Cache-Control: no-cache');
        $items = $this->bulkLoadSetup();
        $this->bulkLoadAdd($items);
        $this->bulkLoadEnd();
        $this->eventMessage('Bulk Load complete', 100);
    }

    /**
     * bulkLoadSetup
     *         Loop through bulk item folders -
     *         add data to array of items
     */
    private function bulkLoadSetup()
    {
        $this->eventMessage('setting up item data from bulkload folder', 2);
        $items = array();
        // get item data from the bulkload folder
        if ($handle = opendir("bulkload")) {
            while ( false !== ($folder = readdir($handle)))
            {
                If ( substr ($folder,0,1) != '.') // exclude system files starting with '.'
                {
                    // parse folder name from format yyyy-mm-dd_topic_subtopic_header
                    $exploded = explode("_", $folder);
                    if (count($exploded) != 4 && count_chars($folder,1)[95] != 3) //4 parameters with 3 underscores (i.e. ASCII 95)
                        $this->eventMessage('ERROR: invalid folder name: '.$folder, 100);

                    // date
                    $date = trim($exploded[0]);
                    $d = date_create_from_format('Y-m-d', $date);
                    if (!$d || $d->format('Y-m-d') !== $date)
                        $this->eventMessage('ERROR: invalid date in folder name: '.$folder, 100);
                    $date = $date . ' 00:00:01';

                    // topic
                    $topic = trim($exploded[1]);
                    $topics = $this->editInput->getTopicsList();
                    if (!in_array($topic, $topics) && !$this->config['topics_updatable']) {
                        $this->eventMessage('config set to topic "not updatable" but attempting to add new topic: '.$topic, 99);
                        $this->eventMessage('ERROR: bulk load cancelled ', 100);
                    }

                    // subtopic
                    $subtopic = trim($exploded[2]);
                    $subtopics = $this->editInput->getSubtopicsList();
                    if (!in_array($subtopic, $subtopics) && !$this->config['subtopics_updatable']) {
                        $this->eventMessage('config set to subtopic "not updatable" but attempting to add new subtopic: '.$subtopic, 99);
                        $this->eventMessage('ERROR: bulk load cancelled ', 100);
                    }

                    // heading
                    $heading = trim($exploded[3]);

                    // get the names of images in the item folder
                    $images = array();
                    if ($handle2 = opendir('bulkload/'.$folder)) {
                        while (false !== ($file = readdir($handle2))) {
                            $url = 'bulkload/'.$folder.'/'.$file;
                            if (
                                $file != "." && $file != ".." && $file != ".DS_Store"  // check this first or error thrown by exif_imagetype
                                && substr($file, 0, 1) != "T"
                                && substr($file,-3) != "txt"
                                &&  exif_imagetype($url) === IMAGETYPE_JPEG )
                                    // not thumbnail, JPG, or system files starting with '.'
                                {
                                    $num = preg_replace('/[^0-9]/', '', $file);
                                    $images[$num] =  $file;
                                }
                        }
                        closedir($handle2);
                    }
                    ksort ($images);
                    $item = array();
                    $item['folder'] = $folder;
                    $item['topic'] = $topic;
                    $item['subtopic'] = $subtopic;
                    $item['heading'] = $heading;
                    $item['date'] = $date;
                    $item['images'] = $images;
                    $items[] = $item;
                }
            }
            closedir($handle);
        }
        else $this->eventMessage('ERROR: failed to open directory bulkload: ', 100);
        $this->eventMessage('&nbsp&nbsp setup item data completed. Total items: '.count($items), 4);
        return($items);
    }

    /**
     * bulkLoadAdd
     *         Loop through array of loaded items
     *         and add data to cms
     */
    private function bulkLoadAdd($items)
    {
        $this->eventMessage('adding items to cms images folder and database.', 6);
        $cnt=0; $percent = 10;

        foreach ($items as $item) {
            ++$cnt;
            $percent += floor(round(90 / count($items)));
            if ($percent>99)$percent=99;
            $this->eventMessage('&nbsp&nbsp adding: '.$item['heading']. ' - item: '.$cnt, $percent);
            $_SESSION['item'] = $this->editInput->setupNewItem();
            $_SESSION['item']['topic'] = $item['topic'];
            $_SESSION['item']['subtopic'] = $item['subtopic'];
            $_SESSION['item']['heading'] = $item['heading'];
            $_SESSION['item']['date'] = $item['date'];

            foreach ($item['images'] as $image) {
                $this->eventMessage(' &nbsp;&nbsp; &nbsp; image: '.$image, 50);
                $image_posted = 'bulkload/'.$item['folder'].'/'.$image;;
                $newImage = $image;
                $newImage = str_replace(' ','-',$newImage);
                $newImage = preg_replace('/[^A-Za-z0-9\-]/', '-', $newImage);
                $newImage = date( 'Y-m-d-H-i-s-') . $newImage;
                $newImage = $newImage . '.jpg'; //note: all files are stored as JPGs
                $this->editInput->processNewImage($image_posted, $newImage);
                $this->editInput->createThumbnail(end($_SESSION['item']['images']));
            }
            $this->editUpdate->publishItem();
        }
        $this->eventMessage(' &nbsp&nbsp adding items complete.', $percent);
    }

    /**
    *     bulkLoadEnd
    *     copies bulkload input folders to 'bulkloaded' folder
    *
    */
    private function bulkLoadEnd()
    {
        $this->eventMessage('copying bulkload input folders to bulkloaded folder', 95);
        $src = CONFIG_DIR.'/bulkload/';
        $dst = CONFIG_DIR.'/bulkloaded/bulkloaded-' . date('Y-m-d-h-i-s');
        if ($this->copyDirectory($src, $dst)) {
            $this->removeDirectory($src);
            if (!mkdir($src))
                $this->reportError('cms/model/admin.php bulkLoadEnd. mkdir - Failed to create directory: '. $src);
            $this->eventMessage(' &nbsp&nbsp copying folders complete', 99);
        }
        else
            $this->eventMessage('ERROR - cms content created OK but failed to copy to bulkloaded folder: '. $src, 100);
    }

    /**
     * Recreate images directory, with sub-directories and images for live and pending items.
     * Users SSE(server sent event). See note on SSE in controller/admin/recreateImages.
     *
     */
    function recreateImages()
    {
        // set headers for SSE stream
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header("Access-Control-Allow-Origin: *");

        // backup images file
        $progressPercent = 0;
        $imagesCount = $this->countImages();
        if (empty($imagesCount)) $imagesCount = 1;
        $backupDate = date('Y-m-d-H-i-s');
        $this->eventMessage('Backup in progress', 5);
        $src = CONFIG_DIR.'/content/images';
        $dst = CONFIG_DIR.'/content/backups/' . $backupDate.'-images';
        if ($this->copyDirectory($src, $dst))
            $this->eventMessage('Backup complete to directory: '.$backupDate.'-images', 9);
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
            '/landscape',    '/portrait',    '/panorama',    '/square'   , '/fluid',
            '/landscape/1x', '/portrait/1x', '/panorama/1x', '/square/1x', '/fluid/1x',
            '/landscape/2x', '/portrait/2x', '/panorama/2x', '/square/2x', '/fluid/2x',
            '/landscape/3x', '/portrait/3x', '/panorama/3x', '/square/3x', '/fluid/3x',
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
        echo PHP_EOL;
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
            if (!is_dir(CONFIG_DIR.'/content/backups/'.$value) || (strtolower(substr($value, 20, 6)) != 'images' && strtolower(substr($value, 20, 8)) != 'database')) {
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

        /* ref: https://stackoverflow.com/questions/4914750/how-to-zip-a-whole-folder-using-php */
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath),\RecursiveIteratorIterator::LEAVES_ONLY);
        set_time_limit(0);
        foreach ($files as $name => $file) {
            if (!$file->isDir()) { // directories are added automatically)
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
