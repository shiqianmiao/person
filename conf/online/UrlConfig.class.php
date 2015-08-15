<?php

/**
 * @brief 每个人的域名不同，需要个性化的配置在这里配置，上线后这个文件ignore掉，不提交，如果线上环境改变，手动修改
 */
class UrlConfig {

    //测试环境BC后台URL，将这个URL改为自己的URL即可
    const TEST_BC_URL = 'bc.miaosq.com';

    //线上环境BC后台URL
    const ONLINE_BC_URL = 'bc.miaosq.com';

    const WX_WEB_URL = 'vagrant.weixin.com';
    /**
     * @brief 获取BC后台的URL（加http://），用来统一配置
     */
    public static function getBcUrl() {
        if ($_SERVER['DEBUG']) {
            $url = self::TEST_BC_URL;
        } else {
            $url = self::ONLINE_BC_URL;
        }
        return $url;
    }

    const RPC_HOST = 'http://rpc.local.anxin365.com';
    //const RPC_HOST = 'http://vagrant.rpc.com';
    const STA_URL = 'sta.miaosq.com';
    //
    const M_URL = 'http://m.test.anxin365.com';
    //app
    const HYBRID_URL = 'http://hybrid.anxin365.com';
}
