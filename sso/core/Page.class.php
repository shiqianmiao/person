<?php

/**
 * @brief sso项目中页面的基类，不采用v3框架，因为需要独立管理
 * @author aozhongxu
 */

class Page {

    public $phpTpl = null;

    final public function __construct() {
        $this->phpTpl = new PhpTpl();
    }

    /**
     * @brief 输出页面
     */
    final public function render($tplFile, $paramArray = array()) {
        
        header('Content-Type:text/html; charset=UTF-8');
        $html = $this->phpTpl->fetch($tplFile, $paramArray);
        echo $html;
        exit;
    }

}
