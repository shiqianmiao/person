<?php
/**
 * @通过微信服务器给用户发送模板信息
 */
class Message {

    private static $_handle;

    private static function _send($data) {
        self::_getInstance();
        self::$_handle->sendMessage($data);
    }


    /**
     * @brief 如果安装gearman扩展，使用gearman，否则直接写入数据库中
     */
    private static function _getInstance() {
        if (extension_loaded('gearman')) {
            include_once(dirname(__FILE__) . '/../gearman/MessageGearman.class.php');
            self::$_handle = MessageGearman::getInstance();
        } else {
            include_once(dirname(__FILE__) . '/WxMessage.class.php');
            self::$_handle = new WxMessage();
        }
    }

    public static function sendError($params) {
        $toUser = Util::getFromArray('touser', $params, '');
        $sysName = Util::getFromArray('system_name', $params, '安心客');
        $createTime = Util::getFromArray('create_time', $params, date('Y-m-d H:i:s'));
        $level = Util::getFromArray('level', $params, '高');
        $data = Util::getFromArray('data', $params, '');
        if(empty($toUser) || empty($data)) {
            throw new Exception('发送微信模板消息,参数缺失!');
        }
        $data   = [
            "touser"      => $toUser,
            "template_id" => "og6awnEReSZ6at5rNcCRIszbG3wWkadiVJSwtD7LUH8",
            "url"         => "",
            "topcolor"    => "#FF0000",
            "data"        => [
                "first"            => [
                    "value" => "你好,请注意查收系统报警信息",
                    "color" => "#173177"
                ],
                "keyword1"    => [
                    "value" => $sysName,
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => $createTime,
                    "color" => "#173177"
                ],
                "keyword3" => [
                    "value" => $level,
                    "color" => "#173177"
                ],
                "remark"           => [
                    "value" => $data,
                    "color" => "#173177"
                ]
            ],
        ];
        self::_send($data);
    }
}