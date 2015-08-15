<?PHP
/**
 * @author    陈朝阳 <chency@273.cn>
 * @since     2014-3-17
 * @copyright Copyright (c) 2003-2014 273 Inc. (http://www.273.cn)
 * @desc      memcache类封装，增加数据有效性判断，支持自定义判空函数，空数据连续累计次数需超过计数（默认3次）才有效
 */

require_once FRAMEWORK_PATH . '/util/cache/CacheNamespace.class.php';

class SafeCacheUtil {
    
    /**
     * @desc memcache实例
     */ 
    private $_memcacheHandle = null;
    
    /**
     * @desc （可自定义）空数据最大计数，超过这个计数时，空数据开始生效
     */
    private $_maxEmptyCount = 3;
    
    /**
     * @desc （可自定义）判空函数，不传时使用empty函数，
     */
    private $_emptyFunction = null;
    
    /**
     * @desc构造函数
     */
    public function __construct($serversParam) {
        $this->_memcacheHandle = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE, $serversParam);
    }
    
    /**
     * @desc 读缓存
     * @return 缓存失效或无数据时返回  false
     */
    public function read($cacheKey) {
        $ret = $this->_memcacheHandle->read($cacheKey);
        if (!empty($ret) && !$this->_isEmpty($ret['data']) || $ret['count'] >= $this->_maxEmptyCount) {
            return $ret['data'] ;
        }
        return false;
    }
    
    /**
     * @desc 写缓存
     * 缓存结构 array(
     *          'count'      //空数据计数
     *          'data'       //缓存数据
     *          'expire_time'//过期时间
     *      )
     */
    public function write($cackeKey, $data, $cacheTime) {
        $cacheData = array(
            'count'       => 1,
            'data'        => $data,
        );
        $isEmpty = empty($this->_emptyFunction) ? empty($data) : $this->_isEmpty($data);
        if ($isEmpty) {
            //空数据计数三次才写入
            $ret = $this->_memcacheHandle->read($cackeKey);
            if (isset($ret['count']) && $ret['count'] > 0) {
                $cacheData['count'] = $ret['count'] + 1;
            }
        } else {
            $cacheData['data'] = $data;
        }
        $ret = $this->_memcacheHandle->write($cackeKey, $cacheData, $cacheTime);
    }
    
    /**
     * @desc 设置判空参数
     * @param Array $param (
     *                 'empty_func' 判断数据是否有效的函数(返回值为1或0)
     *                 'max_count'  最大空计数，超过这个数时，空缓存生效
     *              )
     * 注意：为区分call_user_func调用失败时的返回值false，'empty_func' 函数的返回值为1或0
     */
    public function setEmptyParam($param) {
        if (!empty($param['empty_func'])) {
            $this->_emptyFunction = $param['empty_func'];
        }
        if ($param['max_count'] > 0) {
            $this->_maxEmptyCount = $param['max_count'];
        }
        
    }
    
    /**
     * @desc 判断数据是否为空
     */
    private function _isEmpty($data) {
        if (!empty($this->_emptyFunction)) {
            $ret = call_user_func($this->_emptyFunction, $data);
            if ($ret == 1) {
                return true;
            } else if ($ret == 0) {
                return false;
            } else {
                exit('回调函数不存在，或返回值为false');
            }
        } else {
            return empty($data);
        }
    }
}
