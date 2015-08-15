<?php 
/**
 * @brief DES3加密, key必须是8位整倍数， 如果不足作补充
 * @copyright
 * @author
 * @date
 * @version 1.0 
 *
 */
class Des3EncryptAdapter {
    /**
     * @brief 密钥
     */
    private $_secret_key = '';

    private $_iv="s(2L@f!a";
    

    public function __construct($key, $iv = null, $padding = true) {
        if(empty($key)) {
            throw new Exception(sprintf('fail, 3deskey非法 key=%s', $key));
        }
        if(!empty($iv)) {
            $this->_iv = $iv;
        }
        if ($padding) {
            $this->_secret_key = $this->_padding(trim($key));
        } else {
            $this->_secret_key = $key;
        }
    }
    public function encrypt($string){
        $td = mcrypt_module_open( MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        //使用MCRYPT_3DES算法,cbc模式
        mcrypt_generic_init($td, $this->_secret_key, $this->_iv);
        //初始处理
        $data = mcrypt_generic($td, $string);
        //加密
        mcrypt_generic_deinit($td);
        //结束
        mcrypt_module_close($td);
//        return bin2hex(base64_encode($data));
        return $this->_removeBr(base64_encode($data));
    }

    public function decrypt($encrypted) {
//        $encrypted   = pack("H*",$encrypted);
        $encrypted = base64_decode($encrypted);

        $td = mcrypt_module_open( MCRYPT_3DES,'',MCRYPT_MODE_CBC,'');
        //使用MCRYPT_3DES算法,cbc模式
        mcrypt_generic_init($td, $this->_secret_key, $this->_iv);
        //初始处理
        $decrypted = mdecrypt_generic($td, $encrypted);
        //解密
        mcrypt_generic_deinit($td);
        //结束
        mcrypt_module_close($td);
        $decrypted = $this->_removePadding($decrypted);
        return $decrypted;
    }
    
    public function decryptEcb($encrypted) {
        $key = $this->_secret_key . substr($this->_secret_key, 0, 16);
        $key = pack('H48', $key);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        $iv = pack('H*','0000000000000000');
        mcrypt_generic_init($td, $key, $iv);
        $ret = mdecrypt_generic($td, pack('H*', $encrypted));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return preg_replace("/\}.*$/s", "}", $ret);
    }
    
    public function encryptEcb($string) {
        $key = $this->_secret_key . substr($this->_secret_key, 0, 16);
        $key = pack('H48', $key);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        $iv = pack('H*','0000000000000000');
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return bin2hex($encrypted_data); // 转化成16进制字符串
    }
    
    //填充密码，填充至8的倍数
    private function _padding( $str ) {
        $len = 8 - strlen( $str ) % 8;
        for ( $i = 0; $i < $len; $i++ ) {
            $str .= chr( 0 );
        }
        return $str ;
    }
    
    //删除回车和换行
    private function _removeBr($str) {
        $len = strlen( $str );
        $newstr = "";
        $str = str_split($str);
        for ($i = 0; $i < $len; $i++ ) {
            if ($str[$i] != '\n' and $str[$i] != '\r') {
                $newstr .= $str[$i];
            }
        }
        return $newstr;
    }

    //删除填充符
    private function _removePadding( $str ) {
        $len = strlen( $str );
        $newstr = "";
        $str = str_split($str);
        for ($i = 0; $i < $len; $i++ )
        {
            if ($str[$i] != chr( 0 ))
            {
                $newstr .= $str[$i];
            }
        }
        return $newstr;
    }
}
