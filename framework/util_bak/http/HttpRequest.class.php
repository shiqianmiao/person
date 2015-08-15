<?php
/**
 * @brief 封装发起Http请求的逻辑
 *
 */
class HttpRequest
{
    const CONNECT_TIMEOUT = 10;
    const READ_TIMEOUT = 10;
    
    /**
     * @brief 默认的 curl opts
     * @var array
     */
    public static $DEFAULT_CURLOPTS = array(
        CURLOPT_CONNECTTIMEOUT    => 3,
        CURLOPT_TIMEOUT           => 5,
        CURLOPT_USERAGENT         => 'ganji-php-1.0',
        CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_HEADER            => false,
        CURLOPT_FOLLOWLOCATION    => false,
    );

    /**
     * 带连接超时和重试机制的HTTP GET请求
     * @param string $url URL(必须HTTP打头)
     * @param int $connectTimeout 连接超时(毫秒)
     * @param int $readTimeout 读取超时(毫秒)
     * @param int $retry 重试次数
     */
    public static function socketGet($url, $connectTimeout, $readTimeout = 0, $retry = 0)
    {
        $urlInfo = parse_url($url);
        $urlInfo['path'] = (empty($urlInfo['path'])) ? '/' : $urlInfo['path'];
        $urlInfo['port'] = (!isset($urlInfo['port'])) ? 80 : $urlInfo['port'];
        $urlInfo['query'] = (empty($urlInfo['query'])) ? '' : '?' . $urlInfo['query'];
        $urlInfo['fragment'] = (empty($urlInfo['fragment']) ? '' : '#' . $urlInfo['fragment']);
        $requestUrl = $urlInfo['path'] . $urlInfo['query'] . $urlInfo['fragment'];

        $fp = @fsockopen($urlInfo['host'], $urlInfo['port'], $errno, $errstr, $connectTimeout / 1000.0);
        if (!$fp) {
            if (($retry - 1) >= 0) {
                self::socketGet($url, $connectTimeout, $readTimeout, $retry - 1);
            } else {
                return false;
            }
        } else {
            $in  = "GET $requestUrl HTTP/1.0\r\n";
            $in .= "Host: {$urlInfo['host']}\r\n";
            $in .= "Connection: Close\r\n\r\n";
            fwrite($fp, $in);
            if ($readTimeout != 0) {
                //设置读取超时
                stream_set_timeout($fp, 0, $readTimeout * 1);
            }
            $out = '';
            while (!feof($fp)) {
                $out .= fgets($fp, 512);
            }
            fclose($fp);
            list($head, $body) = explode("\r\n\r\n", $out);
            return $body;
        }
    }


    public static function socketPost($url, $post_string, $connectTimeout=10, $readTimeout=10)
    {
//        $connectTimeout = self::CONNECT_TIMEOUT;
//        $readTimeout = self::READ_TIMEOUT;

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
        $in .= "Content-type: application/x-www-form-urlencoded\r\n";
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

    public static function socketPostJson($url, $post_string, $connectTimeout=3, $readTimeout=3)
    {
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



    /**
     * get请求，对file_get_contents的封装，可控制超时
     * @param string $url
     * @param int $timeout 超时，默认两秒
     * @return string
     */
    public static function get($url , $timeout = 2) {
        $stream_setting    = stream_context_create(
            array(
                'http'  => array(
                    'timeout'   => $timeout,
                ),
            )
        );
        return @file_get_contents($url , 0 , $stream_setting);
    }
    
    /**
     * post请求
     * @param string $url
     * @param array $params
     * @param int $timeout
     */
    public static function post($url, $params, $timeout = 2) {
        try {
            foreach ($params as $key => $val) {
                $post[] = "{$key}={$val}";
            }
            $post_data  = implode('&', $post);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            return curl_exec($ch);
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * post请求
     * @param string $url
     * @param array $params
     * @param int $timeout
     * @throw exception 链接失败抛出异常
     */
    public static function curlPost($url, $params, $timeout = 2) {
        $timeout = (int) $timeout;
        try {
            return self::execCurlHttpRequest($url, $params, 'POST', array(CURLOPT_TIMEOUT => $timeout));
        } catch (Exception $e) {
//            // 可以记录log
//            return false;
            throw $e;
        }
    }

    /**
     * @brief 执行一个HTTP POST请求
     * @param $url
     * @param array $params
     * @param string HTTP 请求类型 POST, GET
     * @param array $curlOpts curl 参数配置, 允许为空
     * @return mix
     */
    public static function execCurlHttpRequest($url, $param = array(), $httpMethod = 'POST', $curlOpts = null) {
        if (!is_array($curlOpts) || !$curlOpts) {
            $curlOpts = self::$DEFAULT_CURLOPTS;
        } else {
            $curlOpts = (array)$curlOpts + (array) self::$DEFAULT_CURLOPTS;
        }

        $_useHttps = false;
        if (stripos($url, 'https://') === 0) {
            $_useHttps = true;
        }
        $ch = curl_init();

        if ($_useHttps) {
            $curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
        }

        if ($httpMethod == 'POST') {
            $curlOpts[CURLOPT_URL] = $url;
            $curlOpts[CURLOPT_POSTFIELDS] = $param;
        } else {
            $postData = http_build_query($param, '', '&');
            $delimiter = strpos($url, '?') === false ? '?' : '&';
            $curlOpts[CURLOPT_URL] = $url . $delimiter . $postData;
            $curlOpts[CURLOPT_POST] = false;
        }

        curl_setopt_array($ch, $curlOpts);
        $result = curl_exec($ch);

        if ($result === false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            //throw new Exception(sprintf('curl_errno=%s, curl_error=%s', $errno, $error));
        } elseif (empty($result)) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode != 200) {
                curl_close($ch);
                //throw new Exception(sprintf('http response status code: %s', $httpCode));
            }
        }

        curl_close($ch);
        return $result;
    }

}
