<?php
/**
 * @Copyright (c) 2011 Ganji Inc
 * @file          /code_base2/util/session/SessionNamespace.class.php
 * @author        dujian@ganji.com
 * @date          2011-03-31
 *
 * session操作的封装，所有使用session的文件，必须require_one本文件
 */

/**
 * @class: Session
 * @PURPOSE: Session是一个静态类，对session进行统计管理
 */
class Session {

    public static function init() {
        static $initFinish = FALSE;
        if (!$initFinish) {
            $initFinish = TRUE; 
            session_start();

            if( session_id() == 'deleted' ) {
                $newId = 's_' . str_replace('.', '_', microtime(true)) . '_' . rand(1000, 9999);
                session_unset();
                session_destroy();
                session_id($newId);
                setcookie(session_name(), $newId, 0, '/', 'ganji.com');
                error_log(date('Y-m-d H:i:s ') . 'fix session:' . $newId . "\n");
            }
        }
    }
    
    /// @brief 读取或写入session值，注意：仅存取一次,这个一般用于在redirect到某个页面前，先存入某个临时值，到新页面取出。
    /// @param $key  string  SESSION中的key
    /// @param $value    unknown_type    要存入SESSION的值 为空时，表示读取。
    /// @return unknown_type SESSION的值
    
    public static function flashData($key, $value=NULL) {
        self::init();
        if($value===NULL) {
            if (isset($_SESSION[$key])) {
                $value = $_SESSION[$key];
                unset($_SESSION[$key]);
            }
        } else {
            $_SESSION[$key] = $value;
        }
        return $value;
    }
    
    /// @brief 向session中存入某个值
    /// @param $key string SESSION中的key
    /// @param $value unknown_type 要存入SESSION的值 
    /// @return unknown_type 返回当前key对应的旧值，如果原来key不存在，则返回NULL
    
    public static function setValue($key, $value) {
        self::init();
        $ret = NULL;
        if (isset($_SESSION[$key])) {
            $ret = $_SESSION[$key];
        }
        $_SESSION[$key] = $value;
        return $ret;
    }
    
    /// @brief 从session中获取某个值
    /// @param $key string SESSION中的key
    /// @param $default 设置未找到SESSION的返回值，默认是NULL
    /// @return SESSION的值，未找到默认返回NULL
    
    public static function getValue($key, $default=NULL) {
        self::init();
        if (isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return $default;
    }
    
    /// @brief 判断SESSION是否存在指定key
    /// @param $key string SESSION中的key
    /// @return bool 返回真或者假
    
    public static function isExists($key) {
        self::init();
        return isset($_SESSION[$key]);
    }
    
    /// @brief 从session中删除某个值
    /// @param $key string SESSION中的key
    
    public static function delete($key) {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }        
    }
    
    /// @brief 清空Session
    
    public static function clear() {
        self::init();
        session_unset();
        session_destroy();
    }
    
}
