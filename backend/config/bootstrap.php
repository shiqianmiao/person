<?php
//载入全局配置文件
require_once dirname(__FILE__) . '/config.php';
require_once FRAMEWORK_PATH . '/mvc_org/PageVars.class.php';
require_once FRAMEWORK_PATH . '/mvc_org/Bootstrap.class.php';

// 加载配置文件
require_once dirname(__FILE__) . '/' . (defined('APP_CONFIG_FILE_NAME') ? APP_CONFIG_FILE_NAME : 'config.inc.php');

$displayError = true;
//$displayError = true;
//$displayError = true;

if ($displayError) {
     error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

if (!$displayError) {
    function sys_get_404_url() {
        $attrs = Bootstrap::getRouteParams();
        if ($attrs['ajax']) {
            $url = PageVars::URL_404_AJAX;
            if (!empty($attrs['jsonp'])) {
                $url .= '?' . Bootstrap::$ROUTE_NAMES['jsonp'] . '=' . rawurlencode($attrs['jsonp']);
            }
        } else if ($attrs['iframe']) {
            $url = PageVars::URL_404_IFRAME;
        } else {
            $url = PageVars::URL_404;
        }
        return $url;
    }

    //捕获错误，显示404
    function sys_my_error($errno, $errstr, $errfile, $errline) {
        $attrs = Bootstrap::getRouteParams();
        Response::redirect(sys_get_404_url(), $attrs['iframe']);
    }
    set_error_handler('sys_my_error', E_USER_ERROR);

    //捕获异常
    function sys_my_exception($exception) {
        $attrs = Bootstrap::getRouteParams();
        Response::redirect(sys_get_404_url(), $attrs['iframe']);
    }
    set_exception_handler('sys_my_exception');

}
$routeParams = BackendPageConfig::getRequestParams();

//判断文件是否存在
$className = ucfirst($routeParams['module']) . 'Page';
$file = $routeParams['channel_dir'] . '/' . $routeParams['module_dir'] . '/' . $className . '.class.php';
if (!file_exists($file)) {
    Response::redirect('/404.php?errorMessage=' . $className . ' not found');
}

Bootstrap::setRouteParams(array(
    'channel'       => $routeParams['channel'],
    'channel_dir'   => $routeParams['channel_dir'],
    'dir'           => $routeParams['channel_dir'] . '/' . $routeParams['module_dir'],
    'module'        => $routeParams['module'],
    'action'        => $routeParams['action'],
    'ajax'          => $routeParams['is_ajax'],
    'iframe'        => $routeParams['is_iframe'],
    'jsonp'         => $routeParams['jsonp_callback'],
));

$templateConfig = BackendPageConfig::getTemplateConfig();

Bootstrap::setAppDir(BACKEND . '/channel');
Bootstrap::addHelperDir($templateConfig['helper_path']);
Bootstrap::addTemplateDir($templateConfig['template_path']);
require_once BACKEND . '/common/BackendPage.class.php';

//判断方法是否存在，不再就404
include_once $file;
$instance = new $className();
if (!method_exists($instance, $routeParams['action'] . 'Action')) {
    Response::redirect('/404.php?errorMessage=' . $routeParams['action'] . ' not found');
}
