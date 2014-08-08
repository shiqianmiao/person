<?php

/**
 * @brief sso外部接口，sso验证通过http请求，该接口对http请求的操作进行封装，方便调用
 * @author aozhongxu
 */

require_once FRAMEWORK_PATH . '/util/http/HttpRequest.class.php';
require_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';
require_once API_PATH . '/model/SsoGroupModel.class.php';
require_once API_PATH . '/model/SsoGUModel.class.php'; 

class SsoImpl {
    
    /**
     * @return array
     */
    public static function getUserInfo($passport) {
        $ret = HttpRequest::curlPost('http://sso.corp.273.cn/account/getUserInfo', array(
            'm_AUTH_STRING' => $passport,
        ), 20);
        return (array) json_decode($ret);
    }
    
    /**
     * @return array
     */
    public static function getAllCode($app, $passport) {
        if (empty($app)) {
            return false;
        }
        $ret = HttpRequest::curlPost('http://sso.corp.273.cn/account/getAllCode', array(
            'app' => $app,
            'm_AUTH_STRING' => $passport,
        ), 20);
        return (array) json_decode($ret);
    }

    /**
     * @return array
     */
    public static function getInfoAndCode($app, $passport) {
        if (empty($app)) {
            return false;
        }
        $ret = HttpRequest::curlPost('http://sso.corp.273.cn/account/getInfoAndCode', array(
            'app' => $app,
            'm_AUTH_STRING' => $passport,
            ), 20);
        return (array) json_decode($ret, true);
    }

    /**
     * @return boolean
     */
    public static function checkCode($code, $app, $passport) {
        $ret = HttpRequest::curlPost('http://sso.corp.273.cn/account/checkCode', array(
            'code' => $code,
            'app' => $app,
            'm_AUTH_STRING' => $passport,
        ), 20);
        return json_decode($ret);
    }
    
    /**
     * @return boolean
     */
    private static function checkUser($passport) {
        $ret = HttpRequest::curlPost('http://sso.corp.273.cn/account/checkUser', array(
            'm_AUTH_STRING' => $passport,
        ), 20);
        return json_decode($ret);
    }
    
    /**
     * @brief 如果已经登录，则不进行操作，否则跳转到登录页
     */
    public static function login($passport) {
        if (!empty($passport) && self::checkUser($passport)) {
            return true;
        }
        return false;
    }

    public static function getLoginUrl() {
        $refer = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        return 'http://sso.corp.273.cn/account/login/?refer=' . $refer;
    }
    
    /**
     * @brief logout是手动的，所以返url
     */
    public static function getLogoutUrl() {
        return 'http://sso.corp.273.cn/account/logout/';
    }
    /**
     *@breif 获取passport
     */
    public static function getPassport($accountId, $passwd, $createTime, $checkTime) {
        include_once FRAMEWORK_PATH . '/util/des/DesUtil.class.php';
        $key = '1d387351b3023e0e';
        $iv = 'dfh^&(89';
        $passwd = substr($passwd, 0, 16);
        if (!$createTime) {
            $createTime = time();
        }
        if (!$checkTime) {
            $checkTime = $createTime;
        }
        $text = $accountId . '-' . $passwd . '-' . $createTime . '-' . $checkTime; 
        $encrypt = DesUtil::create(DesUtil::MODE_3DES, $key, $iv);
        return urlencode($encrypt->encrypt($text));
    }
    /**
     *@breif 解析passport
     */
    public static function parsePassport($passport) {
        $passport = urldecode($passport);
        include_once FRAMEWORK_PATH . '/util/des/DesUtil.class.php';
        $key = '1d387351b3023e0e';
        $iv = 'dfh^&(89';
        $encrypt = DesUtil::create(DesUtil::MODE_3DES, $key, $iv);
        $text = $encrypt->decrypt($passport);
        return explode('-', $text);
    }

