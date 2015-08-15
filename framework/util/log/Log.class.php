<?php

class Log {
    private static $handle;

    private static function _log($params) {
        $data  = Util::getFromArray('data', $params, '');
        $tag   = Util::getFromArray('tag', $params, '');
        $level = Util::getFromArray('level', $params, '');
        if (empty($data) || empty($tag) || empty($level)) {
            throw new Exception('记录日志时需要传递data和tag参数');
        }

        //日志路径
        $trace = debug_backtrace();
        foreach ($trace as $key => $value) {
            if (strpos($value['file'], 'Dispatch.class.php') !== false) {
                break;
            }
        }

        //构造日志参数
        $logParams['level']       = $level;
        $logParams['tag']         = $tag;
        $logParams['data']        = json_encode([$data]);
        $logParams['trace']       = json_encode($trace[$key - 1]);
        $logParams['url']         = Request::getCurrentUrl();
        $logParams['create_time'] = time();

        //记录日志
        self::_getInstance();
        self::$handle->log($logParams);
    }

    /**
     * @brief 如果安装gearman扩展，使用gearman，否则直接写入数据库中
     */
    private static function _getInstance() {
        if (extension_loaded('gearman')) {
            include_once(dirname(__FILE__) . '/../gearman/LogGearman.class.php');
            self::$handle = logGearman::getInstance();
        } else {
            include_once(dirname(__FILE__) . '/../../../rpc/business/log/controller/LogController.class.php');
            self::$handle = new LogController();
        }
    }

    public static function logInfo($params) {
        $params['level'] = 1;
        self::_log($params);
    }

    public static function logWarn($params) {
        $params['level'] = 2;
        self::_log($params);
    }

    public static function logDebug($params) {
        $params['level'] = 3;
        self::_log($params);
    }

    public static function logFatal($params) {
        $params['level'] = 4;
        self::_log($params);
    }
}