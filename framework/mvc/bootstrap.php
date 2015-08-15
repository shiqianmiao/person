<?PHP
if (!empty($_SERVER['DEBUG'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once dirname(__FILE__) . '/Router.class.php';
require_once dirname(__FILE__) . '/Dispatch.class.php';
require_once dirname(__FILE__) . '/../util/http/Request.class.php';
require_once dirname(__FILE__) . '/../util/http/Response.class.php';
if (defined('CHARSET')) {
    Response::setCharset(CHARSET);
} 

class SysError {
    public static function run() {
        $args = func_get_args();
        if (is_int($args[0])) {
            $errorCode    = $args[0];
            $errorMessage = $args[1];
        } else {
            $errorCode    = $args[0]->getCode();
            $errorMessage = $args[0]->getMessage();
        }
        if (!empty($_SERVER['DEBUG'])) {
            echo $errorMessage . "\n";
            print_r(debug_backtrace());
            exit;
        }

        $params = array('对不起！系统错误，请稍后再试。', $errorCode);
        Dispatch::run(Router::getController(), 'error', $params, Router::getModule());
    }
}

set_error_handler(array('SysError', 'run'), E_USER_ERROR);
set_exception_handler(array('SysError', 'run'));
