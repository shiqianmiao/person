<?php
/**
 * @package              v3
 * @subpackage           
 * @author               $Author:   weihongfeng$
 * @file                 $HeadURL$
 * @version              $Rev$
 * @lastChangeBy         $LastChangedBy$
 * @lastmodified         $LastChangedDate$
 * @copyright            Copyright (c) 2013, www.273.cn
 */
class ImageUtil {

    const TOKEN_KEY = 'M)!=tOz2)G7h$3x&yFMX539WjPYc#_UM';

    /**
     * @brief 格式化图片
     * @param string $image 图片地址
     * @param array $option 配置参数
     */
    public static function formatImageUrl($image, $option){
        $replacement = '';
        if($image){
            //图片宽度
            $replacement .= '_'.$option['width'];
            //图片高度
            $replacement .= '-'.$option['height'];
            //图片是否剪裁
            if($option['cut']){
                $replacement .= 'c';
            }
            //图片质量
            $replacement .= '_'.$option['quality'];
            //是否锐化图片,1锐化，0不锐化
            $replacement .= '_'.$option['mark'];
            //版本号
            $replacement .= '_'.$option['version'];
            //$pattern = "/(|_[\w-]*)(.[a-z]{3,})$/i";
            $pattern = "/(|_\d+-\d+[c|f]?_\d_\d_\d)(.[a-z]{3,})$/i";
            $replacement .= "\$2";
            $image = preg_replace($pattern,$replacement,$image);
        }
        return $image;
    }
    /**
     * @brief 获取无水印缩略图url
     * @param string $image 缩略图url
     */
    public static function getNoWaterImageUrl($image) {
        $time = time();
        $token = md5('/' . $image . $time . self::TOKEN_KEY);
        return $image . '?token=' . $token . '&time=' . $time; 
    }
}
