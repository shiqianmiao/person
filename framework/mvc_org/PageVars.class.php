<?php
/**
 * PageVars    页面变量
 *
 * @author    龙卫国 
 * @since     2013-3-6
 * @copyright Copyright (c) 2003-2013
 * @desc      
 */

class PageVars {
    
    /// 页面类型
    const PAGE_HOME   = 1; // 首页
    const PAGE_INDEX  = 2; // 主页
    const PAGE_LIST   = 3; // 列表页
    const PAGE_DETAIL = 4; // 详情页
    const PAGE_PUB    = 5; // 发布页

    /// 访问类型
    const ACCESS_HUMAN   = 1; // 用户
    const ACCESS_SPIDER  = 2; // 正常爬虫
    const ACCESS_CRAWLER = 3; // 非法抓取

    // 404页面url
    const URL_404        = '/404.php';
    const URL_404_AJAX   = '/404_ajax.php';
    const URL_404_IFRAME = '/404_iframe.php';

}
