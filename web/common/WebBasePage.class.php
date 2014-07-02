<?php
/**
 * @desc web基类
 * @since 2014-5-12
 * @author 缪石乾<miaosq@273.cn>
 */

require_once FRAMEWORK_PATH . '/mvc/BasePage.class.php' ;

class WebBasePage extends BasePage {
    
    protected $_pageType = 0;
    
    private static $TIME = null;
    private static $TIME_ARRAY = array();
    private static $TIME_SPENT = array();
    
    /**
     * @desc 是否是有门店地区,默认有
     */
    protected $_isStoreCity = true;
    /**
     * @desc 是否是城市
     */
    protected $_isCity = false;
    
    /**
     * @desc 是否是蜘蛛
     */
    protected $_isSpider = false;
    
    /**
     * @desc 合作网站链接信息
     * @var array
     */
    protected $_domain = array();
    
    /**
     * 跳转所在城市domain
     * @var array
     */
    protected $_jumpDomain = 'www';
    
    /**
     * @var domain对应的cookie键
     */
    protected $_domainCookieKey = '273home';
    
    /**
     * @var 关闭电话转接
     */
    protected $_closeExtPhone = false;
    
    public function __construct() {
        parent::__construct();
        /*$this->_isSpiderVisit();
        $this->_closeExtPhone = ExtPhoneInterface::isExtPhoneClose();
        if (SITE_VERSION != 'v2' ) {
            $touchUrl = RequestUtil::getCurrentUrl();
            $this->_domain = BaseHelper::getSiteDomain();
            $domain = $this->_domain['location'];
            
            if (!$this->_isSpider) {
                $cookieDomain = RequestUtil::getCOOKIE('273home');
                if (!empty($cookieDomain)) {
                    $this->_jumpDomain = $cookieDomain;
                } else {
                    $cityInfo = LocationInterface::getCityByIp();
                    if (array_key_exists($cityInfo['domain'], CityRedirect::$OLD2NEW)) {
                        $cityInfo['domain'] = CityRedirect::$OLD2NEW[$cityInfo['domain']];
                    }
                    $this->_jumpDomain = $cityInfo['domain'];
                }
                if (empty($this->_jumpDomain)) {
                    $this->_jumpDomain = 'www';
                }
            }
            if (!empty($this->_domain) && $domain == 'www') {
                $touchUrl = str_replace('www', 'm', $touchUrl);
            } else {
                $newDomain = $domain;
                if (key_exists($domain, CityRedirect::$OLD2NEW)) {
                    $newDomain = CityRedirect::$OLD2NEW[$domain];
                }
                $touchUrl = str_replace($domain, $newDomain.'.m', $touchUrl);
            }
            $this->assign('touchUrl',$touchUrl);
            //获取顶部搜索框下方的热词链接
            $this->_setSearchKeywords();
            $this->_isHasStore($domain);
            //设置公共变量
            $this->_setPublicParam();
            $this->_getPartnerInfo();
            $this->_nofollowPartner();
        }*/
    }
    
    /**
     * @desc 获取通过cookie或ip获取用户当前城市的domain
     */
    protected function _getDomainByCookieAndIp () {
        $cookieDomain = RequestUtil::getCOOKIE($this->_domainCookieKey);
        $domain = null;
        if (!empty($cookieDomain)) {
            $domain = $cookieDomain;
        } else {
            $cityInfo = CityRedirect::getCityByIp();
            if (!empty($cityInfo)) {
                $domain = $cityInfo['domain'];
            }
        }
        return $domain;
    }
    
    /**
     * @desc 设置公共变量
     * @author chenchaoyang
     */
    protected function _setPublicParam() {
        //无门店地区的头部广告
        if (!$this->_isStoreCity) {
            $commonDataSKeywords = CarCommonDataInterface::getData('INDEX_TOP_ADV_PIC');
            $noDeptAd = $commonDataSKeywords['no_dep_adv'];
            $this->assign('noDeptAd', $noDeptAd);
        }
    }
    
    /**
     *
     * @brief 获取顶部搜索框下方的热词链接
     */
    protected function _setSearchKeywords() {
        //获取通用数据存储容器中的指定内容，搜索框下方的热词链接
        $commonDataSKeywords = CarCommonDataInterface::getData('SEARCH_KEYWORDS');
    
        $keyWordsArray = array();
        $getNum = 0;
        foreach ($commonDataSKeywords as $item) {
            //当前关键词有限定了某些城市显示的话
            if ($item['city']) {
                //当前域名必须属于指定域名内才能显示
                if (strpos(',,' . $item['city'] . ',,', $this->_domain['location'])) {
                    $keyWordsArray[] = $item;
                    ++$getNum;
                }
            }
            else {
                $keyWordsArray[] = $item;
                ++$getNum;
            }
            //最多只取6个关键词
            if ($getNum >= 5) {
                break;
            }
        }
        $this->assign('commonDataSKeywords',$keyWordsArray);
    }
    
