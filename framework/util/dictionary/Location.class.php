<?php
/**
 * @brief 简介：地标数据获取类
 * @author 陈朝阳<chenchaoyang@iyuesao.com>
 * @date 15/3/19
 * @copyright Copyright (c) 2015 安心客 Inc. (http://www.anxin365.cn)
 */


class Location {
    const ALL_PROVINCE = 'province_all';
    const PROVINCE_CITY = 'province_city_';
    const PROVINCE_ID = 'province_id_';
    const CITY_ID = 'city_id_';
    /**
     * 获得所有省份数据 params array()
     */
    public static function getProvinceList($params = array()) {
        $key = self::ALL_PROVINCE;
        return CacheNamespace::readLocalCache($key);
    }

    /**
     * 获得省份下的城市数据 params array()
     */
    public static function getCityList($params = array()) {
        $id = Util::getFromArray('province_id', $params, 0);
        if (empty($id)) {
            return false;
        }
        $key = self::PROVINCE_CITY . $id;
        return CacheNamespace::readLocalCache($key);
    }

    /**
     * 通过id获取单个省份信息 params array()
     * province_id: 省份id
     */
    public static function getProvinceById($params = array()) {
        $id = Util::getFromArray('province_id', $params, 0);
        if (empty($id)) {
            return false;
        }
        $key = self::PROVINCE_ID . $id;
        return CacheNamespace::readLocalCache($key);
    }

    /**
     * 通过id获取单个城市信息 params array()
     * city_id: 城市id
     */
    public static function getCityById($params = array()) {
        $id = Util::getFromArray('city_id', $params, 0);
        if (empty($id)) {
            return false;
        }
        $key = self::CITY_ID . $id;
        return CacheNamespace::readLocalCache($key);
    }
} 