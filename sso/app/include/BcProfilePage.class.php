<?php

require_once FRAMEWORK_PATH . '/util/common/Session.class.php' ;
require_once API_PATH . '/interface/SsoInterface.class.php';
require_once API_PATH . '/interface/MbsUserInterface.class.php';

/**
 * @author chenhan <chenhan@273.cn>
 */

class BcProfilePage extends Page {
    
    protected static $_COOKIE_NAME = 'back_code';
    /**
     * 用户个人信息
     * @var array
     */
    public $userInfo;
    
    /**
     * @brief 通过cookie取得用户的完整信息
     */
    public function getUserInfo() {
        if (!empty($_COOKIE['m_AUTH_STRING'])) {
            $sessUesrInfo = Session::getValue('backend-user-info');
            if (!empty($sessUesrInfo) && $_COOKIE['m_AUTH_STRING'] == $sessUesrInfo['cookie']) {
                $this->userInfo = $sessUesrInfo;
            } else {
                $this->userInfo = SsoInterface::getUserInfo(array('passport' => $_COOKIE['m_AUTH_STRING']));
            }
        }
        //如果未登录则跳转到业管主页
        if (empty($this->userInfo)) {
            header('Location: http://mbs.corp.273.cn/');
        }
    }
    
    /**
     * @brief 显示验证码
     */
    public function showImgAction(){
        $cookieName = self::$_COOKIE_NAME;
        $image = new Image();
        define('VALID_WIDTH',86);
        define('VALID_HEIGHT',19);
        define('VALID_CODE_TYPE', 3);
        define('VALID_CODE_LENGTH', 4);
        $valid = $image->imageValidate(VALID_WIDTH, VALID_HEIGHT,
                VALID_CODE_LENGTH, VALID_CODE_TYPE,'#3e3e3e','#B6B6B6');
    
        setcookie($cookieName, md5(strtolower($valid)), 0, '/');
        header("Pragma: no-cache");
        header("Cache-control: no-cache");
        $image->display();
    }
    
    /**
     * @brief 通过id取得用户的完整信息
     */
    protected function _getUserInfo() {
        $params = array();
        $params['id'] = $this->userInfo['id'];
        $this->userInfo = MbsUserInterface::getInfoByIdForOracle($params);
    }
    
    /**
     * @brief 生成操作码
     */
    protected function _getOpCode() {
        $lifeTime = 60 * 10;  // 保存10分钟
        session_set_cookie_params($lifeTime);
        session_start();
        $opCode = md5(time()+rand(10000, 90000));
        $_SESSION[$opCode] = true;
        return $opCode;
    }
    
    /**
     * @brief 检查密码的正确性
     */
    protected function _checkPasswd($passwd, $notPasswd) {
        if ($passwd == '') {
            return '请输入密码';
        }
        if ($passwd != $notPasswd) {
            return '两次密码输入不一致';
        }
    }
    /**
     * @brief 格式化json
     */
    protected function _formatJson($json) {
        //去除'\',否则json_decode会失败
        $json = stripslashes($json);
        return json_decode($json);
    }
}
