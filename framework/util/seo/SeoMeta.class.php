<?php
/**
 * seo接口类
 *
 * @author    gaosk <gaosk@273.cn>
 * @since     2013-3-8
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc      seo接口类
 */

///1. 每个页面的规则不一样，
///   如首页，只要传一个title参数即可，这时候一个简单的方法带上一个title参数就可以实现
//    而搜索列表页，则要根据多个参数生成seo信息，需要传递GET参数数组来生成，方法比较复杂
///2. seo信息包括标题、描述、关键字大多数情况下根据参数同时处理比较好，而不需要各自单独写方法
///3. seo信息不仅仅包括标题、描述、关键字。还包括fedd等其他头信息，所以对外的方法的返回结果应该是生成所有这些头信息的字符串
///4. 有的页面根据参数的不同会有不同的seo信息。如部分省市页面有测试的seo信息

class SeoMeta {

    const SITE_NAME = '273二手车交易网';

    private static $_PAGE_TYPE = 1;
    ///页面类型

    const PAGE_INDEX = 1;
    ///主页

    const PAGE_MAIN = 2;
    ///省市主页

    const PAGE_LIST = 3;
    ///搜索车源列表页

    const PAGE_VIEW = 4;
    ///车源详细页

    const TITLE_TPL = '';
    const KEYWORD_TPL = '';
    const DESCRIPTION_TPL = '';
    const TITLE_TPL_BETA = '';
    const KEYWORD_TPL_BETA = '';
    const DESCRIPTION_TPL_BETA = '';
    ///标题、描述、关键字模板，包括beta版

    private static $_META_TITLE = '273二手车交易网';
    private static $_META_KEYWORD = '二手车,二手车交易网,273二手车';
    private static $_META_DESCRIPTION = '273二手车交易网是国内最大的二手车交易平台';

    function __construct($pageType) {
        self::$_PAGE_TYPE = $pageType;
    }

    ///直接设置标题
    public static function setTitle($title) {
        if (self::$_PAGE_TYPE == self::PAGE_INDEX) {
            self::$_META_TITLE = self::SITE_NAME . '|' .$title;
        } else {
            self::$_META_TITLE = $title;
        }
    }

    ///直接设置关键字
    public static function setKeyword($keyword) {
        self::$_META_KEYWORD = $keyword;
    }

    ///直接设置描述
    public static function setDescription($description) {
        self::$_META_DESCRIPTION = $description;
    }

    ///直接设置所有seo信息
    public static function createMetaByInfo($meta) {
        self::setTitle($meta['title']);
        self::setKeyword($meta['keyword']);
        self::setDescription($meta['description']);
    }

    ///根据参数生成标题、描述、关键字
    function createMetaByParam($params = array()){
    	
    }

    /**
     * 生成seo信息
     * @param type $output
     * @return mixed 返回seo的meta字符串或者数组
     */
    public function genMetaInfo($output = 0) {
        if ($output) {
            return '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>'.self::$_META_TITLE.'</title>
            <meta name="keywords" content="'.self::$_META_KEYWORD.'" />
            <meta name="description" content="'.self::$_META_DESCRIPTION.'" />';
        } else {
            return array(
                'title' => self::$_META_TITLE,
                'keyword' => self::$_META_KEYWORD,
                'description' => self::$_META_DESCRIPTION
            );
        }

    }

}