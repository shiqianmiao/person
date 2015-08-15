<?php
/**
 * @author 缪石乾<miaoshiqian@anxin365.com>
 * @brief 简介：
 * @date 15/3/24
 * @time 下午3:35
 */
require_once API_PATH . '/impl/banner/BannerImpl.class.php';

class BannerInterface {
    /**
     * @brief 添加banner信息
     * @param $params['info'] banner信息数组
     * @return int 记录id
     * @throws Exception
     */
    public static function add($params) {
        return BannerImpl::add($params);
    }

    /**
     * @brief 获取banner列表
     */
    public static function getList($params) {
        return BannerImpl::getList($params);
    }

    /**
     * @breif 获取数量
     */
    public static function getCount($params) {
        return BannerImpl::getCount($params);
    }
}