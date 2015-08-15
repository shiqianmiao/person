<?php
/**
 * @brief 简介：yac服务类,提供封装好的各种yac服务调用接口
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月8日 下午4:47:10
 */
class YacCache {
    /**
     * @desc yac获取
     * @param string|array $key 键名
     * @default 默认值，当要获取的key不存在的时候返回的默认值,不传则返回false
     */
    public static function get($key, $default = false) {
        $yac = new Yac();
        $result = $yac->get($key);
        return $result ? $result : $default;
    }
    
    /**
     * @brief yac添加
     * @param string $key 键名
     * @param string $value 值
     * @param bool 是否允许覆盖已经存在的值，默认为true，即允许覆盖已经存在的key值
     */
    public static function set($key, $value = '', $cover = true) {
        $yac = new Yac();
        if ($cover) {
            return $yac->set($key, $value);
        } else if (!self::get($key)){
            //如果已经存在相同的key就不写入，直接返回false失败。
            return $yac->set($key, $value);
        }
        
        return false;
    }
    
    /**
     * @brief yac删除
     * @param string|array $key 键名
     * @param string|array $delay 延时，延时几秒后执行删除操作
     */
    public static function delete($key, $delay = 0) {
        $yac = new Yac();
        return $yac->delete($key, $delay);
    }
    
    /**
     * @brief yac清除所有缓存
     */
    public static function flush() {
        $yac = new Yac();
        return $yac->flush();
    }
    
    /**
     * @brief yac信息
     */
    public static function info() {
        $yac = new Yac();
        return $yac->info();
    }
}