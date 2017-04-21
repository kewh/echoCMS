<?php
/**
 * controller class for router
 *
 * Router parses the incoming URL and parameters, then calls
 * the appropriate controller for the requested page.
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms\router
 */
namespace echocms;

class router
{
    function __construct()
    {
        $method = $controller = null;
        $params = array();
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $controller = isset($url[0]) ? $url[0] : null;
            $method = isset($url[1]) ? $url[1] : null;
            unset($url[0], $url[1]);
            $params = array_values($url);
        }
        if (!$controller) {
            $controller = 'auth';
            $method = 'index';
        }
        if (file_exists(CONFIG_DIR. '/controller/' . $controller . '.php')) {
            require CONFIG_DIR. '/controller/' . $controller . '.php';
        }
        $class = '\\echocms\\' . $controller;
        if (!class_exists($class) || !method_exists($class, $method)) {
            $controller = 'error';
            $method = 'notfound';
            require CONFIG_DIR. '/controller/' . $controller . '.php';
        }
        $class = '\\echocms\\' . $controller;
        $class = new $class();
        call_user_func_array(array($class, $method), $params);
    }
}
