<?php
/**
 * @brief 简介：爱月嫂、旧版图片工具类
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月19日 下午4:25:36
 */
class ImgUtil {
    /**
     * @brief 老版图片的域名
     * @author 缪石乾<miaoshiqian@iyuesao.com>
     * @date 2015年1月19日 下午4:29:52
     */
    public static $IMG_HOST = 'http://pic.iyuesao.com';
    
    /**
     * @brief 根据图片保存路径，输出指定配置的可显示url
     * @file 图片路径 例如：/Upload/Images/YueSao/3bd0be43-4a7b-4439-833e-54a046f2b810.jpg
     * $option array() 图片参数，各个参数详细说明如下：
     * $option['width']  需要显示的图片的宽 如：180,默认为100
     * $option['height'] 需要显示的图片的高 如：180 ，默认为100
     * $option['mode'] 模式，如：crop为裁剪，默认为裁剪
     */
    public static function getUrl($params) {
        $file = !empty($params['file']) ? $params['file'] : '';
        if (empty($file)) {
            return '';
        }
        
        $width = !empty($params['option']['width']) && is_int($params['option']['width']) ? $params['option']['width'] : 100;
        $height = !empty($params['option']['height']) && is_int($params['option']['height']) ? $params['option']['height'] : 100;
        $mode = !empty($params['option']['mode']) ? $params['option']['mode'] : 'crop';
        $option = "w={$width}&amp;h={$height}&amp;mode={$mode}";
        
        return self::$IMG_HOST . $file . '?' . $option;
    }
}