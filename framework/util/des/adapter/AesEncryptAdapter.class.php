<?php 
/**
 * @brief AES加密
 * @category   
 * @copyright 
 * @author 
 * @date
 * @version  1.0 
 *
 */
class AesEncryptAdapter {
    /**
     * @brief 加密
     */
    const ENCODE = 1;

    /**
     * @brief 加密
     */
    const DECODE = 2;

    /**
     * @brief 当前TYPE
     */
    private static $_TYPE = 0;

    
    /**
     * @brief 密钥
     */
    private $_secret_key = '';

    public function __construct($key) {
        if(empty($key)) {
            throw new Exception(sprintf('fail, aeskey非法 key=%s', $key));
        }
        $this->_secret_key = trim($key);
    }

    /**
     * @brief 加密字符串
     * @param $str
     * @param $this->_secret_key
     * @return see _get()
     */
    public function encrypt($str) {
        self::$_TYPE = self::ENCODE;
        return self::_get($str);
    }

    /**
     * @brief 解密字符串
     * @param string $str
     * @param string $this->_secret_key 加密key
     * @return see _get()
     */
    public function decrypt($str) {
        self::$_TYPE = self::DECODE;
        return self::_get($str);
    }
    
    /**
     * @brief 获取key 
     * @param string $str
     * @return string 
     *   - 空字符串 
     *   - 有效的字符串
     */
    private function _get($str) {
        $str        = (string) $str;
        
        if (strlen($this->_secret_key) < 1) {
            return '';
        }
        
        $key        = md5($this->_secret_key);
        $key_length = strlen( $key );

        $str = self::$_TYPE == self::DECODE 
             ? base64_decode( $str ) 
             : substr( md5( $str . $key ), 0, 8 ) . $str;
        $str_length = strlen( $str );

        $rndkey = $box = array ();

        for ($i = 0; $i <= 255; $i ++) {
            $rndkey[$i] = ord( $key[$i % $key_length] );
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i ++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        $result = '';

        for ($a = $j = $i = 0; $i < $str_length; $i ++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr( ord( $str[$i] ) ^ ($box[($box[$a] + $box[$j]) % 256]) );
        }

        if (self::$_TYPE == self::DECODE) {
            if (substr( $result, 0, 8 ) == substr( md5( substr( $result, 8 ) . $key ), 0, 8 )) {
                return substr( $result, 8 );
            } else {
                return '';
            }
        } else {
            return str_replace( '=', '', base64_encode( $result ) );
        }
    }
}
