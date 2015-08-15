<?PHP
class Router {
    protected static $_modules        = array();
    protected static $_module         = '';
    protected static $_controller     = '';
    protected static $_action         = '';
    protected static $_params         = array();
    protected static $_run            = false;

    public static function setModules($modules = array()) {
        self::$_modules = $modules;
    }

    public static function getModule() {
        if (!self::$_run) {
            self::$_run = true;
            self::_init();
        }
        return self::$_module;
    }

    public static function getController() {
        if (!self::$_run) {
            self::$_run = true;
            self::_init();
        }
        return self::$_controller;
    }

    public static function getAction() {
        if (!self::$_run) {
            self::$_run = true;
            self::_init();
        }
        return self::$_action;
    }

    public static function getParams() {
        if (!self::$_run) {
            self::$_run = true;
            self::_init();
        }
        return self::$_params;
    }

    public static function createUrl($controller = 'index', $action = 'default', $params = array(), $module = '') {
        $str = '/';
        if (!empty($module)) {
            if (empty(self::$_modules[$module])) {
                throw new Exception("模块{$module}未定义");
            }
            $str .= $module . '/';
        }
        if (is_array($params) && count($params) > 0) {
            if (empty($controller)) {
                $controller = 'index';
            }
            if (empty($action)) {
                $action = 'default';
            }
        }
        if (!empty($controller)) {
            $str .= $controller . '/';
        }
        if (!empty($action)) {
            $str .= $action . '/';
        }
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $val) {
                $str .= rawurlencode($val) . '/';
            }
        }
        return $str;
    }

    private static function _init() {
        $url = preg_replace("/[#\?].*$/", '', $_SERVER['REQUEST_URI']);
        $_paths = explode('/', $url);
        $paths = array();
        foreach ($_paths as $path) {
            if ($path !== '' && $path != '.' && $path != '..') {
                $paths[] = $path;
            }
        }

        if (!empty($paths[0]) && in_array($paths[0], self::$_modules)) {
            self::$_module = array_shift($paths);
        } else {
            self::$_module = null;
        }

        if (count($paths) == 0){
            $paths[] = 'index';
        }
        if (count($paths) == 1){
            $paths[] = 'default';
        }

        self::$_controller = !empty($paths[0]) ? array_shift($paths) : 'index';
        self::$_action     = !empty($paths[0]) ? array_shift($paths) : 'default';

        if (count($paths) > 0) {
            foreach ($paths as $path) {
                self::$_params[] = rawurldecode($path);
            }
        }
    }
}