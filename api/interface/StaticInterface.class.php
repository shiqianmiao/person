<?php
/**
 * @desc 静态文件调用接口
 * @copyright (c) 2011 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-06-13
 */

require_once API_PATH . '/impl/static/StaticImpl.class.php';

class StaticInterface {

    /**
     * @desc 获取静态文件列表
     * @param array $params
     * - file array('a.js', 'b.css') | 'a.js, b.css'  路径相对于src目录
     * @return string 返回script标签或者link标签
     */
    public static function getStaticFiles($params) {

        return StaticImpl::getStaticFiles($params);
    }
}