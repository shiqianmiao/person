<?PHP
/**
 * @brief 构造cache对象
 * @package              ganji.cache
 * @author               yangyu
 * @file                 $RCSfile: Curl.class.php,v $
 * @version              $Revision: 50890 $
 * @modifiedby           $Author: yangyu $
 * @lastmodified         $Date: 2011-03-11 17:29:21 +0800 (五, 2011-03-11) $
 * @copyright            Copyright (c) 2011, ganji.com
 */

require_once CONF_PATH . '/cache/MemcacheConfig.class.php';

class CacheNamespace
{
    /**
     * @brief 脚本进程内的cache
     * @var array
     */
    private static $_CACHE_DATA = array();
    /**
     * @brief 没有结果缓存标记
     * @var string
     */
    const NO_RESULT_CACHE_FLAG = 'dict_nores_v1';
    
    /** APC */
	const MODE_APC = 1;

	/** memcache */
	const MODE_MEMCACHE = 2;

    private static $_HANDLE_ARRAY   = array();

    private static function _getHandleKey($params) {
        ksort($params);
        return md5(implode('_' , $params));
    }

    /**
     * @brief 创建一个cache 对象
     *
     * @param $mode int cache的类型
     *
     * @see CacheBackingStore::MEMCACHE         memcache
     * @see CacheBackingStore::LOCAL_FILE       local_file
     * @see CacheMemcache
     * @see CacheFile
     *
     * @return  MemCacheAdapter|ApcCacheAdapter
     */
	public static function createCache($mode, $servers=array()) {

        $handle_key = self::_getHandleKey(array(
            'mode'      => $mode,
            'servers'   => serialize($servers),
        ));

        if (isset(self::$_HANDLE_ARRAY[$handle_key])) {
            return self::$_HANDLE_ARRAY[$handle_key];
        }

		switch($mode) {
			case self::MODE_MEMCACHE:
				require_once dirname(__FILE__) . '/adapter/MemCacheAdapter.class.php';
				$objCache = new MemCacheAdapter($servers);
				break;
			case self::MODE_APC:
			    require_once dirname(__FILE__) . '/adapter/ApcCacheAdapter.class.php';
				$objCache = new ApcCacheAdapter();
				break;
			default:
			    require_once dirname(__FILE__) . '/adapter/MemCacheAdapter.class.php';
				$objCache = new MemCacheAdapter($servers);
				break;
		}

		self::$_HANDLE_ARRAY[$handle_key]    = $objCache;

		return $objCache;
	}

    public static function encodeKey($key) {
        return urlencode($key);
    }
    
    public static function writeLocalCache($key, $data) {
        $apc    = CacheNamespace::createCache(CacheNamespace::MODE_APC);
        $apc->write($key, $data);
        self::_writeToFile($key, $data);
    }

    /**
     * 缓存写到文件里
     * @param string $path  存储路径，有vehicle,area,store区分
     */

    protected static function _writeToFile($key, $data) {
        if (!defined('DATA_PATH')) {
            exit('must defined const DATA_PATH for save file path');
        }
        $key = str_replace('_', '/', $key);
        $file = DATA_PATH  . '/localCache/' . $key.'.cache';
        @mkdir(substr($file, 0, strrpos($file, '/')), 0777, true);
        file_put_contents($file, serialize($data));
    }
    
    /**
     * 读取缓存
     * 从cache文件读取内容
     * @param string $path 文件路径
     * @return boolean
     */
    public static function readLocalCache($key) {
        // 若已取过此key，直接返回
        if (isset(self::$_CACHE_DATA[$key])) {
            return self::$_CACHE_DATA[$key];
        }

        // apc缓存
        $apc = CacheNamespace::createCache(CacheNamespace::MODE_APC);
        $apcKey = $key;
        $data   = $apc->read($apcKey);
        if ($data) {
            if ($data == self::NO_RESULT_CACHE_FLAG) {
                $data = null;
            } else {
                // 存入内存缓存
                self::$_CACHE_DATA[$key]    = $data;
                return $data;
            }
        }

        // memcache缓存, 用于存储所有未找到的数据的key
        $memcache = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE, MemcacheConfig::$GROUP_DEFAULT);
        $memData   = $memcache->read($key);

        // 缓存结果  == $noResultCache 直接返回
        if ($memData == self::NO_RESULT_CACHE_FLAG) {
            //return null;
        }
        // apc_cache, memcache 不存在，读取文件cache
        if (!$data) {
            // 文件缓存
            $data   = self::_getFileCache($key);

            // 数据找不到, 存入memcache, 否则存储在apc
            if (!$data) {
                $memcache->write($key, self::NO_RESULT_CACHE_FLAG, 36000);
                return null;
            } else {
                $apc->write($apcKey, $data);
            }
        }

        // 存入内存缓存
        self::$_CACHE_DATA[$key]    = $data;
        return $data;
    }
    /**
     * @brief 通过文件获取缓存
     * @param $key
     * @return mix
     *   - null
     *   - array
     */
    protected static function _getFileCache($key) {
        // 文件缓存
        $file   = self::_getCachePath($key);
        if (!file_exists($file)) {
            //self::_log(sprintf('fail, notexist key=%s, file = %s 不存在', $key, $file));
            return null;
        }

        $string = @file_get_contents($file);
        if (strlen($string) == 0) {
            //self::_log(sprintf('fail, fileempty key=%s, file = %s 文件为空', $key, $file));
            return null;
        }

        $content    = @unserialize($string);

        return $content;
    }
    /**
     * 获取文件缓存路径
     * @param string $key 缓存key
     * @return string
     */
    protected static function _getCachePath($key) {
        if (!defined('DATA_PATH')) {
            exit('must defined const DATA_PATH for save file path');
        }
        $keyParams = str_replace('_', '/', $key);
        return DATA_PATH  . '/localCache/' . $keyParams.'.cache';
    }
}

