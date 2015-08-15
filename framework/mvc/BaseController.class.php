<?php
require_once dirname(__FILE__) . '/View.class.php';

class BaseController {
    // 前端变量
    protected $staticVars = array();

    // 模板引擎
    protected $view;

    // 构造函数
    public function __construct() {
        $this->view = new View();
        //设置应用下面的conf目录为自动加载
        if (is_dir(PROJECT_PATH . '/conf/')) {
            AutoLoader::setAutoDir(PROJECT_PATH . '/conf/');
        }
    }

    public function init() {
        
    }

    public function errorAction($params = array()) {
        $errorMessage = (!empty($params[0])) ? $params[0] : '对不起！操作失败，请稍后再试。';
        $errorCode = (!empty($params[1])) ? $params[1] : 1;
 
        if (Request::isAjax()) {
            Response::outputJson(array(
                'errorMessage' => $errorMessage,
                'errorCode'    => $errorCode
            ));
        } else if (in_array('?', $_GET)) {
            $key = array_search('?', $_GET);
            Response::outputJsonp($errorMessage, $key);
        } else {
            Response::output('<!DOCTYPE html><html><head></head><body><div style="text-align:center">' . $errorMessage . '</div></body></html>');
        }
        exit;
    }
}
