<?php

class Helper {
    private static $_dirs = array();
    private static $_loaded = array();

    public static function call($funcName, $params=array()) {
        if (empty(self::$_dirs)) {
            self::init();
        }
        $funcName = $funcName . '_helper';

        if (!empty(self::$_loaded[$funcName]) && function_exists($funcName)) {
            return $funcName($params);
        }

        foreach (self::$_dirs as $dir) {
            $filePath = $dir . '/' . $funcName . '.php';
            if (file_exists($filePath)){
                include_once($filePath);
                self::$_loaded[$filePath] = 1;
                if (function_exists($funcName)){
                    return $funcName($params);
                }
            }
        }
        return NULL;
    }

    public static function addDir($dir) {
        if (empty($dir)) return false;
        
        if (is_string($dir) && is_dir($dir) && !in_array($dir, self::$_dirs)) {
            array_unshift(self::$_dirs, $dir);
            return true;
        } 
        
        return false;
    }

    public static function init() {
        Helper::addDir(PROJECT_PATH . '/helper');
        include_once dirname(__FILE__) . '/Router.class.php';
        if (Router::getModule() != '') {
            Helper::addDir(PROJECT_PATH . '/modules/' . Router::getModule() . '/helper');
        }
    }
}
