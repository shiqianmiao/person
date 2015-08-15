<?php
require_once BACKEND . '/widget/DataGridWidget.class.php';

/**
 * @brief 页面基类，交易管理页面基类
 * @author miaoshiqian
 * @since - 2013-8-23
 */

class ShopPage extends BackendPage {
    /**
     * @desc 每页显示的车源数量
     * @var int
     */
    public $pageSize = 20;
    
    /**
     * @desc 显示的最大车源数量
     * @var int
     */
    public $maxSize = 2000;
    
    /**
     * @desc 当前页数
     * @var int
     */
    public $currentPage = 1;
    
    public function init() {
        parent::init();
    }
    
    /**
     * @desc 拼接get参数
     * @return string
     */
    public function getQueryString() {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $queryString = isset($url['query']) ? $url['query'] : '';
        parse_str($queryString,$params);
        return http_build_query($params);
    }
}
