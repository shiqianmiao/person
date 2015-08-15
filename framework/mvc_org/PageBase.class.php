<?php
/**
 * @Copyright (c) 2011 Ganji Inc.
 * @file          /ganji_v5/app/common/PageBase.class.php
 * @author        longweiguo@ganji.com
 * @date          2011-03-29
 *
 * 页面基类
 */

abstract class PageBase {

    /// 页面模式
    const PAGE_MODE_DEFAULT = "default"; ///< 默认的页面模式
    const PAGE_MODE_IFRAME = "iframe"; ///< 在iframe中显示的页面模式
    const PAGE_MODE_AJAX = "ajax"; ///< 以json格式返回的页面模式
    const PAGE_CHARSET = 'gbk';
    /// 当前页面模式，参考PAGE_MODE_DEFAULT
    public static $PAGE_MODE = self::PAGE_MODE_DEFAULT;

    /// 页面类型
    const PAGE_TYPE_LIST = "list"; ///< 列表页
    const PAGE_TYPE_DETAIL = "detail"; ///< 详情页
    const PAGE_TYPE_PIC = "pic"; ///< 图片页
    const PAGE_TYPE_PUB = "pub"; ///< 发布页
    const PAGE_TYPE_PUB_SELECT = "pub_select"; ///< 发布流程页
    const PAGE_TYPE_CHANGE_CITY = "change_city"; //城市切换页
    const PAGE_TYPE_INDEX = "index";

    /// 访问类型
    const HUMAN = 1; //用户
    const SPIDER = 2; //正常爬虫
    const CRAWLER = 3; //非法抓取

    /// 当前页面类型，参考PAGE_TYPE_LIST
    public static $PAGE_TYPE;
    public static $tplEngine = null;
    
    public static $seoData;

    /**
     * 日志统计参数
     * @var array
     */
    public static $LOG_TRACKER_INFO = array();

    /**
     * 需要显示或传递给模板的值
     * @var array
     */
    protected $renderValues = array();

    /**
     * 自定义的参数
     * @var array
     */
    protected $cumtomParams = array();

    /**
     * 当前Page对象的实例
     * @var object
     */
    public static $instance;

    /**
     * 访问类型
     */
    public static $ACCESS_TYPE = self::HUMAN;

    /**
     * 宇宙超级无敌前端组件配置参数
     */
    public static $STA_CONFIG = array();

    /**
     * FormNamespace对象
     * @var object|FormNamespace
     */
    public static $FORM_NAMESPACE = null;

    private static $TIME = null;
    private static $TIME_ARRAY = array();
    private static $TIME_SPENT = array();

