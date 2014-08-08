<?php
/*
 * @desc 新业管的配置文件
 * @author 缪石乾 <miaosq@273.cn>
 * @since 2013-9-2
 */
require_once dirname(__FILE__) . '/config.php';

require_once FRAMEWORK_PATH . '/util/http/RequestUtil.class.php';
require_once FRAMEWORK_PATH . '/util/http/ResponseUtil.class.php';

RequestUtil::gpcStripSlashes();

class BackendPageConfig {

    // 已注册的频道
    public static function getChannels() {
        return array(
            'default' => array(
                'code' => 'default',
                'text' => '首页',
            ),
            'mbs_index' => array(
                'code' => 'mbs_index',
                'text' => '业管首页',
                'banner_code' => 'can not see',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_index',
            ),
            'common' => array(
                'code' => 'common',
                'text' => '基础数据库',
            ),
            'mbs_post' => array(
                'code' => 'mbs_post',
                'text' => '车源管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_post',
            ),            
            'car_search' => array(
                    'code' => 'car_search',
                    'text' => '抢车源',
                    'dir'  => dirname(__FILE__) . '/../../mbs_common/car_search'
            ),
            
            'mbs_affairs' => array(
                'code' => 'mbs_affairs',
                'text' => '事务管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_common/mbs_affairs'
            ),
            'mbs_personal_setting' => array(
                'code' => 'mbs_personal_setting',
                'text' => '个人设置',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_personal_setting',
            ),
            'mbs_organization' => array(
                'code' => 'mbs_organization',
                'text' => '组织管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_organization',
            ),
            'mbs_daily' => array(
                'code' => 'mbs_daily',
                'text' => '日常设置',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_daily',
            ),
            'mbs_shop' => array(
                'code' => 'mbs_shop',
                'text' => '<font id="shop_maner_menu">店铺管理</font>',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_shop',
            ),
            'mbs_trade' => array(
                'code' => 'mbs_trade',
                'text' => '交易管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_trade',
            ),
            'mbs_contract' => array(
                'code' => 'mbs_contract',
                'text' => '交易签约管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_contract',
            ),
            'chefubao' => array(
                'code' => 'chefubao',
                'text' => '车付宝',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/chefubao',
            ),
            'mbs_crm' => array(
                'code' => 'mbs_crm',
                'text' => '客户管理系统',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_crm',
            ),
            'mbs_finance' => array(
                'code' => 'mbs_finance',
                'text' => '理财中心',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_finance',
            ),
            // mbs_v3迁移模块
            'phone' => array(
                'code' => 'phone',
                'text' => '电话转接',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/phone',
            ),
//            'score' => array(
//                'code' => 'score',
//                'text' => '积分排名系统',
//                'old'  => true,
//                'dir'  => dirname(__FILE__) . '/../../mbs_v3/score',
//            ),
            'log' => array(
                'code' => 'log',
                'text' => '日志查询系统',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../log'
            ),
            'adCenter' => array(
                'code' => 'adCenter',
                'text' => '推广中心 <font color="red">new</font>',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../ad_center/backend'
            ),
            'push' => array(
                'code' => 'push',
                'text' => '消息推送',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/push'
            ),
            'dept_payment' => array(
                'code' => 'dept_payment',
                'text' => '门店缴费',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/dept_payment'
            ),
            'shopInfo' => array(
                'code' => 'shopInfo',
                'text' => '门店信息管理系统',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/shopInfo',
            ),
            'check' => array(
                'code' => 'check',
                'text' => '检测平台',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../mbs_common/check'
            ),
            'feedback' => array(
                'code' => 'feedback',
                'text' => '投诉管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/feedback',
            ),
            'spider' => array(
                'code' => 'spider',
                'text' => '采集管理',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/spider'
            ),
            'distribution' => array(
                'code' => 'distribution',
                'text' => '分销平台',
                'old'  => true,
                'dir'  => dirname(__FILE__) . '/../../distribution/backend'
            ),
            'car_type' => array(
                    'code' => 'car_type',
                    'text' => '车型库管理',
                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/car_type'
            ),
            'sync_site' => array(
                    'code' => 'sync_site',
                    'text' => '全网发车管理',
                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/sync_site'
            ),
            'mbs_tongji' => array(
                'code' => 'mbs_tongji',
                'text' => '统计管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_tongji',
            ),
            'auction' => array(
                'code' => 'auction',
                'text' => '交易平台',
                'dir'  => dirname(__FILE__) . '/../../auction/backend'
            ),
            'pay' => array(
                'code' => 'pay',
                'text' => '收银台系统',
                'dir'  => dirname(__FILE__) . '/../../pay/backend'
            ),
            'mbs_video' => array(
                    'code' => 'video',
                    'text' => '培训视频',
                    'dir'  => dirname(__FILE__) . '/../../mbs_yg/mbs_video'
            ),
//             'weixin' => array(
//                     'code' => 'weixin',
//                     'text' => '微信管理',
//                     'dir'  => dirname(__FILE__) . '/../../mbs_common/weixin'
//             ), 
            'mbs_point' => array(
                'code' => 'mbs_point',
                'text' => '积分系统',
                'dir'  => dirname(__FILE__) . '/../../mbs_common/mbs_point'
            ),
        );
    }

