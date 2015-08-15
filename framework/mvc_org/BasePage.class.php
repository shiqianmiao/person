<?php

require_once dirname(__FILE__) . '/PageVars.class.php';
require_once dirname(__FILE__) . '/BaseView.class.php';


class BasePage {

    // 当前页面类型，参考PageVars::PAGE_*，在子类指定
    protected $pageType;

    // 访问类型，参考PageVars::ACCESS_*，在子类指定
    protected $accessType;

    // 前端变量
    protected $staticVars = array();

    // 模板引擎
    protected $view;

    // 构造函数
    public function __construct() {
        $this->view = new BaseView();
    }
    
    public function init() {}
    
    protected function assign($key, $val='') {
        $this->view->assign($key, $val);
    }
    
    public function fetch($data, $tpl = '') {
        return $this->view->fetch($tpl, $data);
    }
    
    protected function render($data, $tpl = '') {
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
        $this->view->output($content, $attrs['content_type'], $attrs['jsonp']);
        XhprofLog::logXhprof('web');
        exit;
    }

    protected function renderError($msg='操作失败，请重试！ ', $code=1) {
        $attrs = Bootstrap::getRouteParams();
        $url = '';
        if ($attrs['ajax']) {
            $url .= PageVars::URL_404_AJAX;
        } else if ($attrs['iframe']) {
            $url .= PageVars::URL_404_IFRAME;
        } else {
            $url .= PageVars::URL_404;
        }
        $url .= '?errorMessage=' . rawurlencode($msg) . '&errorCode=' . rawurlencode($code);

        $this->redirect($url);
    }

    protected function redirect($url) {
        $attrs = Bootstrap::getRouteParams();
        if (!empty($attrs['jsonp'])) {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= Bootstrap::$ROUTE_NAMES['jsonp'] . '=' . rawurlencode($attrs['jsonp']);
        }

        ResponseUtil::redirect($url, $attrs['iframe']);
    }
}
