<?php
/** 
 @ desc 截取字文字
 * @author   缪石乾<miaosq@273.cn>
 * @since    2013-4-15
 */

require_once FRAMEWORK_PATH . '/util/text/String.class.php' ;

function cuttostr_helper($params)
{
    $num = isset($params['num']) ? (int)$params['num'] : 45;
    if (String::strlen_utf8($params['desc']) > 0 && $num > 0) {
        $str = String::substr_utf8($params['desc'], $num, 0);
        if(String::strlen_utf8($params['desc']) > $num) {
            if ($params['notEnd']) {
                return $str;
            }
            $str .= '...';
        }
    }
    return $str;
}
