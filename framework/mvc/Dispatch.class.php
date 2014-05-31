<?PHP

/** 
 * @Copyright (c) 2014 miao Inc.
 * @author        miaoshiqian
 * @date          2014-05-20
 *
 * 流程分发
 */

require_once dirname(__FILE__) . '/../util/http/ResponseUtil.class.php';

/**
 * @class: Dispatch
 * 流程分发类
 * 所有用户的访问都是通过此类进行分发到app下不同目录下的BasePage派生类的*Action()方法执行
 */
class Dispatch {
    public static $APP_PATH             = '';          // app所在的路径，结尾不要“/” 
    public static $MODULE_NAME_EXT      = 'Page';      // 模块名称的后缀
    public static $ACTION_NAME_EXT      = 'Action';    // 方法名称的后缀
    
    public static function run($moduleName, $actionName, $moduleDir = '', $contentType = '', $jsonpCallback = '', $args = null) {
        if (!empty($contentType)) {
            ResponseUtil::setContentType($contentType, $jsonpCallback);
        }

        $moduleDirPath = self::getModuleDirPath($moduleDir);
        $className = self::getName('module', $moduleName, self::$MODULE_NAME_EXT);
        $filePath = $moduleDirPath . "/" . $className . ".class.php";
        if (!file_exists($filePath)) {
            trigger_error("文件{$filePath}不存在", E_USER_ERROR);
        }
        
        include_once $filePath;
        if (!class_exists($className)) {
            trigger_error("类{$className}不存在", E_USER_ERROR);
        }

        $instance = new $className();

        if (method_exists($instance, 'init')) {
            $instance->init();
        }
        
        $actionName = self::getName('action', $actionName, self::$ACTION_NAME_EXT);
        if (method_exists($instance, $actionName)) {
            
            if (isset($args) && !empty($args)) {

                call_user_func_array(array(&$instance, $actionName), $args);
            } else {

                $instance->$actionName();
            }
        } else {
            trigger_error("类{$className}的{$actionName}方法不存在", E_USER_ERROR);
        }
    }

    protected static function getModuleDirPath($moduleDir) {
        $moduleDirPath = self::$APP_PATH;
        if (!empty($moduleDir)) {
            if (is_dir($moduleDir)) {
                $moduleDirPath = $moduleDir;
            } else {
                $moduleDirPath .= '/' . $moduleDir;
            }
        }
        if (!is_dir($moduleDirPath)) {
            trigger_error("目录{$moduleDirPath}不存在", E_USER_ERROR);
        }
        return $moduleDirPath;
    }

    protected static function getName($type = 'module', $name, $ext) {
        if (empty($name)) {
            trigger_error("未指定{$type}名称", E_USER_ERROR);
        }

        if ($type == 'module') {
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

