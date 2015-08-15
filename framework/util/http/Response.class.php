<?php

class Response {
    const CONTENT_TYPE_HTML  = 'html';
    const CONTENT_TYPE_TEXT  = 'txt';
    const CONTENT_TYPE_CSS   = 'css';
    const CONTENT_TYPE_JS    = 'js';
    const CONTENT_TYPE_JSON  = 'json';
    const CONTENT_TYPE_JSONP = 'jsonp';
    const CONTENT_TYPE_XML   = 'xml';
    const CONTENT_TYPE_RSS   = 'rss';
    const CONTENT_TYPE_GZ    = 'gz';
    const CONTENT_TYPE_TAR   = 'tar';
    const CONTENT_TYPE_ZIP   = 'zip';
    const CONTENT_TYPE_GIF   = 'gif';
    const CONTENT_TYPE_PNG   = 'png';
    const CONTENT_TYPE_JPG   = 'jpg';
    const CONTENT_TYPE_JPEG  = 'jpeg';
    
    private static $_mimeTypes = array(
        self::CONTENT_TYPE_HTML  => 'text/html',
        self::CONTENT_TYPE_TEXT  => 'text/plain',
        self::CONTENT_TYPE_CSS   => 'text/css',
        self::CONTENT_TYPE_JS    => 'application/javascript',
        self::CONTENT_TYPE_JSON  => 'application/json',
        self::CONTENT_TYPE_JSONP => 'application/javascript',
        self::CONTENT_TYPE_XML   => 'text/xml',
        self::CONTENT_TYPE_RSS   => 'application/rss+xml',
        self::CONTENT_TYPE_GZ    => 'application/x-gzip',
        self::CONTENT_TYPE_TAR   => 'application/x-tar',
        self::CONTENT_TYPE_ZIP   => 'application/zip',
        self::CONTENT_TYPE_GIF   => 'image/gif',
        self::CONTENT_TYPE_PNG   => 'image/png',
        self::CONTENT_TYPE_JPG   => 'image/jpeg',
        self::CONTENT_TYPE_JPEG  => 'image/jpeg',
    );

    private static $_charset       = 'UTF-8';

    public static function setCharset($charset) {
        self::$_charset = $charset;
    }

    public static function header($key, $val) {
        header($key . ': ' . $val);
    }

    public static function outputJson($content) {
        echo self::_output($content, self::CONTENT_TYPE_JSON);
    }

    public static function outputJsonp($content, $jsonpCallback) {
        echo self::_output($content, self::CONTENT_TYPE_JSONP, $jsonpCallback);
    }

    /**
     * @param $content 格式化内容
     * @param string $contentType 格式化类型
     * @param array $param 格式化的参数，如jsonpCallback,node_name,delete_bom
     */
    public static function output($content, $contentType = self::CONTENT_TYPE_HTML, $param = array()) {
        if (!array_key_exists($contentType, self::$_mimeTypes)) {
            $contentType = self::CONTENT_TYPE_HTML;
        }
        echo self::_output($content, $contentType, $param);
    }

    private static function _output($content, $contentType = self::CONTENT_TYPE_HTML, $param = array()) {
        header('Content-Type: ' . self::$_mimeTypes[$contentType] . '; charset=' . self::$_charset);
        
    	switch ($contentType) {
            case self::CONTENT_TYPE_HTML:
            case self::CONTENT_TYPE_JS:
                echo $content;
                break;
            case self::CONTENT_TYPE_JSON:
                $deleteBom = isset($param['delete_bom']) && $param['delete_bom'] ? true : false;
                $str = json_encode($content);
                //消除BOM头部
                if($deleteBom && substr($str,0,3) == pack("CCC",0xEF,0xBB,0xBF)){
                    $str = substr($str,3);
                }
                echo $str;
                break;
            case self::CONTENT_TYPE_JSONP:
                $jsonpCallback = $param['jsonpCallback'];
                echo $jsonpCallback . '(' . json_encode($content) . ');';
                break;
            case self::CONTENT_TYPE_XML:
                include_once dirname(dirname(__FILE__)) . '/xml/Array2XML.class.php';
                $nodeName = $param['node_name'] ? $param['node_name'] : 'root';
                $result = Array2XML::createXML($nodeName, $content);
                $str = $result->saveXml();
                echo $str;
                break;
            default:
            	echo $content;
            	break;
        }
    }

    public static function redirect($url, $code = 302) {
        if ($code == 301) {
            header("HTTP/1.1 301 Moved Permanently");
        } else {
            header("HTTP/1.1 {$code} Moved Temporarily");
        }
        header('Location: ' . $url);
        exit;
    }
}