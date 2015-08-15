<?php
/**
 * 基于Redis的简单队列
 *
 * @author    戴志君 <daizj@273.cn>
 * @since     2013-12-12
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc      
 */
require_once FRAMEWORK_PATH . '/util/redis/RedisClient.class.php';
require_once CONF_PATH . '/cache/RedisConfig.class.php';
class RedisQueue {
    
    private static $_REDIS = NULL;
    
    private static function _redis() {
        if (self::$_REDIS === NULL) {
            $redisClient = new RedisClient(RedisConfig::$REDIS_QUEUE);
            self::$_REDIS = $redisClient->getMasterRedis();
        }
        return self::$_REDIS;
    }
    /**
     * 入队列
     * @param type $key
     * @param type $val
     */
    public static function push($key,$val) {return true;
        $redis = self::_redis();
        return $redis->lpush($key,$val);
    }
    
    /**
     * 出队列
     * @param type $key
     */
    public static function pop($key) {
        $redis = self::_redis();
        return $redis->rPop($key);
    }
    
    /**
     * 删除队列
     * @param type $key
     */
    public static function delete($key) {
        $redis = self::_redis();
        return $redis->delete($key);
    }
    
    /**
     * 队列大小
     * @param type $key
     */
    public static function size($key) {
        $redis = self::_redis();
        return $redis->lSize($key);
    }

	/***
	 * 获取队列n个值
	 * @param type $key
	 * @param int $start
	 * @param int $offset
	 * **/
	public static function listRange( $key, $offset, $start=0 ) {
        $redis = self::_redis();
		return $redis->lRange($key, $start, $offset );
	}


}

