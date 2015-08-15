<?php
/**
 * @brief 简介：订单帮助类、提炼一些工具方法、供整个后台调用
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月30日 下午5:52:03
 */
class OrderHelper {
    /**
     * @brief 根据订单的ID来生成订单编号
     * @param unknown $orderId
     */
    public static function makeOrderNum($orderId) {
        if (empty($orderId) || !is_numeric($orderId)) {
            return '';
        }
        
        return 100000 + $orderId;
    }
    
    /**
     * @brief 根据小孩的生日、返回育儿嫂的类型
     * @param $birthday 小孩生日.时间戳
     */
    public static function getParentalType($birthday) {
        if (empty($birthday) || !is_numeric($birthday)) {
            return '无';
        }
        
        //备注：目前只有小婴育儿嫂。
        return '小婴育儿嫂';
    }

    /**
     * @brief 根据小孩的生日、返回育儿嫂的类型
     * @param $birthday 小孩生日.时间戳
     */
    public static function getYuesaoType($birthday) {
        return '月嫂';
    }
}