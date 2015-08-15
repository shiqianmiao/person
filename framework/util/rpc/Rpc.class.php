<?php
/**
 * @brief 简介：Rpc服务类,提供封装好的各种Rpc服务调用接口
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月8日 下午4:47:10
 */

class Rpc {
    /**
     * @brief 保存已经请求了的client内容
     */
    private static $_CLIENTS = array();
    
    /**
     * 初始化一个同步请求,不设置选项就默认服务器的设置
     * @param string $url 服务接口,由服务端提供.格式如：yuesao.test
     * @param array $option 设置项，如array( array(YAR_OPT_CONNECT_TIMEOUT, 1), array(), array() )
     * @param 配置项可以是：可以是: YAR_OPT_PACKAGER, YAR_OPT_PERSISTENT (需要服务端支持keepalive), YAR_OPT_TIMEOUT, YAR_OPT_CONNECT_TIMEOUT
     * @return object yar客户端资源
     */
    public static function client($url, $option = array()) {
        $key = md5($url . json_encode($option));
        if (isset(self::$_CLIENTS[$key])) {
            return self::$_CLIENTS[$key];
        }

        $url = UrlConfig::RPC_HOST . '/' . $url;
        $client = new Yar_Client($url);
        
        if (!empty($option)) {
            foreach($option as $val) {
                $client->setOpt($val[0], $val[1]);
            }
        }
        self::$_CLIENTS[$key] = $client;
        return $client;
    }
    
    /**
     * 注册一个并行请求事件
     * @param string $server 服务器接口地址
     * @param string $func 请求函数名
     * @param array $params 带给远程请求函数的参数
     * @param array $option 可选yar配置
     * @return void
     */
    public static function call($url, $method, $params = array(), $callBack = null, $errorCallBack = null, $option = array()) {
        Yar_Concurrent_Client::call($url, $method, $params, $callBack, $errorCallBack, $option);
    }
    
    /**
     * 执行请求
     * @param string $callback 回调函数
     * @param string $errorCallback 错误回调
     * @return void
     */
    public static function loop($callback="RPC::loopCallback", $errorCallback="RPC::loopCallback") {
        $callback = $callback ? $callback : "RPC::loopCallback";
        $errorCallback = $errorCallback ? $errorCallback : "RPC::loopErrorCallback";
        Yar_Concurrent_Client::loop($callback, $errorCallback);
    }
    
    /**
     * 并行请求回调
     * @param $retval执行异步请求之后之后的返回值、
     * @param $callinfo 请求信息
     */
    public static function loopCallback($retval, $callinfo) {
        //echo " call function : ", $callinfo["method"], ". calling sequence is " , $callinfo["sequence"] , "\n";
        //self::$map[$callinfo['sequence']] = $retval;
        var_dump($retval . 'callback 输出自回调的内容');
        //print_r($callinfo);
    }
    
    /**
     * 并行请求错误回调
     */
    public static function loopErrorCallback($type, $error, $callinfo) {
        var_dump($error);
    }
    
    /**
     * @brief 拼接url
     * @param $url rpc服务端提供的方法。例如：yuesao.test 、yuesao.blog.test......
     */
    public static function getUrl($url) {
        return UrlConfig::RPC_HOST . '/' .$url;
    }
}
