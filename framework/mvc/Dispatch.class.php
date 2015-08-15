<?PHP
require_once dirname(__FILE__) . '/BaseController.class.php';
require_once dirname(__FILE__) . '/../util/http/Response.class.php';
require_once dirname(__FILE__) . '/../util/http/Request.class.php';

class Dispatch {
    public static function run($controller = null, $action = null, $params = null, $module = null) {
        if (empty($controller)) {
            self::_error($action, '未指定controller');
        }

        if (empty($action)) {
            self::_error($action, '未指定action');
        }

        $className = self::_getName('controller', $controller, 'Controller');
        $dirPath = PROJECT_PATH;
        if (!empty($module)) {
            $dirPath .= '/modules/' . $module;
        }
        $filePath  = $dirPath . '/controller/' . $className . '.class.php';
        $actionName = self::_getName('action', $action, 'Action');
        if (!file_exists($filePath)) {
            self::_error($action, "文件 {$filePath} 不存在");
            if ($actionName == 'errorAction') {
                $className = 'IndexController';
                $filePath = PROJECT_PATH . '/controller/' . $className . '.class.php';
            }
            if (!file_exists($filePath)) {
                self::_error($action, "文件 {$filePath} 不存在");
            }
        }
        include_once $filePath;
        if (!class_exists($className)) {
            self::_error($action, "文件 {$filePath} 的 {$className} 类不存在");
        }

        $instance = new $className();
        if (!method_exists($instance, $actionName)) {
            self::_error($action, "文件 {$filePath} 的 {$actionName}方法不存在");
        }
        try {
            if (method_exists($instance, 'init')) {
                $instance->init();
            }
        
            $instance->$actionName($params);
        } catch (Exception $e) {
            self::_error($action, $e->getMessage(), $e->getCode());
        }
    }

    private static function _error($action, $errorMessage, $errorCode = 1) {
        if ($action == 'error') {
            if (!empty($_SERVER['DEBUG'])) {
                echo $errorMessage . "\n";
                print_r(debug_backtrace());
                exit;
            } else {
                $params = array(
                    'errorMessage' => '对不起！系统错误，请稍后再试。',
                    'errorCode'    => $errorCode,
                );
                if (Request::isAjax()) {
                    Response::outputJson($params);
                } else if (in_array('?', $_GET)) {
                    $key = array_search('?', $_GET);
                    Response::outputJsonp($params['errorMessage'], $key);
                } else {
                    Response::output($params['errorMessage']);
                }
                exit;
            }
        }
        throw new Exception($errorMessage);
    }

    private static function _getName($type, $name, $ext) {
        if ($type == 'controller') {
            $name = ucfirst($name);
        }

        $str = '';
        if (strpos($name, '_') !== false) {
            $names = explode("_", $name);
            for ($i = 0, $n = count($names); $i < $n; $i++) {
                if ($i > 0) {
                    $names[$i] .= ucfirst($names[$i]);
                } 
                $str .= $names[$i];
            }
        } else {
            $str .= $name;
        }
        
        return $str . $ext;
    }
}
