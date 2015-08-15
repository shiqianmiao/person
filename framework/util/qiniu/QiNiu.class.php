<?php
/**
 * @brief 简介：七牛文件云存储工具类
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月26日 下午5:01:08
 */
require_once dirname(__FILE__) . '/../../lib/qiniu/rs.php';
require_once dirname(__FILE__) . '/../../lib/qiniu/fop.php';
require_once dirname(__FILE__) . '/../../lib/qiniu/io.php';

class QiNiu {
    private static $_ACCESS_KEY = '_yeH4ZUG1R_9x6yrOgOUcbFcQcVsbSy83stoDQeI';
    private static $_SECRET_KEY = '59AcojCqHQFDDYPnAcSfUAVLmLCoRdKviAIqzr0O';
    //域名
    private static $_QINIU_DOMAIN = '7u2rcd.com1.z0.glb.clouddn.com';
    //七牛上面的空间名称
    private static $_BUCKET = 'miaosq';
    
    /**
     * @brief 格式化图片、返回需要的外链url
     * @param string $path 图片的路径
     * @param array $option 图片的参数、即为、width、height这些参数
     * @param $option['mode'] （默认为0）的特殊说明：
     * =0 ：即为返回不超过指定的宽高的图片。（等比例缩放的前提下、w和h  哪个先到边界就停住）
     * =1 ：强制按照指定的 width 和 height返回图片、不管变形
     */
    public static function formatImageUrl($path, $option = array()) {
        if (empty($path)) {
            return '';
        }
        
        Qiniu_SetKeys(self::$_ACCESS_KEY, self::$_SECRET_KEY);
        
        //生成baseUrl
        $baseUrl = Qiniu_RS_MakeBaseUrl(self::$_QINIU_DOMAIN, $path);
        if (empty($option['width']) && empty($option['height'])) {
            return $baseUrl;
        } else {
            $mode = !empty($option['mode']) && in_array($option['mode'], array(0, 1)) ? $option['mode'] : 0;
            
            $extUrl = 'imageMogr2/thumbnail/';
            $width = !empty($option['width']) ? $option['width'] : '';
            $height = !empty($option['height']) ? $option['height'] : '';
            $url = $baseUrl . '?' . $extUrl . $width . 'x' . $height;
            
            if (!empty($mode) && !empty($option['width']) && !empty($option['height'])) {
                //强制宽高
                $url .= '!';
            }
            
            return $url;
        }
    }
    
    /**
     * @brief 上传本地文件
     * @param $file 要上传的文件的本地路径。例如从表单上来的$_FILES 里面的temp_name
     * @param $pre 前缀。必须要。如果不传默认为 nopre。$pre 图片的key前缀。方便后面归类
     * @param $suffix 后缀。如果有需要的话、就传进来、默认没有
     */
    public static function upLocalFile($file, $pre = 'nopre', $suffix = '') {
        $bucket = self::$_BUCKET;
        $key1 = $pre . "-" . time() . "-" . rand(1000, 9999) . $suffix;
        Qiniu_SetKeys(self::$_ACCESS_KEY, self::$_SECRET_KEY);
        
        $putPolicy = new Qiniu_RS_PutPolicy($bucket);
        $upToken = $putPolicy->Token(null);
        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        list($ret, $err) = Qiniu_PutFile($upToken, $key1, $file, $putExtra);
        if ($err !== null) {
            $errorInfo = get_object_vars($err);
            throw new Exception($errorInfo['Err']);
        } else {
            return $ret;
        }
    }
}