<?PHP
if (! defined('WEB_V3')) {
    define('WEB_V3', dirname(dirname(__FILE__)));    // web_v3 根目录
}
require_once WEB_V3 . '/../conf/config.inc.php';

if (defined('DISPLAY_ERROR_WEB_V3') && DISPLAY_ERROR_WEB_V3 == true) { 
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
    ini_set('display_errors', 1); 
} else {
    error_reporting(0);
    ini_set('display_errors', 0); 
}
require_once FRAMEWORK_PATH . '/util/http/RequestUtil.class.php';
RequestUtil::gpcStripSlashes();

require_once FRAMEWORK_PATH . '/util/http/ResponseUtil.class.php';
require_once FRAMEWORK_PATH . '/mvc/PageVars.class.php';
require_once FRAMEWORK_PATH . '/mvc/Bootstrap.class.php';

if (!defined('DISPLAY_ERROR_WEB_V3') || DISPLAY_ERROR_WEB_V3 == false) {
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
        require_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';
        LoggerGearman::logFatal(array('data' => 'errno:' . $errno . ' errstr:'
            . $errstr . ' errfile:' . $errfile . ' errline:' . $errline, 'identity' => 'sys_my_error'));
        $attrs = Bootstrap::getRouteParams();
        ResponseUtil::redirect(sys_get_404_url(), $attrs['iframe']);
    }

    set_error_handler('sys_my_error', E_USER_ERROR);

    //捕获异常
    function sys_my_exception($exception) {
        require_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';
        LoggerGearman::logFatal(array('data' => $exception, 'identity' => 'sys_my_exception'));
        $attrs = Bootstrap::getRouteParams();
        ResponseUtil::redirect(sys_get_404_url(), $attrs['iframe']);
    }

    set_exception_handler('sys_my_exception');
}

Bootstrap::setAppDir(WEB_V3 . '/app');
Bootstrap::addHelperDir(WEB_V3 . '/plugin');
Bootstrap::addTemplateDir(WEB_V3 . '/template');
header('content-Type: text/html; charset=utf-8');
