<?php 
/**
 * @brief DES加密, 为了保证java和object-c 跨平台的兼容性必须是8位
 * @copyright
 * @author
 * @date
 * @version  1.0 
 *
 */
class DesEncryptAdapter {
    /**
     * @brief 密钥
     */
    private $_secret_key = '';

    public function __construct($key) {
        if(empty($key) || strlen($key) != 8) {
            throw new Exception(sprintf('fail, deskey非法 key=%s', $key));
        }
        $this->_secret_key = trim($key);
    }

    public function encrypt($string) {
        $ivArray=array(0x12, 0x34, 0x56, 0x78, 0x90, 0xAB, 0xCD, 0xEF);
        $iv = '';

        foreach ($ivArray as $element) {
            $iv .= chr($element);
        }

        $size   = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC );  
        $string = $this->_pkcs5Pad ( $string, $size );  

        $data =  mcrypt_encrypt(MCRYPT_DES, $this->_secret_key, $string, MCRYPT_MODE_CBC, $iv);
        return base64_encode($data);
    }

    public function decrypt($string) {
        $ivArray=array(0x12, 0x34, 0x56, 0x78, 0x90, 0xAB, 0xCD, 0xEF);
        $iv = '';
        foreach ($ivArray as $element) {
            $iv .= chr($element);
        }

        $string = base64_decode($string);
        $result = mcrypt_decrypt(MCRYPT_DES, $this->_secret_key, $string, MCRYPT_MODE_CBC, $iv);
        $result = $this->_pkcs5Unpad( $result );  
        return $result;
    }

    private function _pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen ( $text ) % $blocksize);  
        return $text . str_repeat ( chr ( $pad ), $pad );  
    }  
  
    private function _pkcs5Unpad($text) {  
        $pad = ord ( $text {strlen ( $text ) - 1} );  
        if ($pad > strlen ( $text ))  
            return false;  
        if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)  
            return false;  
        return substr ( $text, 0, - 1 * $pad );  
    }
}
