<?php
/**
 * index - the htaccess file sends all page requests here,
 *         which then just passes them on to the router.
 *
 * @since 1.0.3
 * @author Keith Wheatley
 * @package echocms
 */
namespace echocms;

class index
{
    function __construct()
    {
        session_start();
        define('CONFIG_DIR', getcwd());
        require CONFIG_DIR . '/controller/router.php';
	      require CONFIG_DIR . '/config/url.php';
        define('CONFIG_URL', $config_URL. '/cms/');
        define('CONFIG_URL_DIR', $config_URL_DIR);
        $app = new router;
    }
}

/**
 *
 *  create an instance of this class
 *
 */
$index = new index();
