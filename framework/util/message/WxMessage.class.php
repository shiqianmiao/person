<?php
/**
 * @通过微信服务器给用户发送模板信息
 */
//TODO:项目中由于自动require了下面的文件,不需要重新包好,如果要跑脚本等等的,需要require_once下面的文件
define('CONF_PATH', dirname(__FILE__) . '/../../../conf/debug');
require_once dirname(__FILE__) . '/../http/Request.class.php';
require_once dirname(__FILE__) . '/../../../conf/debug/cache/MemcacheConfig.class.php';
require_once dirname(__FILE__) . '/../cache/CacheNamespace.class.php';

class WxMessage {
    /**
     * @brief 获取微信应用的accesstoken
     */
    public function getAccessToken() {
        $memHandle = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE, MemcacheConfig::$GROUP_DEFAULT);
        //从缓存中读取，如果缓存中有数据
        $memKey      = 'WX_ACCESS_TOKEN';
        $accessToken = $memHandle->read($memKey);
        if (!empty($accessToken) && empty($_GET['mem'])) {
            return $accessToken;
        }
        $appid          = 'wx433336e9ca24dfe4';
        $secret         = '3ea84423e69f6f82a24d55797ee49c77';
        $accessTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $ret            = Request::RequestUrl($accessTokenUrl);
        $ret            = json_decode($ret, true);
        //加入缓存
        if (!empty($ret['access_token'])) {
            $memHandle->write($memKey, $ret['access_token'], 3600);
        }

        return $ret['access_token'];
    }

    /**
     * @brief 发送微信模板信息
     */


    /**
     * @brief 发送模板信息
     */
    public function sendMessage($data) {
        $accessToken = self::getAccessToken();
        $url         = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
        $data        = json_encode($data);
        $ret         = Request::RequestUrl($url, $data, 1);


        //if(!empty($ret)) {
        //    if($ret['errmsg'] == 'ok') {
        //        return true;
        //    }
        //}
        //向微信发送消息失败,线上请检查gearman服务是否开启,
        //return false;
    }

}