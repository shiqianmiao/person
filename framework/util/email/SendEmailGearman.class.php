<?php
class SendEmailGearman {
    private static $handle;

    public static function sendEmail($params) {
        $data = Util::getFromArray('data', $params, '');
        $tag      = Util::getFromArray('tag', $params, '');
        $level    = Util::getFromArray('level', $params, '');
        if (empty($data) || empty($tag) || empty($level)) {
            throw new Exception('记录日志时需要传递describe和tag参数');
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
        $logParams['data']    = json_encode([$data]);
        $logParams['trace']      = json_encode($trace[$key - 1]);
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
            include_once(dirname(__FILE__) . '/../gearman/EmailGearman.class.php');
            self::$handle = EmailGearman::getInstance();
        } else {
            include_once(dirname(__FILE__) . '/../email/SendEmail');
            self::$handle = new LogController();
        }
    }
}