<?php
require_once dirname(__FILE__) . '/../../../conf/config.inc.php';
require_once FRAMEWORK_PATH . '/util/image/Image.class.php';
require_once FRAMEWORK_PATH . '/util/common/Session.class.php';

/**
 * 图片验证码
 * @author 陈朝阳<chency@273.cn>
 * @since 2014-8-25
 * 
 */
class ImageCode {
    public static $CODE_NAME_LIST = array(
        'web_image_code'
    );
    /**
     * @desc 获取图片验证码
     * @param $param array(
     *                  [必填]'code_name'    //对应session中的key，需在$CODE_NAME_LIST中注册
     *                  [可选]'image_width'  //图片长，默认60，则表示60px
     *                  [可选]'image_height' //图片高，默认20，则表示20px
     *                  [可选]'code_length'  //验证码位数，默认4，则表示4位验证码
     *                  [可选]'code_type'    //验证码类型，默认2，0=数字,1=字母,2=数字加字母
     *                  [可选]'text_color'   //文字颜色
     *                  [可选]'bg_color'     //背景颜色
     *               )
     * @return Image实例，显示图像调用display()方法，$image->display()
     */
    public static function getCode($param) {
        $codeName = $param['code_name'];
        if (!in_array($codeName, self::$CODE_NAME_LIST)) {
            return false;
        }
        $imageParam['image_width']  = $param['image_width'] > 0 ? $param['image_width'] : 60;
        $imageParam['image_height'] = $param['image_height'] > 0 ? $param['image_height'] : 20;
        $imageParam['code_length']  = $param['code_length'] > 0 ? $param['code_length'] : 4;
        $imageParam['code_type']    = isset($param['code_type']) && $param['code_type'] >= 0 ? $param['code_type'] : 2;
        $imageParam['text_color']   = $param['text_color'] ? $param['text_color'] : '#3e3e3e';
        $imageParam['bg_color']     = $param['bg_color'] ? $param['bg_color'] : '#B6B6B6';
        $image = new Image();
        $value = $image->imageValidate(
            $imageParam['image_width'],
            $imageParam['image_height'],
            $imageParam['code_length'],
            $imageParam['code_type'],
            $imageParam['text_color'],
            $imageParam['bg_color']
        );
        $sessionKey = self::getSessionKey($codeName);
        Session::setValue($sessionKey, md5(strtolower($value)));
        return $image;
    }
    
    /**
     * @desc 验证图片验证码
     * @return Image实例，显示图像调用display()方法，$image->display()
     */
    public static function checkCode($codeName, $value) {
        if (empty($codeName) || empty($value)) {
            return false;
        }
        $sessionKey = self::getSessionKey($codeName);
        $sessionValue = Session::getValue($sessionKey);
        if ($sessionValue == md5(strtolower($value))) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @desc 获取加密后的session key
     */
    public static function getSessionKey($codeName) {
        $codeName = '273_imagecode_' . $codeName;
        return md5($codeName);
    }
}
