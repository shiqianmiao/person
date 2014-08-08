<?php

/**
 * @brief sso外部接口，sso验证通过http请求，该接口对http请求的操作进行封装，方便调用
 * @author aozhongxu
 */

require_once FRAMEWORK_PATH . '/util/common/Util.class.php';
require_once API_PATH . '/impl/sso/SsoImpl.class.php';

class SsoInterface {
    
    /**
     * @return array
     */
    public static function getUserInfo($params) {
        return SsoImpl::getUserInfo($params['passport']);
    }
    
    /**
     * @return array
     */
    public static function getAllCode($params) {
        return SsoImpl::getAllCode(
            Util::getFromArray('app', $params),
            Util::getFromArray('passport', $params)
        );
    }
    
    /**
     * @return array
     */
    public static function getInfoAndCode($params) {
        return SsoImpl::getInfoAndCode(
            Util::getFromArray('app', $params),
            Util::getFromArray('passport', $params)
        );
    }

    /**
     * @return boolean
     */
    public static function checkCode($params) {
        return SsoImpl::checkCode(
            Util::getFromArray('code', $params), 
            Util::getFromArray('app', $params),
            Util::getFromArray('passport', $params)
        );
    }
    
    /**
     * @return boolean
     */
    private static function checkUser($params) {
        return SsoImpl::checkUser($params['passport']);
    }
    
    /**
     * @brief 如果已经登录，则不进行操作，否则跳转到登录页
     */
    public static function login($params) {
        return SsoImpl::login($params['passport']);
    }

    public static function getLoginUrl($params = array()) {
        return SsoImpl::getLoginUrl();
    }
    
    /**
     * @brief logout是手动的，所以返url
     */
    public static function getLogoutUrl($params = array()) {
        return SsoImpl::getLogoutUrl();
    }
    /**
     *@breif 获取passport
     */
    public static function getPassport($params) {
       return SsoImpl::getPassport(
            $params['account_id'],
            $params['passwd'],
            Util::getFromArray('create_time', $params, 0),
            Util::getFromArray('check_time', $params, 0)
       );
    } 
    /**
     *@breif 解析passport
     */
    public static function parsePassport($params) {
       return SsoImpl::parsePassport($params['passport']);
    } 
    /**
     *@breif 登陆并拿到passport
     */
    public static function loginAndPassport($params) {
        return SsoImpl::loginAndPassport($params['account_id'], $params['passwd']);
    }
    /**
     *通过passport获取allUserInfo(包括用户信息，用户权限信息)
     */
    public static function getAllUserInfo($params) {
        return SsoImpl::getAllUserInfo($params['app'], $params['passport']);
    }
    
    /**
     * $brief 取得修改密码的url地址
     * @param array $params
     * @return string
     */
    public static function getChangePwdUrl($params = array()) {
        return SsoImpl::getChangePwdUrl();
    }
    
    /**
     * $brief 取得个人中心的url地址
     * @param array $params
     * @return string
     */
    public static function getProfileUrl($params = array()) {
        return SsoImpl::getProfileUrl();
    }
    /**
     * @brief 验证密码正确性
     * @param array $params
     */
    public static function checkPwd($params) {
        return SsoImpl::checkPwd($params['account_id'], $params['passwd']);
    }
    
    /**
     * @brief 修改密码
     * @param array $params
     */
    public static function changePwd($params) {
        return SsoImpl::changePwd($params['account_id'],$params['new_passwd']);
    }
    /**
     * @brief 添加人员到组
     * @param array $params
     */
    public static function addUserToGroup($params) {
        return SsoImpl::addUserToGroup($params['user_id'], $params['code_array']);
    }
    /**
     * @brief 修改人员所在组
     * @param array $params
     */
    public static function updateUserFromGroup($params) {
        return SsoImpl::updateUserFromGroup($params['user_id'], $params['code_array']);
    }
    /**
     * @brief 获取所有组
     * @param array $params
     */
    public static function getAllGroup() {
        return SsoImpl::getAllGroup();
    }
    /**
     * @brief 获取用户所有组
     * @param array $params
     */
    public static function getGroupByUser($params) {
        if (empty($params['field'])) {
            $params['field'] = '*';
        }
        return SsoImpl::getGroupByUser($params['user_id'], $params['field']);
    }
}
