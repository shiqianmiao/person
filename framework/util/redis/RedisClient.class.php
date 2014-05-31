<?php
//require_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';

class RedisClient{
    public static $redis     = array();
    public $option    = null;
    /* {{{ __construct */
    /**
     * @brief 
     *
     */
    public function __construct($option){
        if( is_array($option) ){
            $this->setOption($option);
        }
    }
    
    /**
     * @brief 获得Redis主库链接句柄
     * @details 根据key进行分库，所以对redis key的任何操作都要先通过这个方法来获得一个正确的链接
     *
     * @Param $key string 要操作的redis key
     * @throw Exception 当redis 服务器的配置不是array的时候抛出异常
     * @Returns phpredis object。
     * @code
     * 例子:
     *      ...
     *      $HousePremierRedisPool =  array(
     *          'servers'   => array(
     *              array('host' => '127.0.0.1', 'port' => 6379,'db' => 16, 'weight' => 1, 'password' => 'ganji_!q2w#','timeout' => 3)
     *           )
     *      );
     *      $redisClient = new RedisClient($HousePremierRedisPool);
     *      不同的key一定要使用这个方法获得redis对象，否则可能操作错误
     *      try{
     *          $redis = $redisClient->getMasterRedis($key)->[method]();//各种phpredis的方法
     *      }
     *      catch(Exception $e){}
     *      ...
     * @endcode
     */
    public function getMasterRedis($key = null){
        if(!is_array($this->option)){
            throw new Exception('请设定redis 服务的配置信息');
            return false;
        }
        $server_num = (null != $key) ? self::hashServer($key) : 0;
        $server_key = md5("{$this->option['servers'][$server_num]['host']}-{$this->option['servers'][$server_num]['port']}-{$this->option['servers'][$server_num]['db']}");
        try{
            if( isset(self::$redis[$server_key])
                    && self::$redis[$server_key] instanceof Redis
                    && '+PONG' == self::$redis[$server_key]->ping() )
            {
                return self::$redis[$server_key];
            }
        }
        catch(Exception $e){}
        try{
            self::$redis[$server_key] = new Redis();
            $ok = self::$redis[$server_key]->connect($this->option['servers'][$server_num]['host'],$this->option['servers'][$server_num]['port'],$this->option['servers'][$server_num]['timeout']);
            self::loginRedis($server_key,$server_num);
            return self::$redis[$server_key];
        }
        catch(Exception $e){
            $msg = $e->getMessage();
            if(false !== strpos($msg, "Can't connect") || $msg == 'read error on connection'){
                try{
                    self::$redis[$server_key]->connect($this->option['servers'][$server_num]['host'],$this->option['servers'][$server_num]['port'],$this->option['servers'][$server_num]['timeout']);
                    self::loginRedis($server_key,$server_num);
                    return self::$redis[$server_key];
                }
                catch(Exception $e){}
            }
            self::$redis[$server_key] = null;
            //LoggerGearman::logDebug("redis error,code:" . $e->getCode() . "msg:" . $e->getMessage() . "\n". $e->getTraceAsString());
        }
    }
    /* {{{ sedDb */
    /**
     * @brief 设定redis 服务器的配置信息
     *
     * @Param $option array 参考配置文件格式
     * @see $this->option
     *
     */
    public function setOption($option){
        $this->option = $option;
        //$this->close();
    }//}}}
    /* {{{ loginRedis */
    /**
     * @brief 完成密码设定和选择db过程
     *
     * @Param $server_num int   被hash的key
     *
     * @Returns  void 
     */
    private function loginRedis($server_key,$server_num){
        if(!empty($this->option['servers'][$server_num]['password']))
            self::$redis[$server_key]->auth($this->option['servers'][$server_num]['password']);
        if(isset($this->option['servers'][$server_num]['db']))
            self::$redis[$server_key]->select($this->option['servers'][$server_num]['db']);
    }//}}}
    /** {{{ 返回使用服务器的编号 hashServer($key)
      * @param string   key
      * @return int
      */
    public function hashServer($key){
        $serverTotal = count($this->option['servers']);
        $num = sprintf("%u", crc32($key) ) % $serverTotal;
        if(!isset($this->option['servers'][$num])){
           //LoggerGearman::logDebug("redis error,key=>{$key}:total=>{$serverTotal}:num=>{$num}", 'redis');
           $num =  0;
        }
        return $num;
    }//}}}
    /* {{{ close */
    /**
     * @brief 关闭资源
     *
     */
    public function close(){
        foreach(self::$redis as $k => &$r){
            if($r instanceOf Redis){
                $r->close();
                $r = null;
            }
        }
    }//}}}
}
