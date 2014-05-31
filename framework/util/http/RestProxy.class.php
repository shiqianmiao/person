<?php
/**
 * 调用REST服务
 *
 * @author liubijian <liubj@273.cn>
 *
 */
class RestProxy {
    /**
     * 五种请求方式
     *
     * @var string
     */
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_LIST   = 'LIST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * 对数组进行编码
     *
     * @param array $args
     */
    public static function encodeArray($args) {
        $out = '';
        foreach ($args as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    if (isset($val)) {
                        $out .= "{$name}[{$key}]=" . urlencode($val) . "&";
                    }
                }
            } else if (isset($value)) {
                $out .= urlencode($name) . '=' . urlencode($value) . '&';
            }
        }
        $out = substr($out, 0, -1);
        return $out;
    }

    /**
     * 调用REST服务方法
     *
     * @param string $method 调用方法，'GET' 'POST' 'DELETE' 'PUT' 'LIST'等
     * @param string $host 主机
     * @param string $resource 资源名
     * @param array $params 输入参数
     * @param string $contentType 返回内容类型
     * @param int $connectTimeout 连接超时（单位：秒）
     * @param int $readTimeout 调用超时（单位：秒）
     * @return depend on the content type if success, FALSE if failed
     */
    public static function callMethod($method, $host, $resource, $params, $contentType = "json", $connectTimeout = 5, $readTimeout = 5) {
        //初始化curl
        $ch = curl_init();

        //设置连接超时和读取超时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $readTimeout);

        //这个选项是为了防止lighttpd返回417错误而加的。
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

        //设置请求参数
        $resource .= ".{$contentType}";
        $params['_method'] = $method;
        $str_params = self::encodeArray($params);
        if ($method == self::METHOD_POST || $method == self::METHOD_PUT || $method == self::METHOD_DELETE) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_POST);
            curl_setopt($ch, CURLOPT_URL, "http://{$host}/{$resource}");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
        } else if ($method == self::METHOD_GET || $method == self::METHOD_LIST) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_GET);
            curl_setopt($ch, CURLOPT_URL, "http://{$host}/{$resource}?{$str_params}");
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        if ($output === FALSE) {
            // @todo 增加报警
            self::warning($ch,$host);
        } else if ($contentType == 'json') {
            $output = json_decode($output, true);
        }
        curl_close($ch);

        return $output;
    }
    
    /**
     * 报警代码
     * @param type $ch
     */
    public static function warning($ch,$host) {
        // 错误明细
        $errlog = date('Y-m-d H:i:s') . ' ' . curl_error($ch) .' '.$host. "\n";
        $fp = fopen(dirname(__FILE__) . '/curl_error.log', 'a+');
        fputs($fp, $errlog);
        fclose($fp);
        return; 
        // 出错次数记录  记录文件内容为：时间 次数
        $errlogtimeFile = dirname(__FILE__) . '/curl_error.time';
        $fc = @file_get_contents($errlogtimeFile);
        if ($fc) {
            $ts = explode(' ', $fc);
            if ((time() - $ts[0]) <= 1800) {//半小时内
                $time = $ts[1] + 1;
                if ($time >= 10) {//超出10次
                    self::sendSMS();
                }
                $fc = $ts[0] . ' ' . $time;
            } else {
                $fc = time() . ' 1';
            }
        } else {
            $fc = time() . ' 1';
        }
        $fp = fopen($errlogtimeFile, 'w');
        fputs($fp, $fc);
        fclose($fp);
    }
    
    /**
     * 发短信通知
     */
    public static function sendSMS() {
        $sms = ModelNewSMS::getInstance();
        $mobiles = array('13960864746' ,'18612345273','13015728081','18611580518'); //
        foreach ($mobiles as $mobile) {
            $sms->sendSMS(array(
                        'Mobiles' => $mobile,
                        'Msg'     => '[报警]电话转接绑定号码失败次数已超过10次',
                    ));
        }
    }
}
