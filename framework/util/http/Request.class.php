<?php
class Request {
    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
    
    public static function isPost() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
    
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

    public static function getCurrentUrl() {
        $pageURL = 'http';
        if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";
        $pageURL .= "://" . $_SERVER["HTTP_HOST"];
        if (!empty($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= ":" . $_SERVER["SERVER_PORT"];
        } 
        $pageURL .= $_SERVER["REQUEST_URI"];
        return $pageURL;
    }

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
            return $useInt ? ip2long($ip) : $ip;
        }

        $ret = array();
        foreach ($ips as $ip) {
            $ret[] = $useInt ? ip2long($ip) : $ip;
        }
        return $ret;
    }
    
    public static function getCurrentQuery() {
        $current = Request::getCurrentUrl();
        $s = parse_url($current);
        return $s['query'];
    }

    public static function getPOST($key, $default = false, $enableHtml = false) {
        if (array_key_exists($key, $_POST)) {
            return !$enableHtml ? strip_tags($_POST[$key]) : $_POST[$key];
        }
        return $default;
    }

    public static function getGET($key, $default = false, $enableHtml = false) {
        if (array_key_exists($key, $_GET)) {
            return !$enableHtml ? strip_tags($_GET[$key]) : $_GET[$key];
        }
        return $default;
    }

    public static function getREQUEST($key, $default = false, $enableHtml = false) {
        if (array_key_exists($key, $_REQUEST)) {
            return !$enableHtml ? strip_tags($_REQUEST[$key]) : $_REQUEST[$key];
        }
        return $default;
    }


    public static function getCOOKIE($key, $default = false, $enableHtml = false) {
        if (isset ($_COOKIE[$key])) {
            return !$enableHtml ? strip_tags($_COOKIE[$key]) : $_COOKIE[$key];
        }
        return $default;
    }
    
    public static function socketPostJson($url, $post_string, $connectTimeout=3, $readTimeout=3) {
        $urlInfo = parse_url($url);
        $urlInfo["path"] = ($urlInfo["path"] == "" ? "/" : $urlInfo["path"]);
        $urlInfo["port"] = (!isset($urlInfo["port"]) ? 80 : $urlInfo["port"]);
        $hostIp = gethostbyname($urlInfo["host"]);
    
        $urlInfo["request"] =  $urlInfo["path"]    .
        (empty($urlInfo["query"]) ? "" : "?" . $urlInfo["query"]) .
        (empty($urlInfo["fragment"]) ? "" : "#" . $urlInfo["fragment"]);
    
        $fsock = fsockopen($hostIp, $urlInfo["port"], $errno, $errstr, $connectTimeout);
        if (false == $fsock) {
            throw new Exception('open socket failed!');
        }
        /* begin send data */
        $in = "POST " . $urlInfo["request"] . " HTTP/1.0\r\n";
        $in .= "Accept: */*\r\n";
        $in .= "User-Agent: ganji.com API PHP5 Client 1.0 (non-curl)\r\n";
        $in .= "Host: " . $urlInfo["host"] . "\r\n";
        $in .= "Content-type: application/json\r\n";
        $in .= "Content-Length: " . strlen($post_string) . "\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $in .= $post_string . "\r\n\r\n";
    
        stream_set_timeout($fsock, $readTimeout);
        if (!fwrite($fsock, $in, strlen($in))) {
            fclose($fsock);
            throw new Exception('fclose socket failed!');
        }
        unset($in);
    
        //process response
        $out = "";
        while ($buff = fgets($fsock, 2048)) {
            $out .= $buff;
        }
        //finish socket
        fclose($fsock);
        $pos = strpos($out, "\r\n\r\n");
        $head = substr($out, 0, $pos);        //http head
        $status = substr($head, 0, strpos($head, "\r\n"));        //http status line
        $body = substr($out, $pos + 4, strlen($out) - ($pos + 4));        //page body
        if (preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)) {
            if (intval($matches[1]) / 100 == 2) {//return http get body
                return $body;
            } else {
                throw new Exception('http status not ok:' . $matches[1]);
            }
        } else {
            throw new Exception('http status invalid:' . $status . "\nOUT: " . var_export($out, true));
        }
    }

    //远程请求
    public static function RequestUrl($url, $data = array(), $request = 0) {
        try {
            $ch = curl_init ();

            curl_setopt ($ch, CURLOPT_URL, $url);            //定义表单提交地址
            curl_setopt ($ch, CURLOPT_POST, $request);       //定义提交类型 1：POST ；0：GET
            curl_setopt ($ch, CURLOPT_HEADER, 0);            //定义是否显示状态头 1：显示 ； 0：不显示
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);    //定义是否直接输出返回流
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);    //定义提交的数据

            $ret = curl_exec ($ch);
            curl_close ($ch);
        } catch (Exception $e) {

        }

        return $ret;
    }
}