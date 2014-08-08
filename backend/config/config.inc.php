<?PHP
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
            'common' => array(
                'code' => 'common',
                'text' => '基础数据库',
            ),
            'sso' => array(
                'code' => 'sso',
                'text' => '单点登录',
                'dir'  => dirname(__FILE__) . '/../../sso/backend',
            ),
            'phone' => array(
                'code' => 'phone',
                'text' => '电话转接',
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/phone',
            ),            
            'mbsNew' => array(
                'code' => 'mbsNew',
                'text' => '业管系统',
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/mbsNew',
            ),
            'zs' => array(
                'code' => 'zs',
                'text' => '招商管理系统',
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/zs',
            ),
            'score' => array(
                'code' => 'score',
                'text' => '积分排名系统',
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/score',
            ),
            'log' => array(
                'code' => 'log',
                'text' => '日志查询系统',
                'dir'  => dirname(__FILE__) . '/../../log'
            ),
//            'pay' => array(
//                'code' => 'pay',
//                'text' => '收银台系统',
//                'dir'  => dirname(__FILE__) . '/../../pay/backend'
//            ),
            'adCenter' => array(
                'code' => 'adCenter',
                'text' => '推广中心 <font color="red">new</font>',
                'dir'  => dirname(__FILE__) . '/../../ad_center/backend'
            ),
            'check' => array(
                'code' => 'check',
                'text' => '检测平台',
                'dir'  => dirname(__FILE__) . '/../../check'
            ),            
//            'info' => array(
//                    'code' => 'info',
//                    'text' => '信息管理',
//                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/info'
//            ),
            'push' => array(
                    'code' => 'push',
                    'text' => '消息推送',
                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/push'
            ),
            'webBackend' => array(
                'code' => 'webBackend',
                'text' => '主站后台',
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/web_backend',
            ),
            'dept_payment' => array(
                    'code' => 'dept_payment',
                    'text' => '门店缴费',
                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/dept_payment'
            ),
            'shopInfo' => array(
                'code' => 'shopInfo',
                'text' => '门店信息管理系统',
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/shopInfo',
            ),
            'feedback' => array(
                    'code' => 'feedback',
                    'text' => '用户评价',
                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/feedback'
            ),
            'chefubao' => array(
                    'code' => 'chefubao',
                    'text' => '车付宝',
                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/chefubao'
            ),
            'weixin' => array(
                    'code' => 'weixin',
                    'text' => '微信管理',
                    'dir'  => dirname(__FILE__) . '/../../mbs_common/weixin'
            ),
            'spider' => array(
                    'code' => 'spider',
                    'text' => '采集管理',
                    'dir'  => dirname(__FILE__) . '/../../mbs_v3/spider'
            ),
            'distribution' => array(
                    'code' => 'distribution',
                    'text' => '分销平台',
                    'dir'  => dirname(__FILE__) . '/../../distribution/backend'
            ),            
            'car_type' => array(
                'code' => 'car_type',
                'text' => '车型库管理',
                'dir'  => dirname(__FILE__) . '/../../mbs_v3/car_type'
            ),
        );
    }

    // 完整页面超级模板
    const WHOLE_SUPER_TPL  = 'common_whole_page.php';

    // iframe页面超级模板
    const IFRAME_SUPER_TPL = 'common_iframe_page.php';

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
        //var_dump('JSON_CALLBACK!!!');
        //var_dump(RequestUtil::getPOST(self::$JSONP_CALLBACK_NAME, RequestUtil::getGET(self::$JSONP_CALLBACK_NAME, '')));

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
