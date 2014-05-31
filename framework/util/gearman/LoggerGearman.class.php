<?php
/**
 * 基于gearman的日志收集
 * 其它日志收集服务，请改写_log
 *
 * @author    林云开 <cloudop@gmail.com>
 * @since     2013-2-28
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 */
include_once('ClientGearman.class.php');
class LoggerGearman{

    private static $gearmanDown = false;
    
    /**
     * 
     * @param mixed data  日志内容 数组，对象，布尔，字符串等
     * @param string identity 自定义标识
     */
    public static function logWarn($params){

        //兼容
        if (is_string($params)) {
        	$data['data'] = $params;
        } else {
            $data = $params;
        }
        self::_log('<font color="#ffff33">WARN</font>', $data);
    }

    /**
     * 
     * @param mixed data 日志内容 数组，对象，布尔，字符串等
     * @param string identity 自定义标识
     */
    public static function logDebug($params){
        
        if (is_string($params)) {
        	$data['data'] = $params;
        } else {
            $data = $params;
        }
        self::_log('<font color="#996600">DEBUG</font>', $data);
    }

    /**
     * 
     * @param mixed data  日志内容 数组，对象，布尔，字符串等
     * @param string $identity 自定义标识
     */
    public static function logInfo($params){
        
        if (is_string($params)) {
        	$data['data'] = $params;
        } else {
            $data = $params;
        }
        self::_log('<font color="#66ff00">INFO</font>', $data);
    }

    /**
     * 
     * @param mixed data  日志内容 数组，对象，布尔，字符串等
     * @param string $identity 自定义标识
     */
    public static function logFatal($params){
        
        if (is_string($params)) {
        	$data['data'] = $params;
        } else {
            $data = $params;
        }
        self::_log('<font color="#cc0000">FATAL</font>', $data);
    }

    /**
     * 以一定几率记录日志
     *
     * @param mixed data  日志内容 数组，对象，布尔，字符串等
     * @param string identity 自定义标识
     * @param float prob 记录日志的几率 比如0.01就是百分之一的几率
     */
    public static function logProb($params) {

        $prob = isset($params['prob']) ? floatval($params['prob']) : 0.001;
        $max = 10000;
        $line = intval($prob * $max);
        $r = rand(0, $max);
        if ($r <= $line) {

            self::_log('<font color="#ffff33">PROB</font>', $params);
        }
    }

    public static function logExecutTime($tag) {

        static $a;
        $trace = debug_backtrace();
        foreach ($trace as $key => $value) {

            if (strpos($value['file'], 'Dispatch.class.php') !== false) {

                break;
            }
        }
        if (isset($a[$tag])) {

            $endBrief = $trace[$key-1]['file'] . ' line:' . $trace[$key-1]['line'];
            $data = '<br>' . $a[$tag]['brief'] . ' to ' . $endBrief . ' <br>total' . (microtime(true) - $a[$tag]['t']);
            self::_log('<font color="#ffff33">PERFORMENCE</font>', array('data' => $data, 'identity' => $tag));
        }

        $a[$tag] = array(
            't' => microtime(true),
            'brief' => $trace[$key-1]['file'] . ' line:' . $trace[$key-1]['line']
        );
    }
    
    private static function _log($level, $params){

        if (is_array($params['data']) || is_object($params['data'])) {

        	$params['data'] = var_export($params['data'], true);
        } elseif (is_bool($params['data'])) {

            $params['data'] = $params['data'] ? 'true' : 'false';
        }

        $trace = debug_backtrace();
        foreach ($trace as $key => $value) {

            if (strpos($value['file'], 'Dispatch.class.php') !== false) {

                break;
            }
        }
        $current = date('Y-m-d H:i:s', time());
        $data = <<<eot
[{$current}] {$level} {$params['data']} 
<font color="#ff33ff">{$trace[$key-1]['file']}</font> 
line:<font color="#99ccff">{$trace[$key-1]['line']}</font> 
[class]:<font color="#990000">{$trace[$key]['class']}</font> 
[function]:<font color="#0033ff">{$trace[$key]['function']}</font>
eot;
        $params['data'] = $data;
        if(self::$gearmanDown === false){
            
            try{

                $gearman = ClientGearman::instance();
                $gearman->doBackground('logCollector', $params);
                self::_localToServer();
            //存储在本地，待连接上之后上传
            }catch (Exception $e){
        
                $localData = <<<eot
[{$current}] FATAL can't connect to gearman server \n
eot;
                //php会生成gb2312的文件？
                error_log($data. "\n", 3, DATA_PATH. '/PHP_LOCAL_LOG');
                error_log($e->getMessage(), 3, DATA_PATH. '/GEARMAN_FATAL' . date('Y-m-d'));
                self::$gearmanDown = true;
            }
        }else{

            $localData = <<<eot
[{$current}] FATAL already can't connect to gearman server \n
eot;
            error_log($localData, 3, DATA_PATH. '/GEARMAN_LOCAL_LOG');
            error_log($data. "\n", 3, DATA_PATH. '/PHP_LOCAL_LOG');            
        }
    }
    
    private static function _localToServer(){
        
    }
}