    /**
     * @desc 取得该地区的连锁信息(有无门店),并插入到protect变量和模板中,非省份城市站定义跳转host
     * 规则:只要有门店的地区就都算作有门店,比如:www,fj(省级)
     */
    protected function _isHasStore($domain) {
        $siteDomain['domain'] = strtolower($domain);
        $ret = false;
        if(!empty($siteDomain['domain'])) {
            if ($siteDomain['domain'] != 'www') {
                $domainInfo = CityRedirect::getLocationByDomain($siteDomain);
            } else {
                $ret = true;
            }
        }
        
        //$domainInfo为空说明既不是www也不是任何城市省份站,现头部各跳转都跳转到http://www.273.cn
        if (!empty($domainInfo)) {
            $chainParmas = array();
            $chainParmas['id'] = $domainInfo['id'];
            if ($domainInfo['type'] == 'province') {
                $chainParmas['type'] = 1;
            } else {
                $chainParmas['type'] = 0;
            }
            $ret = LocationInterface::isChainCity($chainParmas);
            $this->_isCity      = !$chainParmas['type'];
            $this->assign('isCity', !$chainParmas['type']);
        } else if ($domain != 'www') {
            $domain = 'www';
            $homeHost = 'http://www.273.cn';
            $this->assign('homeDomain', $domain);
            $this->assign('homeHost', $homeHost);
            $this->_isStoreCity = flase;
            $ret = false;
        }
        $this->_isStoreCity = $ret;
        $this->assign('isStoreCity', $ret);
        return $ret;
    }
    
    /**
     * 网址跳转,首页列表页通用
     */
    protected function _redJudge($domainInfo = array(), $uri = '') {
        //全国页,如果合作站的网址不支持xxx.www.273.cn则跳转到xxx.273.cn
        if($this->_domain['location'] == 'www' && $this->_domain['not_www'] && $this->_domain['illegal_www']) {
            $redUrl = 'http://';
            if (!empty($this->_domain['partner'])) {
                $redUrl .= $this->_domain['partner'] . '.';
            }
            if (!empty($this->_jumpDomain) && $this->_jumpDomain != 'www') {
                $redUrl .= $this->_jumpDomain . '.';
            }
            $redUrl .= "273.cn{$_SERVER['REQUEST_URI']}";
            ResponseUtil::redirectPermently($redUrl);
        }
        //不是全国页,也不是合作站
        if((empty($domainInfo) && $this->_domain['location'] != 'www') || $this->_domain['not_exist_partner']) {
            $redUrl = 'http://';
            if (!empty($this->_domain['partner'])) {
                $redUrl .= $this->_domain['partner'] . '.';
            }
            if (!empty($this->_jumpDomain)) {
                $redUrl .= $this->_jumpDomain . '.';
            }
            $redUrl .= '273.cn';
            if (!empty($uri)) {
                $redUrl .= $uri;
            }
            ResponseUtil::redirectPermently($redUrl);
        }
    }
    /**
     * 插入头尾部
     */
    protected function _assignHeadFooter(& $renList = array(), $hDir = 'common/header_v2.php', $fDir = 'common/footer_v2.php') {
        if (empty($this->_domain['partner'])) {
            $header = $this->fetch($renList, $hDir);
            $footer = $this->fetch($renList, $fDir);
        } else {
            $partner = $this->_domain['partner'];
            $partConf = PartnerConfig::$PARTNER_CONF[$partner];
            $iframeHtml = '<iframe class="lay1 pa_iframe" scrolling="no" style="height:%dpx" src="%s?%d"></iframe>';
            if ($partConf['header_url']) {
                $header = sprintf($iframeHtml, $partConf['h_height'], $partConf['header_url'], rand(0, 10000));
            } else {
                $header = $this->fetch($renList, $hDir);
            }
            if ($partConf['footer_url']) {
                $footer = sprintf($iframeHtml, $partConf['f_height'], $partConf['footer_url'], rand(0, 10000));
            } else {
                $footer = $this->fetch($renList, $fDir);
            }
            $this->assign('partner', $this->_domain['partner']);
        }
        $this->assign('header', $header);
        $this->assign('footer', $footer);
    }
    
    /**
     * nofollow不允许爬虫的页面
     */
    private function _nofollowPartner() {
        if (!empty($this->_domain['partner'])) {
            $partner = $this->_domain['partner'];
            $partConf = PartnerConfig::$PARTNER_CONF[$partner];
            if ($partConf['no_follow']) {
                $this->assign('noFollow', '<meta name="robots" content="noindex,nofollow" />');
            }
        }
    }
    
    /**
     * @desc 当前是否是蜘蛛访问
     */
    protected function _isSpiderVisit() {
        //蜘蛛访问时，服务器会将ua修改为273Spider
        if (strpos($_SERVER['HTTP_USER_AGENT'], '273Spider') !==false) {
            $this->_isSpider = true;
        } else {
            $this->_isSpider = false;
        }
        return $this->_isSpider;
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
     * 取得合作站日志
     */
    private function _getPartnerInfo() {
        $type = RequestUtil::getGET('__log', '');
        if (empty($type) || empty($this->_domain['partner'])) {
            return;
        }
        if ($type == 'hipartner') {
            PartnerHelper::getLog($this->_domain['partner']);
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
