<?php
/**
 * controller class for config
 *
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms\config
 */
namespace echocms;

class config
{
    protected $dbh;
    protected $config;

    function __construct()
    {
        require CONFIG_DIR. '/model/config.php';
        $this->configModel = new configModel();
        $this->dbh = $this->configModel->setupDbh();
        $this->config = $this->configModel->readConfig();
        require 'assets/lang/en_GB.php';
        $this->lang = $msg;
        require CONFIG_DIR. '/model/auth.php';
        $this->authModel = new authModel($this->dbh, $this->config, $this->lang);
    }

    /**
     * Edit configuration items
	   *
	   */
    function editConfig()
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $postMaxSize = $this->configModel->getMaxFileSize();
        if (!empty($_POST) || (!empty($_FILES['postedImage']['name']) && $_FILES['postedImage'] ['error'] == 0 )) {
            $configUpdates = $this->configModel->getPostedConfig();
            $this->configModel->updateConfig($configUpdates);

            $elements = $this->configModel->getPostedElements();
            $this->configModel->updateConfigElements($elements);

            $pages = $this->configModel->getPostedPages();
            $this->configModel->updateConfigPages($pages);

            $result['message'] = 'configuration updated';
        }
        $config = $this->config = $this->configModel->readConfig();
        $menu = 'admin';
        $timezones = \DateTimeZone::listIdentifiers();

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/config/editConfig.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }
}