    // 完整页面超级模板
    const WHOLE_SUPER_TPL  = 'mbs_common_whole_page.php';

    // iframe页面超级模板
    const IFRAME_SUPER_TPL = 'mbs_common_iframe_page.php';

    // 采用jsonp请求时的querystring参数的下标
    public static $JSONP_CALLBACK_NAME = 'jsonpCallback';

    public static function getTemplateConfig() {
        $params = self::getRequestParams();
        $template_path = array();

        $channel = $params['channel'];
        if ($channel != 'default') {
            $template_path[] = $params['channel_dir'] . '/template';
        }

        $template_path[] = BACKEND . '/channel/default/template';
        $template_path[] = BACKEND . '/channel/common/template';

        return array(
            'template_path' => $template_path,
            'helper_path'   => array(
                BACKEND . '/plugin',
            ),
        );
    }

    public static function getRequestParams() {
        static $params;
        if ($params) {
            return $params;
        }

        $params = self::parseUrl();
        return $params;
    }

    public static function parseUrl($url = '') {
        if (empty($url)) {
            $url = RequestUtil::getGET('_sys_url_path', '');
        } else {
            $url = preg_replace("/[#\?].*$/", '', $url);
        }

        $_paths = explode('/', $url);
        $paths = array();
        foreach ($_paths as $path) {
            if (!empty($path) && $path != '.' && $path != '..') {
                $paths[] = $path;
            }
        }

        if (count($paths) == 0){
            $paths[] = 'default';
        }
        if (count($paths) == 1){
            $paths[] = 'index';
        }
        if (count($paths) == 2){
            $paths[] = 'default';
        }

        $action   = array_pop($paths);
        $module   = array_pop($paths);
        $channel  = array_shift($paths);
        $moduleDir = 'app' . (count($paths) > 0 ? '/' . implode('/', $paths) : '');
        $channelDir = BACKEND . '/channel/' . $channel;

        $channels = self::getChannels();
        if (isset($channels[$channel])) {
            $channelInfo = $channels[$channel];
            if (!empty($channelInfo['dir'])) {
                $channelDir = rtrim($channelInfo['dir'], '/');
            }
            //trigger_error('未配置管理后台['.$channel.']', E_USER_ERROR);
        }

        $isAjax   = strpos($action, 'ajax') === 0 ? true : false;
        $isIframe = strpos($action, 'iframe') === 0 ? true : false;

        return array(
            'channel'        => $channel,
            'action'         => $action,
            'module'         => $module,
            'channel_dir'    => $channelDir,
            'module_dir'     => $moduleDir,
            'content_type'   => $isAjax ? ResponseUtil::CONTENT_TYPE_JSON : ResponseUtil::CONTENT_TYPE_HTML,
            'jsonp_callback' => RequestUtil::getPOST(self::$JSONP_CALLBACK_NAME, RequestUtil::getGET(self::$JSONP_CALLBACK_NAME, '')),
            'is_ajax'        => $isAjax,
            'is_iframe'      => $isIframe,
        );
    }

}

