<?php

/**
 * @Copyright (c) 2011 Ganji Inc.
 * @author        longweiguo@ganji.com
 * @date          2011-03-30
 *
 * http分析与跳转的一些操作
 */

/**
 * @class http分析与跳转的一些操作
 * 包括分析url、ip，获取$_GET、$_POST、$_REQUEST数据，页面跳转等
 */
class RequestUtil {
    /**
     *  判断是否是ajax操作的数据
     */
    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
    
    /**
     * 判断页面是否是数据post过来
     * @return boolean
     */
    public static function isPost() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
    
    /**
     *  判断 http  method 是否为get
     */
    public static function isGet() {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    private static function _gpcStripSlashes(& $arr) {
        if (is_array ($arr)) {
            foreach ($arr as &$v) {
                self::_gpcStripSlashes ($v);
            }
        } else {
            $arr = stripslashes ($arr);
        }
    }

    public static function gpcStripSlashes() {
        $_striped = false;
        if (!$_striped && get_magic_quotes_gpc ()) {
            $_striped = true;
            self::_gpcStripSlashes ($_GET);
            self::_gpcStripSlashes ($_POST);
            self::_gpcStripSlashes ($_COOKIE);
            self::_gpcStripSlashes ($_REQUEST);
        }
        //set_magic_quotes_runtime (0);
    }

    /// 取得当前页面的url
    /// @return string 返回当前页面的url
    public static function getCurrentUrl() {
        $pageURL = 'http';
        if (! empty ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
//             $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * 获取POST中的数据
     * @param $key				POST中的key
     * @param $default			如果数据不存在，默认返回的值。默认情况下为false
     * @param $enableHtml		返回的结果中是否允许html标签，默认为false
     * @return string
     */
    public static function getPOST($key, $default = false, $enableHtml = false) {
        if (array_key_exists($key, $_POST)) {
            return !$enableHtml ? strip_tags($_POST[$key]) : $_POST[$key];
        }
        return $default;
    }

    /**
     * 获取GET中的数据
     * @param $key				GET中的key
     * @param $default			如果数据不存在，默认返回的值。默认情况下为false
     * @param $enableHtml		返回的结果中是否允许html标签，默认为false
     * @return string
     */
    public static function getGET($key, $default = false, $enableHtml = false) {
        if (array_key_exists($key, $_GET)) {
            return !$enableHtml ? strip_tags($_GET[$key]) : $_GET[$key];
        }
        return $default;
    }

    /// 获取REQUEST中的数据
    /// @param $key				REQUEST中的key
    /// @param $default			如果数据不存在，默认返回的值。默认情况下为false
    /// @param $enableHtml		返回的结果中是否允许html标签，默认为false
    /// @return string
    public static function getREQUEST($key, $default = false, $enableHtml = false) {
        if (array_key_exists($key, $_REQUEST)) {
            return !$enableHtml ? strip_tags($_REQUEST[$key]) : $_REQUEST[$key];
        }
        return $default;
    }

	
	/// 获取COOKIE中的数据
	/// @param $key             COOKIE中的key
	/// @param $default         如果数据不存在，默认返回的值。默认情况下为false
	/// @param $enableHtml      返回的结果中是否允许html标签，默认为false
	/// @return string
	public static function getCOOKIE($key, $default = false, $enableHtml = false) {
		if (isset ($_COOKIE[$key])) {
			return !$enableHtml ? strip_tags($_COOKIE[$key]) : $_COOKIE[$key];
		}
		return $default;
	}

    /// 获取用户ip
    /// @param $useInt			是否将ip转为int型，默认为true
    /// @param $returnAll		如果有多个ip时，是否会部返回。默认情况下为false
    /// @return string|array|false
    public static function getIp($useInt = true, $returnAll=false) {
        $ip = getenv('HTTP_CLIENT_IP');
		if($ip && strcasecmp($ip, "unknown") && !preg_match("/192\.168\.\d+\.\d+/", $ip)) {
            return self::_returnIp($ip, $useInt, $returnAll);
		} 
        
        $ip = getenv('HTTP_X_FORWARDED_FOR');
        if($ip && strcasecmp($ip, "unknown")) {
            return self::_returnIp($ip, $useInt, $returnAll);
        }

        $ip = getenv('REMOTE_ADDR');
        if($ip && strcasecmp($ip, "unknown")) {
            return self::_returnIp($ip, $useInt, $returnAll);
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
            if($ip && strcasecmp($ip, "unknown")) {
                return self::_returnIp($ip, $useInt, $returnAll);
            }
        }
        
		return false;
    }

    private static function _returnIp($ip, $useInt, $returnAll) {
        if (!$ip) return false;

        $ips = preg_split("/[，, _]+/", $ip);
        if (!$returnAll) {
            $ip = $ips[count($ips)-1];
            return $useInt ? self::ip2long($ip) : $ip;
        }

        $ret = array();
        foreach ($ips as $ip) {
            $ret[] = $useInt ? self::ip2long($ip) : $ip;
        }
        return $ret;
    }

    /// 对php原ip2long的封装，原函数在win系统下会出现负数
    /// @author zhoufan <zhoufan@staff.ganji.com>
    /// @param string $ip
    /// @return int
    public static function ip2long($ip) {
        return sprintf ('%u', ip2long ($ip));
    }

    /// 对php原long2ip的封装
    /// @author zhoufan <zhoufan@staff.ganji.com>
    /// @param int $long
    /// @return string
    public static function long2ip($long) {
        return long2ip ($long);
    }
    
    public static function getCurrentQuery() {
        $current = RequestUtil::getCurrentUrl();
        $s = parse_url($current);
        return $s['query'];
    }

}
