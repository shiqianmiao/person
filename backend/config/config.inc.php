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
            'sso' => array(
                'code' => 'sso',
                'text' => '单点登录',
                'dir'  => dirname(__FILE__) . '/../../sso/backend',
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