    /**
     *@breif 登陆并拿到passport
     */
    public static function loginAndPassport($accountId, $passwd) {
        include_once API_PATH . '/model/MbsUserModel.class.php';
        $userModel = new MbsUserModel();
        $userInfo = $userModel->getUserInfoWhenLogin('*', $accountId, $passwd);
        if (empty($userInfo)) {
            $content = $accountId .'_'.$passwd;
            LoggerGearman::logInfo(array('data'=>$content, 'identity'=>'login.getUserInfo'));
            return false;
        } else {
            $passport = self::getPassport($accountId, $passwd, 0, 0);
            if(empty($passport)) {
                $content = $accountId .'_'.$passwd;
                LoggerGearman::logInfo(array('data'=>$content, 'identity'=>'login.getPassport'));
            }
            return $passport;
        }
    }
    /**
     *通过passport获取allUserInfo(包括用户信息，用户权限信息,通过memcache做缓存)
     */
    public static function getAllUserInfo($app, $passport) {
        $key = $app . '-user-info-' . $passport;
        include_once FRAMEWORK_PATH . '/util/cache/CacheNamespace.class.php';
        include_once CONF_PATH . '/cache/MemcacheConfig.class.php';
        $handle = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE, MemcacheConfig::$GROUP_DEFAULT);
        $userInfo = $handle->read($key);
        if ($userInfo === false || $userInfo === null) {
            $userInfo = array();
            $userInfo['user'] = self::getUserInfo($passport);
            $userInfo['Permisssions'] = self::getAllCode($app, $passport);
            $userInfo['Permisssions'] = array_flip($userInfo['Permisssions']);
            $handle->write($key, $userInfo, 3600);
        }
        return $userInfo;
    }
    
    /**
     * @brief 取得修改密码的url地址
     * @return string
     */
    public static function getChangePwdUrl() {
        return 'http://sso.corp.273.cn/Account/changePwd/';
    }
    
    /**
     * @brief 验证密码正确性
     * @param int $accoutId  用户名
     * @param string $passwd 密码
     * @return boolean
     */
    public static function checkPwd($accoutId, $passwd) {
        include_once API_PATH . '/model/MbsUserModel.class.php';
        $userModel = new MbsUserModel();
        $userInfo = $userModel->checkPwd('*', $accoutId, $passwd);
        if (empty($userInfo)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * @brief 修改密码
     * @param int $accoutId  用户名
     * @param string $passwd 新密码
     * @return boolean
     */
    public static function changePwd($accoutId, $passwd) {
        include_once API_PATH . '/model/MbsUserModel.class.php';
        $userModel = new MbsUserModel();
        $userInfo = $userModel->changePwd($accoutId, $passwd);
        if (empty($userInfo)) {
            return false;
        } else {
            return true;
        }
    }
    //添加人员到组
    public static function addUserToGroup($userId, $codeArray) {
        $groupModel = new SsoGroupModel2();
        $guModel = new SsoGUModel2();
        foreach ($codeArray as $code) {
            $groupId = $groupModel->getOne('id', array(array('code', '=', $code)));
            $info['group_id'] = $groupId;
            $info['user_id'] = $userId;
            $guModel->insert($info);
        }
    }
    //修改人员所在的组
    public static function updateUserFromGroup($userId, $codeArray) {
        $groupModel = new SsoGroupModel2();
        $guModel = new SsoGUModel2();
        $ret = $guModel->delete(array(array('user_id', '=', $userId)));
        if ($ret) {
            foreach ($codeArray as $code) {
                $groupId = $groupModel->getOne('id', array(array('code', '=', $code)));
                $info['group_id'] = $groupId;
                $info['user_id'] = $userId;
                $guModel->insert($info);
            }
        }
    }
    //获取所有的组
    public static function getAllGroup() {
        $groupModel = new SsoGroupModel2();
        return $groupModel->getAll('*');
    }
    //获取用户所有的组
    public static function getGroupByUser($userId, $field = '*') {
        if (empty($userId)) {
            return array();
        }
        $groupModel = new SsoGroupModel2();
        $guModel = new SsoGUModel2();
        $idArray = $guModel->getAll('group_id', array(array('user_id', '=', $userId)));
        if (empty($idArray)) {
            return array();
        }
        $row = array();
        foreach ($idArray as $id) {
            $row[] = $id['group_id'];
        }
        $ret = $groupModel->getAll($field, array(array('id', 'in', $row)));
        return $ret;
    }
}
