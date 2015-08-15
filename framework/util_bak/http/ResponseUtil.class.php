<?php

class ResponseUtil {
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

    private static $_headers       = array();
    private static $_charset       = 'UTF-8';
    private static $_contentType   = self::CONTENT_TYPE_HTML;
    private static $_jsonpCallback = '';

    public static function setContentType($contentType, $jsonpCallback='') {
    	if ($contentType == self::CONTENT_TYPE_JSONP && empty($jsonpCallback)) {
    		$contentType = self::CONTENT_TYPE_JSON;
    	}
        self::$_contentType = $contentType;
        self::$_jsonpCallback = $jsonpCallback;
        if (array_key_exists(self::$_contentType, self::$_mimeTypes)) {
        	self::$_headers['Content-Type'] = self::$_mimeTypes[self::$_contentType] . '; charset=' . self::$_charset;
        }
    }

    public static function setCharset($charset) {
        self::$_charset = $charset;
    }

    public static function output($content, $contentType='', $jsonpCallback='') {
        if (!empty($contentType)) {
            self::setContentType($contentType, $jsonpCallback);
        }

        foreach (self::$_headers as $key => $value) {
            header($key . ": " . $value);
        }

        if (!isset(self::$_headers['Content-Type'])) {
            header('Content-Type: ' . self::$_mimeTypes[self::$_contentType] . '; charset=' . self::$_charset);
        }
        
    	switch (self::$_contentType) {
            case self::CONTENT_TYPE_HTML:
            case self::CONTENT_TYPE_JS:
                echo $content;
                break;
            case self::CONTENT_TYPE_JSON:
                echo json_encode($content);
                break;
            case self::CONTENT_TYPE_JSONP:
                echo self::$_jsonpCallback . '(' . json_encode($content) . ');';
                break;
            default:
            	echo $content;
            	break;
        }
    }

    /// 302跳转
    /// @param $url
    public static function redirect($url, $isIframe=false) {
        self::_redirect($url, 302, $isIframe);
    }

    public static function parentRedirect($url) {
        echo '<html><head><title>redirect</title></head><body>
                <script src="http://sta.ganji.com/cgi/ganji_sta.php?file=ganji" type="text/javascript"></script>
                <script type="text/javascript">
                var win = window.parent || window.top || window.opener;
                win.location.href = "' . $url . '";
                </script>
              </body></html>';
    }

    private static function _redirect($url, $code=302, $isIframe=false) {
        if (!$isIframe) {
            header("HTTP/1.1 {$code} Moved Temporarily");
            header('Location: ' . $url);
        } else {
            echo '<html><head><title>redirect</title></head><body>
                    <script src="http://sta.ganji.com/cgi/ganji_sta.php?file=ganji" type="text/javascript"></script>
                    <script type="text/javascript">
                    GJ.use("talk_to_parent", function(){
                        window.location.href = "' . $url . '";
                    });
                    </script>
                  </body></html>';
        }
        exit;
    }

    /// 301跳转
    /// @param $url
    public static function redirectPermently($url, $isIframe = false) {
        self::_redirect($url, 301, $isIframe);
    }

    /// 跳转到页面本身
    public static function redirectToSelf($isIframe = false) {
        $url = $_SERVER['REQUEST_URI'];
        self::_redirect ($url, 302, $isIframe);
    }
}