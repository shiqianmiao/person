<?PHP
/**
 * @brief memcache操作对象, 此class是对Memcached对象的包装
 * @author               liubj
 * @copyright            Copyright (c) 2013, 273.cn
 */

class MemCacheAdapter
{
    private $connection = null;
    private static $instances = array();


    /**
     * @brief 构造函数
     *
     * @returns
     */
    public function __construct($servers)
    {
        if (!is_array($servers) || count($servers) == 0 || empty($servers[0]['host']) || empty($servers[0]['port'])) {
            die('MemCache::未指定$servers');
        }

        $keys = array();
        $serversToAdd = array();
        foreach ($servers as $server) {
            $keys[] = $server['host'] . '_' . $server['port'];
            $serversToAdd[] = array($server['host'], $server['port'], $server['weight']);
        }
        $key = join('|', $keys);

        if (isset(self::$instances[$key]) && self::$instances[$key]) {
            $this->connection = self::$instances[$key];
        } else {
            try {
                $this->connection = new Memcached();
                $this->connection->addServers($serversToAdd);
                $this->connection->setOption(Memcached::OPT_COMPRESSION, 0);
                $this->connection->setOption(Memcached::OPT_CONNECT_TIMEOUT, 50);
                $this->connection->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);
                self::$instances[$key] = $this->connection;
            } catch (Exception $e) {
                // @todo 加入报警
                $this->connection = null;
            }
        }
    }

    /**
     * @brief 写缓存
     *
     * @param $key string 缓存的key
     * @param $value mixed 缓存的值
     * @param $duration int 缓存的时间，以秒为单位
     *
     * @returns  bool  如果成功缓存则返回true，否则返回false.
     */
    public function write($key, $value, $duration = 3600)
    {
        if (!$this->connection) return false;
        $key  = CacheNamespace::encodeKey($key);
        return $this->connection->set($key, $value, time() + $duration);
    }


    /**
     * @brief 一次设置多个items
     *
     * @param $items array key=>value 形式存储到cache中
     * @param $duration 过期时间秒
     *
     * @see Memcached::setMulti PHP手册
     * @returns mixed
     */
    public function writeMulti(array $items, $duration = 3600)
    {
        if (!$this->connection) return false;
        $data = array();
        foreach ($items as $key => $item) {
           $key = CacheNamespace::encodeKey($key);
           $data[$key] = $item;
        }
        return $this->connection->setMulti($data, time() + $duration);
    }

    /**
     * @brief 读缓存
     *
     * @param $key string 缓存的key
     *
     * @returns  mixed 返回key对应的value，如果结果不存在、过期或者在获取的过程中发生错误，将会返回false.
     */
    public function read($key)
    {
        if (!$this->connection) return NULL;
        $key    = CacheNamespace::encodeKey($key);
        $ret     = $this->connection->get($key);
        if (!is_array($ret)) {
            $unret = unserialize($ret);
            $ret = !empty($unret) ? $unret : $ret;
        }
        if( $ret === false )
            $this->miss();
        return $ret;
    }

    /**
     * @brief 壳函数，用于缓存命中率统计
     * @author caifeng
     */
    protected function miss()
    {
        //xhprof通过统计miss函数的调用次数，可以了解缓存的命中信息
    }

    /**
     * @brief 一次读取多个items
     *
     * @param $keys array
     * @code
     * 例子:
     *    $items = array(
     *      'key1' => 'value1',
     *      'key2' => 'value2',
     *      'key3' => 'value3'
     *    );
     *    $result = $m->readMulti(array('key1', 'key3', 'badkey'));
     *    var_dump($result, $cas);
     * 结果:
     *    array(2) {
     *          ["key1"]=>
     *              string(6) "value1"
     *          ["key3"]=>
     *              string(6) "value3"
     *        }
     * @endcode
     *
     * @returns  mixed 返回一个发现key=>value的数组，失败返回false
     *
     * @see Memcached::getMulti
     */
    public function readMulti(array $keys)
    {
        if (!$this->connection) return NULL;
        $cas = 0;
        foreach ($keys as $key => $item) {
            $keys[$key] = CacheNamespace::encodeKey($keys[$key]);
        }
        return $this->connection->getMulti($keys, $cas, Memcached::GET_PRESERVE_ORDER);
    }

    /**
     * @brief 删除缓存
     *
     * @param $key string 要删除的key
     *
     * @returns 如果成功删除,返回true;如果key对应的value不存在或者不能删除，则返回false.
     */
    public function delete($key)
    {
        if (!$this->connection) return false;
        $key    = CacheNamespace::encodeKey($key);
        return $this->connection->delete($key);
    }

    /**
     * @brief 将现有的items的时间，设置为过期,从而使items失效
     * @codeCoverageIgnore
     * @returns boolean
     */
    public function clear()
    {
        if (!$this->connection) return false;
        return $this->connection->flush();
    }

    /**
     * @brief __destruct
     *
     */
    public function __destruct()
    {
        if($this->connection)
            unset($this->connection);
    }

    /**
     * @brief 对值进行加法操作
     *
     * @param $key string 要进行加法操作的key
     * @param $value int 要加的值 如果不传这个参说默认+1
     *
     * @returns 返回key的value key不存在返回false
     * @see Memcached::increment
     */
    public function increment($key, $value = null)
    {
        if (!$this->connection) return false;
        $key    = CacheNamespace::encodeKey($key);
        if ( $value == null )
        {
            return $this->connection->increment($key);
        }
        return $this->connection->increment($key, $value);
    }

    /**
     * @brief 对值进行减法操作
     *
     * @param $key string 要进行减法操作的key
     * @param $value int 要加的值 如果不传这个参说默认-1
     *
     * @returns  返回key的value key不存在返回false
     */
    public function decrement($key, $value = null)
    {
        if (!$this->connection) return false;
        $key    = CacheNamespace::encodeKey($key);
        if ( $value == null )
        {
            return $this->connection->decrement($key);
        }
        return $this->connection->decrement($key, $value);
    }
}

if (!class_exists('Memcached') && defined('DEBUG_STATUS') && DEBUG_STATUS === true) {
    /**
     * 以下代码虚似Memcached扩展 只是为了在window下可以开发调试，只在调试状态下有效
     * 完整测试请依然在Liunx上测
     */
    class Memcached {

        const OPT_COMPRESSION = 1;
        const OPT_CONNECT_TIMEOUT = 2;
        const OPT_HASH = 3;
        const HASH_CRC = 4;

        private $connection = null;

        public function __construct() {
            $this->connection = new Memcache;
        }

        public function addServers($servers) {
            foreach ($servers as $server) {
                $this->connection->addserver($server[0], $server[1], true, $server[2]);
            }
        }

        public function setOption($name) {
            // null
            return $name;
        }

        public function set($key, $value, $duration) {
            return $this->connection->set($key, $value, 0, $duration);
        }

        public function get($key) {
            return $this->connection->get($key);
        }

        public function setMulti($item, $duration) {
            foreach ($item as $key => $value) {
                $this->set($key, $value, $duration);
            }
            return true;
        }

        public function getMulti($keys) {
            $results = array();
            foreach ($keys as $key) {
                $results[$key] = $this->get($key);
            }
            return $results;
        }

        public function delete($key) {
            return $this->connection->delete($key);
        }

        public function flush() {
            return $this->connection->flush();
        }

        public function increment($key, $value) {
            return $this->connection->increment($key, $value);
        }

        public function decrement($key, $value) {
            return $this->connection->decrement($key, $value);
        }

    }

}