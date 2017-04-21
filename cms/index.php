<?php
/**
 * index - the htaccess file sends all page requests here,
 *         which then just passes them on to the router.
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
namespace echocms;

class index
{
    function __construct()
    {
        session_start();
	      require $_SERVER['DOCUMENT_ROOT'] . '/cms/config/url.php';
        define('CONFIG_URL', $config_URL. '/cms/');
        define('CONFIG_DIR', getcwd());
        require CONFIG_DIR . '/controller/router.php';
        $app = new router;
    }
}

/**
 *
 *  create an instance of this class
 *
 */
$index = new index();
