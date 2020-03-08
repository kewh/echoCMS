<?php
/**
 * controller class for admin
 *
 *
 * @since 1.0.10
 * @author Keith Wheatley
 * @package echocms\admin
 */
namespace echocms;

class admin
{
    protected $dbh;
    protected $config;
    protected $edit;
    public $auth;

    function __construct()
    {
        require 'model/config.php';
        $this->configModel = new configModel();
        $this->dbh = $this->configModel->setupDbh();
        $this->config = $this->configModel->readConfig();
        require 'assets/lang/en_GB.php';
        $this->lang = $msg;
        require 'model/auth.php';
        $this->authModel = new authModel($this->dbh, $this->config, $this->lang);
        require 'model/admin.php';
        $this->admin = new adminModel($this->dbh, $this->config);
    }

        /**
         *  Manage bulk load images.
         *
         * Presents page where user can request to bulkload item data and images to cms.
         *
         * Note: This operation uses SSE (Server Sent Events) to monitor progress. The JavaScript in
         *       view/admin/bulkLoadImages fires a call to controller/admin/bulkLoadImages which uses
    	   *       model/admin/bulkLoadImages to recreate data and sends events to the browser to
         *       report on progress.
    	   */
        function bulkLoadImages()
        {
            if (!$this->authModel->isLogged('admin')) {
                 $this->authModel->accessDenied();
                 exit();
            }
            $menu = 'admin';

            require CONFIG_DIR. '/view/common/header.php';
            require CONFIG_DIR. '/view/admin/bulkLoadImages.php';
            require CONFIG_DIR. '/view/common/footer.php';
        }

        /**
         *  bulkLoadImagesSSE
    	   *
         *  This is the EventSource called by SSE(server sent event). See note on SSE in bulkLoadImages method.
    	   */
        function bulkLoadImagesSSE()
        {
            if (!$this->authModel->isLogged('admin')) {
                $this->authModel->accessDenied();
                exit();
            }
            $config = $this->config = $this->configModel->readConfig();
            $result = $this->admin->bulkLoadImages();
        }

    /**
     *  Manage recreate images.
     *
     * Presents page where user can request and monitor recreation of images.
     *
     * Note: This operation uses SSE (Server Sent Events) to monitor progress. The JavaScript in
     *       view/edit/recreateImages fires a call to controller/edit/recreateImages which uses
	   *       model/edit/recreateImages to recreate images and sends events to the browser to
     *       report on progress.
	   */
    function recreateImages()
    {
       if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $postMaxSize = $this->configModel->getMaxFileSize();
        $config = $this->config = $this->configModel->readConfig();
        $menu = 'admin';

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/admin/recreateImages.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     *  Recreate images.
	   *
     *  This is the EventSource called by SSE(server sent event). See note on SSE in recreateImages method.
	   */
    function recreateImagesSSE()
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $config = $this->config = $this->configModel->readConfig();
        $result = $this->admin->recreateImages();
    }

    /**
     *  Manage backup and restore.
     *
     *
     *
	   */
    function manageBackups()
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $config = $this->config = $this->configModel->readConfig();
        $menu = 'admin';

        $backups = $this->admin->listBackups();

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/admin/manageBackups.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     *  Create image backup archive.
     *
     *
     *
	   */
    function createImageBackup()
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $config = $this->config = $this->configModel->readConfig();
        $menu = 'admin';

        $src = CONFIG_DIR.'/content/images';
        $dst = CONFIG_DIR.'/content/backups/images-' . date('Y-m-d-H-i-s');
        $this->admin->copyDirectory($src, $dst);

        $backups = $this->admin->listBackups();

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/admin/manageBackups.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     *  Delete image backup archive.
     *
     *
     *
	   */
    function deleteImageBackup($backup)
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $config = $this->config = $this->configModel->readConfig();
        $menu = 'admin';

        $dir = CONFIG_DIR. '/content/backups/' . $backup;
        if (is_dir($dir)) $this->admin->removeDirectory($dir);

        $backups = $this->admin->listBackups();

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/admin/manageBackups.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     *  Restore image backup to live, after movung live images to backup.
     *
     *
     *
	   */
    function restoreImageBackup($backup)
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $config = $this->config = $this->configModel->readConfig();
        $menu = 'admin';

        $backup = CONFIG_DIR. '/content/backups/' . $backup;
        $live = CONFIG_DIR.'/content/images';
        $copy = CONFIG_DIR.'/content/backups/images-' . date('Y-m-d-H-i-s');
        if (rename($live, $copy))
            $this->admin->copyDirectory($backup, $live);

        $backups = $this->admin->listBackups();

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/admin/manageBackups.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     *  Download image zip backups.
	   *
     *  actions the download link on view/admin/manageBackups.php
	   */
    function downloadImageBackup($dir)
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $config = $this->config = $this->configModel->readConfig();
        $zipPath = $this->admin->createZipImages($dir);
        $this->admin->downloadZip($zipPath);
        $this->admin->removeZip($zipPath);
    }
}
