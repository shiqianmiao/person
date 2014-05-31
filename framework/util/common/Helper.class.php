<?php

/**
 * @Copyright (c) 2011 Ganji Inc.
 * @file          /ganji_v5/app/common/PageBase.class.php
 * @author        longweiguo@ganji.com
 * @date          2011-08-20
 *
 * 显示协助
 */

class Helper {
    
    private static $dirs = array();
    private static $loaded = array();

    public static function call($funcName, $params=array()) {
        $funcName = $funcName . '_helper';
        foreach (self::$dirs as $dir) {
            $filePath = $dir . '/' . $funcName . '.php';
            if (file_exists($filePath)){
                if (empty(self::$loaded[$filePath])) {
                    include_once($filePath);
                    self::$loaded[$filePath] = 1;
                }
                if (function_exists($funcName)){
                    return $funcName($params);
                }
            }
        }
        return NULL;
    }

    public static function addDir($dir) {
        if (empty($dir)) return false;
        
        if (is_string($dir)) {
            if (is_dir($dir) && !in_array($dir, self::$dirs)) {
                array_unshift(self::$dirs, $dir);
                return true;
            }
        } else if (is_array($dir)) {
            foreach ($dir as $_dir) {
                if (is_dir($_dir) && !in_array($_dir, self::$dirs)) {
                    array_unshift(self::$dirs, $_dir);
                }
            }
            return true;
        }
        
        return false;
    }
}