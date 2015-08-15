<?PHP
/**
 * @Copyright (c) 2011 Ganji Inc.
 * @file          /code_base2/util/cache/ApcCache.class.php
 * @author        longweiguo@ganji.com
 * @date          2011-03-15
 *
 * APC缓存应用
 */


/**
 * @class: CacheApcAdapter
 * @PURPOSE:  APC缓存应用
 */
class ApcCacheAdapter {

	/**
	 * 写缓存
	 * @param  string $key      缓存的key
	 * @param  string $value    缓存的value
	 * @param  int    $duration 缓存时间，以秒为单位
	 * @return bool   如果成功缓存则返回true，否则返回false.
	 */
	public function write($key, $value, $duration = 0) {
		if (!function_exists('apc_store')) return false;
		$key  = CacheNamespace::encodeKey($key);
        return apc_store($key, $value, $duration);
	}

	/**
	 * 读缓存
	 * @param  string $key 缓存的key
	 * @return mixed  返回key对应的value，如果结果不存在、过期或者在获取的过程中发生错误，将会返回false.
	 */
	public function read($key) {
        if (!function_exists('apc_exists')) return false;
        $key  = CacheNamespace::encodeKey($key);
        //if (apc_exists($key)) {
            return apc_fetch($key);
        //}
        return null;
	}

	/**
	 * 删除缓存
	 * @param  string $key 缓存的key
	 * @return bool   如果成功删除,返回true;如果key对应的value不存在或者不能删除，则返回false.
	 */
	public function delete($key) {
		if (!function_exists('apc_delete')) return false;
		$key  = CacheNamespace::encodeKey($key);
        return apc_delete($key);
	}
}