    protected static function _time($name = '', $timeKey = null) {
        if (self::$TIME === false) {
            return;
        }
        if (self::$TIME === null) {
            $debug = HttpUtil::getGET('__debug');
            if ($debug === 'gohome') {
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
            }
            if ($timeKey !== null) {
                echo "<strong>{$name} {$spentTime}ms</strong><br />";
                if ($timeKey == 'total') {
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

    /**
     * 构造器，子类中不能覆盖
     * @param string pageMode 页面模式
     */
    final public function __construct($pageMode = '') {
        if (!empty($pageMode)) {
            self::$PAGE_MODE = $pageMode;
        }

        self::$instance = $this;
    }
    
    public static function seo($data='') {
        if ($data==='') {
            return '<title>'.PageBase::$seoData['title'].'</title>
                <meta name="Keywords" content="'.PageBase::$seoData['keywords'].'">
                <meta name="Description" content="'.PageBase::$seoData['description'].'">
                ';
        }
        PageBase::$seoData = $data;
    }

    /**
     * 设置自定义参数，在入口调用
     * @param array cumtomParams 要设置的参数
     */
    public function setCustomParams($cumtomParams = array()) {
        $this->cumtomParams = $cumtomParams;
    }

    /**
     * 初始化，在入口自动调用
     * @param   $type   string  php|smarty
     * @param   $config array
     * @return object
     * @code
     *  config 格式:
     *  self::$TEMPLATE_CONFIG = array(
     *      'template_type'         => self::TEMPLATE_TYPE_PHP,
     *      'tmpelate_path'         => GANJI_V5 . '/template',
     *      'template_cpatch'       => GANJI_V5 . '/template_c',
     *      'smarty_plugin_path'    => GANJI_V5 . '/plugin/smarty',
     *      'helper_path'           => GANJI_V5 . '/plugin/helper',
     *      'template_cache_path'   => '',
     *      'isCached'              => false,
     *      );
     *
     * @endcode
     */
    public function init($type = 'php', $config = array()) {
        self::getTemplateEngine($type, $config);
    }

    /** {{{ getTemplateEngine
     * 取得模板引擎对象
     * 此处不引用V5代码，所以将代码修改成传入模板类型+配置
     * @param   $type   string  php|smarty
     * @param   $config array
     * @return object
     * @code
     *  config 格式:
     *  self::$TEMPLATE_CONFIG = array(
     *      'template_type'         => self::TEMPLATE_TYPE_PHP,
     *      'tmpelate_path'         => GANJI_V5 . '/template',
     *      'template_cpatch'       => GANJI_V5 . '/template_c',
     *      'smarty_plugin_path'    => GANJI_V5 . '/plugin/smarty',
     *      'helper_path'           => GANJI_V5 . '/plugin/helper',
     *      'template_cache_path'   => '',
     *      'isCached'              => false,
     *      );
     *
     * @endcode
     */
    static public function getTemplateEngine($type = 'php', $config = array()) {
        if (empty($config)) {
            $config = array(
               'template_type'         => $type,
               'tmpelate_path'         => APP_PATH . '/template',
               'template_cpatch'       => APP_PATH . '/template_c',
               'smarty_plugin_path'    => APP_PATH . '/plugin/smarty',
               'helper_path'           => APP_PATH . '/plugin/helper',
               'template_cache_path'   => '',
               'isCached'              => false,
           );
        }
        if ('php' === $type) {
            if (empty(self::$tplEngine)) {
                include_once BLACKHOLE_PATH . "mvc/tpl/PhpTemplate.class.php";
                self::$tplEngine = new PhpTemplate($config);
            }
            return self::$tplEngine;
        } else if ('smarty' === $type) {
            if (empty(self::$tplEngine)) {
                include_once BLACKHOLE_PATH . "mvc/tpl/SmartyEngine.class.php";
                self::$tplEngine = new SmartyEngine($config);
            }
            return self::$tplEngine;
        } else {
            trigger_error('模板引擎类型不存在', E_USER_ERROR);
        }
    }//}}}


    /**
     * 设置需要输出的值
     *
     * @param string|array $key
     * @param mix $val
     */
    public function setRenderValue($key, $val = null) {
        if (!is_array($this->renderValues)) {
            $this->renderValues = (array)$this->renderValues;
        }
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->renderValues[$k] = $v;
            }
        } else {
            $this->renderValues[$key] = $val;
        }
    }
    /**
     * setRenderValue 的别名
     * @param type $key
     * @param type $val
     */
    public function assing($key, $val = null) {
        $this->setRenderValue($key, $val);
        
    }
    /**
     * render 别名
     * @param type $template
     */
    public function display($template) {
        $this->render($template);
    }

    /**
     * 输出页面，在ajax模式下输出json_encode后的字串，否则通过模板引擎输出
     *
     * @param array renderValues 要输出的值
     * @param string template 模板名
     */
    public function render($template = '') {
        if (self::$PAGE_MODE == self::PAGE_MODE_DEFAULT || self::$PAGE_MODE == self::PAGE_MODE_IFRAME) {
            foreach ($this->renderValues  as $key => $val) {
                self::$tplEngine->assing($key, $val);
            }
            header('Content-Type:text/html; charset='.self::PAGE_CHARSET);
            $pageHtml = self::$tplEngine->display($template);
            self::_time('render page', 'render_page');
            self::_time('total time', 'total');
        } else if (self::$PAGE_MODE == self::PAGE_MODE_AJAX) {
            //header('Content-Type:text/javascript; charset=UTF-8');
            echo json_encode($this->renderValues);
        }
    }

    /**
     * 解析模板并取得要输出的字符串
     *
     * @param array $renderValues
     * @param string $template
     * @return string
     */
    protected function fetch($template) {
        if (empty($template)) {
            trigger_error("未指定页面模板", E_USER_ERROR);
        }

        if(null == self::$tplEngine) self::getTemplateEngine();
        $templateStr = self::$tplEngine->fetch($template);
        return $templateStr;
    }

    /**
     * 显示一个“404”页面
     * @param string $msg
     */
    public static function displayPageNotFound($msg = '对不起！您要查看的页面没有找到或已删除。') {
        include_once GANJI_V5 . '/app/common/ErrorPage.class.php';
        $pageMode = !(empty(PageBase::$PAGE_MODE)) ? PageBase::$PAGE_MODE : PageBase::PAGE_MODE_DEFAULT;
        $page = new ErrorPage($pageMode);
        $page->init('404');

        $template = (self::$PAGE_MODE == self::PAGE_MODE_IFRAME) ? 'widget_v2/404_iframe.php' : 'widget_v2/404.php';

        self::$instance->render(array(
            'msg' => $msg,
            'autoRedirect' => ($_SERVER['REQUEST_URI'] != '/') ? true : false,
            'next' => preg_replace('/[^\/]+\/?$/', '', $_SERVER['REQUEST_URI']),
            'cityInfo' => self::$CITY_INFO
        ), $template);
        exit();
    }

    

    

    /**
     * 输出一个只有js的页面
     * @param string $jsString
     */
    public static function displayJsPage($jsString) {
        header('Content-Type:text/html; charset=UTF-8');
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
<script type="text/javascript">
' . $jsString . '
</script>
</head>
<body>
</body>
</html>';
        exit();
    }

}
