<?PHP
/**
 * @brief 加密类, DES 数据加密标准
 * @category   
 * @copyright
 * @author    
 * @date 
 * @version    1.0 
 *
 */
class DesUtil {
    /** AES */
    const MODE_AES = 1;

    /** DES */
    const MODE_DES = 2;

    /** 3DES */
    const MODE_3DES = 3;
    
    /** 3DES_ECB */
    const MODE_3DES_ECB = 4;
    
    private static $_HANDLE_ARRAY   = array();    

    private static function _getHandleKey($params) {
        ksort($params);
        return md5(implode('_' , $params));
    }

    /**
     * @brief 创建一个加密对象
     * @param integer $mode  加密的类型
     * @param string $secretKey 密钥
     * @param string $iv Des3EncryptAdapter 需要IV
     * @return  DesEncryptAdapter|AesEncryptAdapter|Des3EncryptAdapter
     */
    public static function create($mode, $secretKey, $iv = null) {
        $handle_key = self::_getHandleKey(array(
            'mode'      => $mode,
            'secretKey'   => $secretKey,
        ));

        if (isset(self::$_HANDLE_ARRAY[$handle_key])) {
            return self::$_HANDLE_ARRAY[$handle_key];
        }

        switch($mode) {
            case self::MODE_DES:
                require_once dirname(__FILE__) . '/adapter/DesEncryptAdapter.class.php';
                $objCache = new DesEncryptAdapter($secretKey);
                break;
            case self::MODE_3DES:
                require_once dirname(__FILE__) . '/adapter/Des3EncryptAdapter.class.php';
                $objCache = new Des3EncryptAdapter($secretKey, $iv);
                break;
            case self::MODE_3DES_ECB:
                require_once dirname(__FILE__) . '/adapter/Des3EncryptAdapter.class.php';
                $objCache = new Des3EncryptAdapter($secretKey, $iv, false);
                break;
            case self::MODE_AES:
                require_once dirname(__FILE__) . '/adapter/AesEncryptAdapter.class.php';
                $objCache = new AesEncryptAdapter($secretKey);
                break;
            default:
                require_once dirname(__FILE__) . '/adapter/AesEncryptAdapter.class.php';
                $objCache = new AesEncryptAdapter($secretKey);
                break;
        }

        self::$_HANDLE_ARRAY[$handle_key]    = $objCache;
        return $objCache;
    }
}
