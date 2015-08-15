<?php

class Util {

    public static $SPIDER_HOSTS = array(
        'img1', 'img2', 'img3', 'img4'
    );

    public static function getFromArray($key, $array, $default = null, $notEmpty = false) {
        if (empty($array) || !array_key_exists($key, $array) || ($notEmpty && empty($array[$key]))) {
            return $default;
        }
        return $array[$key];
    }

    /**
     * 编码转接 支持数组
     * @param type $fContents
     * @param type $from
     * @param type $to
     * @return type
     */
    public static function convertEncoding($fContents, $from = 'gbk', $to = 'utf-8') {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
            //如果编码相同或者非字符串标量则不转换
            return $fContents;
        }
        if (is_string($fContents)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($fContents, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to, $fContents);
            } else {
                return $fContents;
            }
        } elseif (is_array($fContents)) {
            foreach ($fContents as $key => $val) {
                $_key = Util::convertEncoding($key, $from, $to);
                $fContents[$_key] = Util::convertEncoding($val, $from, $to);
                if ($key != $_key)
                    unset($fContents[$key]);
            }
            return $fContents;
        } else {
            return $fContents;
        }
    }

    public static function isDate($date) {
        return preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $date);
    }

    public static function cny($ns) {
        static $cnums = array("零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖"),
        $cnyunits = array("元", "角", "分"),
        $grees = array("拾", "佰", "仟", "万", "拾", "佰", "仟", "亿");
        list($ns1, $ns2) = explode(".", $ns, 2);
        $ns2 = array_filter(array($ns2[1], $ns2[0]));
        $ret = array_merge($ns2, array(implode("", self::_cny_map_unit(str_split
        ($ns1), $grees)), ""));
        $ret = implode("", array_reverse(self::_cny_map_unit($ret, $cnyunits)));
        return str_replace(array_keys($cnums), $cnums, $ret);
    }

    private static function  _cny_map_unit($list, $units) {
        $ul = count($units);
        $xs = array();
        foreach (array_reverse($list) as $x) {
            $l = count($xs);
            if ($x != "0" || !($l % 4))
                $n = ($x == '0' ? '' : $x) . ($units[($l - 1) % $ul]);
            else $n = is_numeric($xs[0][0]) ? $x : '';
            array_unshift($xs, $n);
        }
        return $xs;
    }


    /**
     * @brief 格式化图片
     * @param string $image 图片地址
     * @param array $option 配置参数
     */
    public static function formatImageUrl($image, $option) {
        $replacement = '';
        if ($image) {
            //图片宽度
            $replacement .= '_' . $option['width'];
            //图片高度
            $replacement .= '-' . $option['height'];
            //图片是否剪裁
            if ($option['cut']) {
                $replacement .= 'c';
            }
            //图片质量
            $replacement .= '_' . $option['quality'];
            //是否锐化图片,1锐化，0不锐化
            $replacement .= '_' . $option['mark'];
            //版本号
            $replacement .= '_' . $option['version'];
            $pattern = "/(|_\d+-\d+[c|f]?_\d_\d_\d)(.[a-z]{3,})$/i";
            $replacement .= "\$2";
            $image = str_replace('_min', '', $image);
            $image = preg_replace($pattern, $replacement, $image);
            //字符串包含AD时，是否替换/AD/ 或/AD/AD/两种情况
            if ($option['replace_ad']) {
                if (strpos($image, '/AD/') !== false) {
                    $image = str_replace('/AD/', '/CAD/', $image);
                    if (strpos($image, '/AD/') !== false) {
                        $image = str_replace('/AD/', '/CAD/', $image);
                    }
                }
            }
        }
        if (strpos($image, 'http://') === false) {
            $image = 'http://img.273.com.cn/' . $image;
        }
        return $image;
    }

    public static function getNoWaterImageUrl($image) {
        $image = str_replace('http://img.273.com.cn/', '', $image);
        $time = time();
        $token = md5('/' . $image . $time . 'M)!=tOz2)G7h$3x&yFMX539WjPYc#_UM');
        return $image . '?token=' . $token . '&time=' . $time;
    }

    public static function formatSpiderImage($path, $options) {
        $replacement = '';
        $hash = crc32($path);
        if ($path) {
            //图片宽度
            $replacement .= '_' . $options['width'];
            //图片高度
            $replacement .= '-' . $options['height'];
            //图片是否剪裁
            if ($options['cut']) {
                $replacement .= 'c';
            }
            //图片质量
            $replacement .= '_' . $options['quality'];
            //是否锐化图片,1锐化，0不锐化
            $replacement .= '_' . ($options['mark'] ? 1 : 0);
            //版本号
            $replacement .= '_' . ($options['version'] ? $options['version'] : 0);
            $pattern = "/(|_\d+-\d+[c|f]?_\d_\d_\d)(.[a-z]{3,})$/i";
            $replacement .= "\$2";
            $path = str_replace('_min', '', $path);
            $path = preg_replace($pattern, $replacement, $path);
        }
        if (strpos($path, 'http://') === false) {
            $host = self::$SPIDER_HOSTS[$hash % count(self::$SPIDER_HOSTS)];
            $host = $host ? $host : 'img1';
            $path = 'http://' . $host . '.273.com.cn/' . $path;
        }
        return $path;
    }

    /**
     * @desc 根据车况事故项生成车况展示图片
     * @param $ckParams car_sale表中的condition_detai字段，需反序列化
     */
    public static function getCkImageUrl($ckParams) {
        $host = 'http://img.273.com.cn/ckbimg/';
        $url = $host . 'good.jpg';
        if (!empty($ckParams)) {
            include_once dirname(__FILE__) . '/../../../conf/common/check/CheckVars.class.php';
            $badItems = array();
            foreach (CheckVars::$FRAME_P2P as $key => $value) {
                if (in_array($value, $ckParams)) {
                    $badItems[] = $key;
                }
            }
            if (!empty($badItems)) {
                $badItems = implode('_', $badItems);
                $url = $host . $badItems . '.jpg';
            }
        }
        return $url;
    }

    /**
     * @desc 判断uuid是否合法
     * 规则根据js中的uuid生成规则反解析
     * @param uuid
     * @return true/false
     * @refer /static/util/uuid_v2.js
     */
    public static function isValidUuid($uuid) {
        //随机数位数
        $randomLen = 8;
        $len = strlen($uuid);
        $ret = false;
        do {
            //目前uuid必须是22位
            if ($len != 22) {
                break;
            }
            $random = substr($uuid, -$randomLen);
            $random = intval($random);
            // 10000000<$random<99999999
            if ($random < 10000000) {
                break;
            }
            $remain = substr($uuid, 0, $len - $randomLen);
            $remain = $remain - $random;
            //反转字符串
            $remain = strrev($remain);
            //得到的是毫秒时间戳，且包含1位随机数
            $remain = substr(0, -1);
            $time = $remain / 1000;
            //时间戳必定小于当前时间戳
            if ($time < time()) {
                $ret = true;
            }
        } while (false);
        return $ret;
    }
}
