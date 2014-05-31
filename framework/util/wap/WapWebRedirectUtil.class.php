<?php 
require_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';
require_once WEB_V3 . '/common/CityRedirect.class.php';
/**
 *
 * @author    chenchaoyang <chency@273.cn>
 * @since     2013-8-1
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc      wap跳到主站
 */

class WapWebRedirectUtil {
    
    const COOKIE_WAP2WEB = 'wap2web';
    const COOKIE_DOMAIN = '273.cn';
    /**
     * @brief web自动跳转到wap
     */
    public static function webToWap() {
        $httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
        $isMobile = false;
        //如果有$_SERVER['HTTP_X_WAP_PROFILE']则是手机
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $isMobile = true;
        }
        // 通过UA判断是否手机
        if (!$isMobile) {
            $isJump = self::uaAutoJumpWap($httpUserAgent);
            $isMobile = $isJump;
        }
        
        //不是手机，访问电脑版
        if (!$isMobile) {
            return;
        }
        // 获取跳转的url
        $url = RequestUtil::getCurrentUrl();
        $rurl = self::getWebToWapUrl($url);
        if ($rurl) {
            ResponseUtil::redirect($rurl);
        } else {
            LoggerGearman::logInfo(array('data' => sprintf('getwapurl fail, url=%s, ua=%s', $url, $httpUserAgent), 'identity' => 'wapurl'));
        }
    }

    /**
     * @brief wap 访问电脑版
     */
    public static function wapToWeb() {
        // 设置cookie
        self::setWapToWeb();
        $url = RequestUtil::getGET('curl');
        if ($url) {
            if (strpos($url, 'seven')) {
                $url = 'http://www.273.cn/static/seven.html';
            } else if(strpos($url, 'search')) {
                preg_match('/^http:\/\/(.*)\.m\.273\.cn/is', $url, $_dmt);
                if ($_dmt[1]) {
                    $url = "http://{$_dmt[1]}.273.cn/sale/";
                } else {
                    $url = 'http://www.273.cn/sale/';
                }
            } else {
                preg_match('/^http:\/\/(.*)\.m\.273\.cn/is', $url, $_dmt);
                if ($_dmt[1]) {
                    $url = str_replace($_dmt[1] . '.m.273.cn', $_dmt[1] . '.273.cn', $url);
                } else {
                    $url = str_replace('m.273.cn', 'www.273.cn', $url);
                }
            }
        } else {
            $url = 'http://www.273.cn/';
        }
        ResponseUtil::redirect($url);
    }

    /**
     * @brief 获取web跳转到wap的url
     * @param string $url
     * @return mix
     *  -false 获取失败
     *  -string url
     */
    public static function getWebToWapUrl($url) {
        if(!preg_match("/(\w+)\.273\.cn/is", $url, $match)) {
            return false;
        }
        $domain = (isset($match[1]) && $match[1] != 'www') ? $match[1] : '';
        
        if (empty($domain)) {
            $host = 'm.273.cn';
        } else {
            $newDomain = $domain;
            if (key_exists($domain, CityRedirect::$OLD2NEW)) {
                $newDomain = CityRedirect::$OLD2NEW[$domain];
            }
            $host = $newDomain . '.m.273.cn';
        }
        
        $url = preg_replace("/(\w+)\.273\.cn/is", $host ,$url);
        return $url;
    }
    
    /**
     * @brief 根据 ua判断是否需要自动跳转
     * @param string $httpUserAgent ua
     * @param string $uri REQUEST_URI
     * @return boolean
     */
    public static function uaAutoJumpWap($httpUserAgent = '', $uri = '') {
        // 如果是来自wap访问电脑版, 不在跳转
        if(self::isFromWap()) {
            return false;
        }

        $redirWap = false;
        $httpUserAgent = $httpUserAgent ? $httpUserAgent : $_SERVER['HTTP_USER_AGENT'];

        if (preg_match("/.*baidu\s+Transcoder.*|.*MAUIWAPBrowser.*|.*MAUI[_| ]+WAP[_| ]+Browser.*|.*Smartphone.*|.*Motorola.*|.*Symbian.*|.*OperaMini.*|.*Palm.*|.*SonyEricsson.*|.*Nokia.*|.*iPod.*|.*iPhone.*|.*MIDP.*CLDC.*|.*Mobile.*|.*Android.*|.*webOS.*|.*BREW.*|.*HTC.*|.*UCWEB.*|.*MQQBrowser.*|.*JUC.*|.*ZTE-Me.*|.*UNTRUSTED.*|.*UP.Browse.*|.*BREW-Applet.*|.*wpos.*/is", $httpUserAgent)) {
            $redirWap =  true;
        }

        // 是否支持wap
        if (!$redirWap) {
            if ($httpUserAgent == 'Java/1.7.0_02') {
                // 不需要判断忽略的一些 ua
            } else {
                if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$httpUserAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($httpUserAgent,0,4))) {
                    LoggerGearman::logInfo(array('data' => sprintf('is_wap fail, ua=%s', $httpUserAgent), 'identity' => 'is_wap'));
                }
            }
        }

        if(strpos($httpUserAgent, 'iPad') !== false) {
            $redirWap = false;
        }

        return $redirWap;
    }
    
    /**
     * @brief 是否来自wap
     * @return boolean true|是, false|不是
     */
    public static function isFromWap() {
        return isset($_COOKIE[self::COOKIE_WAP2WEB]) ? true : false;
    }
    
    /**
     * @brief 设置wap跳转到web的cookie
     * @return boolean
     */
    public static function setWapToWeb() {
        // 设置cookie
        $expireTime = time() + 3600;
        setcookie(self::COOKIE_WAP2WEB, 1, $expireTime, '/', self::COOKIE_DOMAIN);
        return true;
    }
    
}