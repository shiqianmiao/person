<?php
/**
 * @desc 静态文件调用
 * @copyright (c) 2011 273 Inc
 * @author miaosq
 * @since 2013-06-13
 */

require_once FRAMEWORK_PATH . '/util/cache/CacheNamespace.class.php';
require_once CONF_PATH . '/cache/MemcacheConfig.class.php';

class StaticImpl {

    private static $servers = array('sta.miaosq.com');

    private static $configInfo = array();

    private static $configPath = '/var/www/garage/config.json';

    private static $memcacheKey = 'STATIC_CONFIG';

    public static function getStaticFiles($params = array()) {

        $files = $params['files'];

        if (empty($params) || empty($files)) {
            return '';
        }

        if (is_string($files)) {
            $files = preg_split('/[,\s]+/', trim($files));
        }

        if (empty($files)) {
            return '';
        }

        if (empty(self::$configInfo)) {
            self::$configInfo = self::getConfigInfo();
        }

        $str = '';

        foreach ($files as $file) {
            $file = ltrim($file, '/');
            $str .= self::getElement($file) . "\n";
        }

        return $str;
    }

    private static function getConfigInfo() {

        $configInfo = array();

        $memcache = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE, MemcacheConfig::$GROUP_DEFAULT);

        $value = $memcache->read(self::$memcacheKey);


        if ($value) {

            $configInfo = json_decode($value, true);

        } else if (@file_exists(self::$configPath)) {

            $value = file_get_contents(self::$configPath);
            $memcache->write(self::$memcacheKey, $value, 86400);
            $configInfo = json_decode($value, true);
        }

        return $configInfo;
    }

    private static function getElement($file) {

        $tag = '';
        $file = self::mapToAlias($file);

        if (preg_match('/\.(js|css)$/i', $file, $match)) {

            if (strtolower($match[1]) == 'js') {
                $tag = sprintf('<script src="%s" type="text/javascript"></script>', self::getUrl($file));
            } else {
                $tag = sprintf('<link href="%s" rel="stylesheet" type="text/css">', self::getUrl($file));
            }
        }
        return $tag;
    }

    private static function mapToAlias($file) {

        //$aliasInfo = self::$configInfo['alias'];
        $aliasInfo = array();

        if (!$aliasInfo) {
            $aliasInfo = array();
        }

        foreach($aliasInfo as $key => $value) {

            if ($key == $file) {
                $file = $value;
            }
        }

        return $file;
    }

    private static function getUrl($file) {

        if (!empty(self::$configInfo['version'][$file])) {
            $version = self::$configInfo['version'][$file];
        } else {
            $time = time();
            $expire = empty(self::$configInfo['cacheExpire']) ? 604800 : self::$configInfo['cacheExpire'];
            $version = $time - $time % $expire;
        }

        $url = preg_replace('/\.(js|css)$/i', '.__' . $version . '__.$1', $file);

        if (strpos($url, 'http:') === false) {
            $server = self::$servers[rand(0, count(self::$servers) - 1)];
            $url = 'http://' . $server . '/' . $url;
        }

        return $url;
    }
}
