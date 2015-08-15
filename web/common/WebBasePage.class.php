<?php
/**
 * @desc web基类
 * @since 2014-5-12
 * @author 缪石乾<miaosq@273.cn>
 */

require_once FRAMEWORK_PATH . '/mvc_org/BasePage.class.php';

class WebBasePage extends BasePage {
    
    protected $_pageType = 0;
    
    private static $TIME = null;
    private static $TIME_ARRAY = array();
    private static $TIME_SPENT = array();
    
    public $pageType = 'default';
    
    public function __construct() {
        parent::__construct();
        //设置并像模板发送变量
        $this->_setPageType();
        $this->_setCommonSta();
    }

    /**
     * @desc 设置页面类型
     */
    private function _setPageType() {
        $urlParams = parse_url($_SERVER['REQUEST_URI']);
        $path = !empty($urlParams['path']) ? trim($urlParams['path'], '/') : 'default';
        $this->pageType = !empty($path) ? $path : 'default';
        $this->assign('pageType', $this->pageType);
    }
    
    /**
     * @desc 公共sta文件设置
     */
    private function _setCommonSta() {
        $commonStaHtml = 'g.js,config.js,app/agency/js/bootstrap.min.js,app/agency/css/bootstrap.min.css';
        //$commonStaHtml = 'app/agency/css/bootstrap.min.css';
        $commonStaHtml = $this->view->helper('sta', array('files' => $commonStaHtml));
        $this->assign('commonStaHtml', $commonStaHtml);
    }
    /**
     * @param $cache 是否需要页面缓存,true的话，返回页面渲染后的静态内容
     */
    protected function render($data, $tpl = '', $cache = false) {
        $attrs = Bootstrap::getRouteParams();
        if ($attrs['ajax']) {
            if (!empty($tpl)) {
                $data['data'] = $this->view->fetch($tpl, $data);
                if (isset($data['errorCode']) && $data['errorCode'] > 0) {
                    $data['errorMessage'] = $data['data'];
                }
            }
            $content = $data;
        } else {
            $content = $this->view->fetch($tpl, $data);
        }
        if ($cache) {
            return $content;
        }
        $this->view->output($content, $attrs['content_type'], $attrs['jsonp']);
        XhprofLog::logXhprof('web');
        exit;
    }
    
    /**
     * @desc 计算页面各个功能模块的时间花费
     */
    protected static function _time($name = '', $timeKey = null) {
        if (self::$TIME === false) {
            return;
        }
        if (self::$TIME === null) {
            $debug = RequestUtil::getGET('__spenttime');
            if ($debug === 'hi273') {
                self::$TIME = true;
            } else {
                self::$TIME = false;
                return;
            }
        }

        list($usec, $sec) = explode(" ", microtime());
        $time = ((float)$usec + (float)$sec);
        if (self::$TIME === true && empty(self::$TIME_ARRAY)) {
            self::$TIME_ARRAY['total'] = $time;
        } else {
        }
        $spentTime = false;

        if ($timeKey === null) {
            if (is_numeric(self::$TIME)) {
                $spentTime = round((($time - self::$TIME)*1000), 1);
            }
            self::$TIME = $time;
        } else {
            if (isset(self::$TIME_ARRAY[$timeKey]) && is_numeric(self::$TIME_ARRAY[$timeKey])) {
                $spentTime = round((($time - self::$TIME_ARRAY[$timeKey])*1000), 1);
                self::$TIME_SPENT[$timeKey] = $spentTime;
            }
            self::$TIME_ARRAY[$timeKey] = $time;
        }

        if ($spentTime !== false) {
            if ($name !== '') {
                $name = $name.': ';
                if ($timeKey !== null) {
                    echo "<strong>{$name} {$spentTime}ms</strong><br />";
                    if ($timeKey == 'total' && count(self::$TIME_SPENT) > 1) {
                        echo "<br /><strong>总花费时间占比:</strong><br />";
                        foreach (self::$TIME_SPENT as $key => $val) {
                            if ($key != 'total') {
                                $percent = round($val/$spentTime, 4) * 100;
                                echo "<strong>{$key} —— {$percent}%</strong><br />";
                            }
                        }
                    }
                } else {
                    echo "{$name} {$spentTime}ms<br />";
                }
            }
        }

    }
    
    /**
     * 显示一个“404”页面
     * @param string $msg
     */
    public function displayPageNotFound($msg = '对不起！您要查看的页面没有找到或已删除。') {
        
        $host = $_SERVER['HTTP_HOST'];
        if (preg_match('/^(\w+)\.273\./', $host, $matchs) && !empty($matchs[1])) {
            $domain = strtolower($matchs[1]);
        }
        if ($domain == 'www') {
            $domainInfo['name'] = '全国';
        } else {
            $domainInfo = CityRedirect::getLocationByDomain(array('domain' => $domain));
            if (empty($domainInfo)) {
                $domain = 'www';
                $domainInfo['name'] = '全国';
            }
        }
        $template = 'common/404.php';
        $staHtml = $this->view->helper('sta', array('files' => 'g.js,config.js,app/ms/css/basic.css'));
        
        $next = '';
        $referer = getenv('HTTP_REFERER');
        if(!empty($referer)){
            $hostInfo = parse_url($referer);
            if(preg_match("/([a-z0-9]+)\.273\.(cc|cn|com.cn)/",$hostInfo['host'])) {
                //如果refer是本站的话
                $next = $referer;
            }
        }
        $next = empty($next) ? "http://{$domain}.273.cn/" : $next;

        $this->render(array(
            'msg'        => $msg,
            'staHtml'    => $staHtml,
            'domainName' => $domainInfo['name'],
            'pageType'   => '404',
            'nextUrl'    => $next,
            'domain'     => $domain
        ), $template);
        exit();
    }
    
     /**
     * 输出404状态 ，服务器需配置404处理页面
     * @param string $msg
     */
    public function display404() {
         header('HTTP/1.1 404 Not Found');
         header("status: 404 Not Found");
         exit();
    }
}
