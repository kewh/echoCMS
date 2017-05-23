<?php
/**
 * controller class for reporting errors to user
 *
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms\error
 */
namespace echocms;

class error
{
    protected $config;
    protected $dbh;

    function __construct()
    {
        require 'model/config.php';
        $this->configModel = new configModel();
        $this->dbh = $this->configModel->setupDbh();
        $this->config = $this->configModel->readConfig();
        require 'assets/lang/en_GB.php';
        $this->lang = $msg;
    }

    /**
     * Requested page not found
     *
     */
    function notfound()
    {
        $menu = null;
        $result['error'] = true;
        $result['message'] = $this->lang['page_not_found'];

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/common/notify.php';
        require CONFIG_DIR. '/view/common/footer.php';
        $this-> __destruct();
    }

	  /**
     * Notify user of error
     *
	   */
    function notify()
    {
        $menu = null;
        $result['error'] = true;
        $result['message'] = $this->lang['system_error'];

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/common/notify.php';
        require CONFIG_DIR. '/view/common/footer.php';
        $this-> __destruct();
    }

	function __destruct()
	{
	}
}
