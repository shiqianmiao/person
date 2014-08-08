<?php

require_once dirname(__FILE__) . '/include/BcProfilePage.class.php';
require_once dirname(__FILE__) . '/include/SsoData.class.php';
require_once API_PATH . '/interface/SmsMobileInterface.class.php';
require_once FRAMEWORK_PATH . '/util/image/Image.class.php' ;

/**
 * @author        chenhan <chenhan@273.cn>
 * @since         2013-6-26
 * @copyright     Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc          业管个人安全增强:找回密码
 */

class BackPwdPage extends BcProfilePage {
    
    /**
     * @brief 找回密码:输入用户名
     */
    public function inputUserAction() {
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('back_pwd/back_input_user.php');
        }
        //检查验证码正确性
        $this->_checkCode();
        
        //检查用户名是否存在
        $this->_checkUser();
        //判断是否绑定了手机,
        if ($this->userInfo['bind_mobile']) {
            $this->render('back_pwd/back_save_check.php', array(
                    'userInfo' => $this->userInfo
            ));
        }
        $this->render('back_pwd/back_error.php', array(
                'resultMsg' => '您未绑定手机号码，无法完成安全验证！'
        ));
    }
    
    /**
     * @brief 找回密码:安全验证
     */
    public function backSaveCheckAction() {
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('back_pwd/back_error.php', array(
                    'resultMsg' => '请先输入用户名,再进行安全验证!'
            ));
        }
        session_start();
        
        //通过用户名取得用户信息
        $params = array();
        $params['username'] = RequestUtil::getPOST('username', '');
        $this->userInfo = MbsUserInterface::getInfoByUsername($params);
        $params['bind_mobile'] = $this->userInfo['bind_mobile'];
        $params['sms_code'] = RequestUtil::getPOST('sms_code', '');
        
        //检查短信验证码
        if ($_SESSION['bd_code_' . $params['bind_mobile']] != $params['sms_code']) {
            $this->render('back_pwd/back_save_check.php', array(
                    'userInfo' =>$this->userInfo,
                    'resultMsg' => array(
                            'error' => '短信验证码错误'
                    ),
            ));
        }

        //通过手机验证,则生成操作码
        $opCode = $this->_getOpCode();
        $this->render('back_pwd/back_pwd.php', array(
                'userInfo' => $this->userInfo,
                'opCode' => $opCode,
        ));
    }
    
    /**
     * @brief 找回密码:输入新密码
     */
    public function backPwdAction() {
        //检查操作码,不存在则是非法访问
        session_start();
        $opCode = RequestUtil::getPOST('opCode', '');
        if (!isset($_SESSION[$opCode])) {
            $this->render('back_pwd/back_error.php', array(
                    'resultMsg' => '非法操作!'
            ));
        }
        
        $params = array();
        $params['username'] = RequestUtil::getPOST('username', '');
        $this->userInfo = MbsUserInterface::getInfoByUsername($params);
        //检查密码的规范性
        $passwd = RequestUtil::getPOST('passwd', '');
        $notPasswd = RequestUtil::getPOST('notPasswd', '');
        $errorMsg = $this->_checkPasswd($passwd, $notPasswd);
        if ($errorMsg) {
            $this->render('back_pwd/back_pwd.php', array(
                    'userInfo' => $this->userInfo,
                    'opCode' => $opCode,
                    'resultMsg' => array(
                            'error' => $errorMsg
                    ),
            ));
        }
        
        //开始密码修改
        $params['new_passwd'] = md5($passwd);
        $ret = MbsUserInterface::changePwd($params);
        if (empty($ret)) {
            $this->render('back_pwd/back_pwd.php', array(
                    'opCode' => $opCode,
                    'resultMsg' => array(
                            'error' => '修改密码失败!'
                    ),
            ));
        }
        
        //修改密码成功后,生成登录cookie
        $ssoTicket = SsoData::encodeSsoTicket($params['username'], $params['new_passwd']);
        setcookie('m_AUTH_STRING', $ssoTicket, 0, '/', '.corp.273.cn');
        $this->render('back_pwd/back_success.php', array(
                'resultMsg' => '恭喜,修改密码成功!',
        ));
    }
    
    /**
     * @brief ajax短信验证码验证
     */
    public function ajaxCheckCodeAction() {
        session_start();
        $postArr = RequestUtil::getPOST('json', '');
        $postArr = $this->_formatJson($postArr);
        if ($postArr->username && $postArr->sms_code) {
            $params = array();
            $params['username'] = $postArr->username;
            $this->userInfo = MbsUserInterface::getInfoByUsername($params);
            $postArr->bind_mobile = $this->userInfo['bind_mobile'];
            if ($_SESSION['bd_code_' . $postArr->bind_mobile] != $postArr->sms_code) {
                echo 0;
            }
        }
    }
    
    /**
     * @brief ajax验证密码
     */
    public function ajaxSendSMSAction(){
        session_start();
        $postArr = RequestUtil::getPOST('json', '');
        $postArr = $this->_formatJson($postArr);
        if ($postArr->username) {
            $params = array();
            $params['username'] = $postArr->username;
            $this->userInfo = MbsUserInterface::getInfoByUsername($params);
            $postArr->bind_mobile = $this->userInfo['bind_mobile'];
            //生成验证码
            $_SESSION['bd_code_' . $postArr->bind_mobile] = rand('100000', '999999');
            //发送短信
            $sms['server_id'] = 0;
            $sms['phone_list'] = $postArr->bind_mobile;
            $sms['content'] = '验证码是：';
            $sms['content'] .= $_SESSION['bd_code_' . $postArr->bind_mobile];
            $sms['content'] .= '。 您正在进行找回业管密码操作，请输入验证码进行验证。如非本人操作，建议及时修改账号密码。【273二手车交易网】';
            SmsMobileInterface::send($sms);
        }
    }
    
    /*
     * @brief 验证码检查
     */
    private function _checkCode() {
        $code = RequestUtil::getPOST('code', '');
        $validString = RequestUtil::getCOOKIE(self::$_COOKIE_NAME);
        
        if (empty($code)) {
            $this->render('back_pwd/back_input_user.php', array(
                    'resultMsg' => array(
                            'code' => true,
                            'error' => '请输入验证码'
                    ),
            ));
        }
        if ($validString != md5($code)) {
            $this->render('back_pwd/back_input_user.php', array(
                    'resultMsg' => array(
                            'code' => true,
                            'error' => '验证码错误'
                    ),
            ));
        }
    }
    /*
     * @brief 用户名检查
     */
    private function _checkUser() {
        $params = array();
        $params['username'] = (int) (RequestUtil::getPOST('username', ''));
        $this->userInfo = MbsUserInterface::getInfoByUsername($params);
        if (!$this->userInfo) {
            $this->render('back_pwd/back_input_user.php', array(
                    'resultMsg' => array(
                            'username' => true,
                            'error' => '账号不存在,请重新输入'
                    ),
            ));
        }
        $this->_checkUserStatus($this->userInfo);
    }
    
    private function _checkUserStatus($userInfo) {
        if ($userInfo['status'] != 1) {
            $this->render('back_pwd/back_error.php', array(
                    'resultMsg' => '您的账号已经被暂停或屏蔽使用,无法完成安全验证'
            ));
        }
    }
}
