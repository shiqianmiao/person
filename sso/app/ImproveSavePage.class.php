<?php

require_once dirname(__FILE__) . '/include/BcProfilePage.class.php';
require_once API_PATH . '/interface/SmsMobileInterface.class.php';

/**
 * @author        chenhan <chenhan@273.cn>
 * @since         2013-6-26
 * @copyright     Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc          业管个人安全增强:提高账户安全级别(检测到密码不安全或未绑定手机)
 */

class ImproveSavePage extends BcProfilePage {
    
    
    /**
     * @brief 1.重置密码
     */
    public function resetPwdAction() {
        $this->getUserInfo();
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('improve_save/reset_pwd.php');
        }
        
        
        $params = array();
        $params['username'] = $this->userInfo['username'];
        $oldPasswd = RequestUtil::getPOST('oldPasswd', '');
        $newPasswd = RequestUtil::getPOST('newPasswd', '');
        $notPasswd = RequestUtil::getPOST('notPasswd', '');
        
        //检查新密码的规范性
        $errorMsg = $this->_checkPasswd($newPasswd, $notPasswd);
        if ($errorMsg) {
            $this->render('improve_save/reset_pwd.php', array(
                    'resultMsg' => array(
                            'error' => $errorMsg
                    ),
            ));
        }
        
        //检查旧用户名密码
        $params['old_passwd'] = md5($oldPasswd);
        $params['new_passwd'] = md5($newPasswd);
        
        $row = MbsUserInterface::checkPwd($params);
        if (empty($row)) {
            $this->render('improve_save/reset_pwd.php', array(
                    'resultMsg' => array(
                            'error' => '旧密码错误'
                    ),
            ));
        }
        
        //修改密码
        $ret = MbsUserInterface::changePwd($params);
        if (empty($ret)) {
            $this->render('improve_save/reset_pwd.php', array(
                    'resultMsg' => array(
                            'error' => '修改密码失败'
                    ),
            ));
        }
        $this->render('improve_save/bind_mobile.php', array(
                    'userInfo' =>$this->userInfo
            ));
    }
    
    /**
     * @brief 仅重置密码(暂时用不到)
     */
    public function onlyResetPwdAction() {
        $this->getUserInfo();
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('improve_save/only_reset_pwd.php');
        }
        
        $params = array();
        $params['username'] = $this->userInfo['username'];
        $oldPasswd = RequestUtil::getPOST('oldPasswd', '');
        $newPasswd = RequestUtil::getPOST('newPasswd', '');
        $notPasswd = RequestUtil::getPOST('notPasswd', '');
        
        //检查新密码的规范性
        $errorMsg = $this->_checkPasswd($newPasswd, $notPasswd);
        if ($errorMsg) {
            $this->render('improve_save/only_reset_pwd.php', array(
                    'resultMsg' => array(
                            'error' => $errorMsg
                    ),
            ));
        }
        
        //检查旧用户名密码
        $params['old_passwd'] = md5($oldPasswd);
        $params['new_passwd'] = md5($newPasswd);
        
        $row = MbsUserInterface::checkPwd($params);
        if (empty($row)) {
            $this->render('improve_save/only_reset_pwd.php', array(
                    'resultMsg' => array(
                            'error' => '旧密码错误'
                    ),
            ));
        }
        
        //修改密码
        $ret = MbsUserInterface::changePwd($params);
        if (empty($ret)) {
            $this->render('improve_save/only_reset_pwd.php', array(
                    'resultMsg' => array(
                            'error' => '新密码不符合规范,修改失败'
                    ),
            ));
        }
        $this->render('improve_save/improve_success.php', array(
                'resultMsg' =>  '重置密码成功'
        ));
    }
    
    /**
     * @brief 2.绑定手机
     */
    public function bindMobileAction() {
        $this->getUserInfo();
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('improve_save/bind_mobile.php', array(
                    'userInfo' =>$this->userInfo
            ));
        }
        
        //验证短信验证码
        $params = array();
        $params['id'] = $this->userInfo['id'];
        $params['bind_mobile'] = RequestUtil::getPOST('bind_mobile', '');
        $params['sms_code'] = RequestUtil::getPOST('sms_code', '');
        
        if (empty($params['sms_code']) || $_SESSION['bd_code_' . $params['bind_mobile']] != $params['sms_code']) {
            $this->render('improve_save/bind_mobile.php', array(
                    'userInfo' =>$this->userInfo,
                    'resultMsg' => array(
                            'error' => '短信验证码错误'
                    ),
            ));
        }
        
        //开始绑定手机
        $row = MbsUserInterface::bindMobile($params);
        if (empty($row)) {
            $this->render('improve_save/bind_mobile.php', array(
                    'resultMsg' => array(
                            'error' => '绑定失败'
                    ),
            ));
        }
        $this->render('improve_save/improve_success.php', array(
                'resultMsg' => '恭喜,您的账号安全等级提高了'
        ));
    }
    
    /**
     * @brief 只绑定手机(暂时用不到)
     */
    public function onlyBindMobileAction() {
        $this->getUserInfo();
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('improve_save/only_bind_mobile.php', array(
                    'userInfo' =>$this->userInfo
            ));
        }
        
        //验证短信验证码
        $params = array();
        $params['id'] = $this->userInfo['id'];
        $params['bind_mobile'] = RequestUtil::getPOST('bind_mobile', '');
        $params['sms_code'] = RequestUtil::getPOST('sms_code', '');
        
        if (empty($params['sms_code']) || $_SESSION['bd_code_' . $params['bind_mobile']] != $params['sms_code']) {
            $this->render('improve_save/only_bind_mobile.php', array(
                    'userInfo' =>$this->userInfo,
                    'resultMsg' => array(
                            'error' => '短信验证码错误'
                    ),
            ));
        }
        
        //开始绑定手机
        $row = MbsUserInterface::bindMobile($params);
        if (empty($row)) {
            $this->render('improve_save/only_bind_mobile.php', array(
                    'resultMsg' => array(
                            'error' => '绑定失败'
                    ),
            ));
        }
        
        //返回个人信息页
        $this->userInfo = MbsUserInterface::getInfoById($params);
        $this->render('profile/user_view.php', array(
                'userInfo' =>$this->userInfo,
                'resultMsg' => array(
                        'success' => '手机绑定成功'
                ),
        ));
    }
    
    /**
     * @brief 跳过验证手机
     */
    public function passBindAction() {
        $this->getUserInfo();
        // 设置一个一年的cookie
        setcookie('m_PASS_tiket' . $this->userInfo['username'], '1', time() + 3600 * 24 * 365, '/', '.corp.273.cn');
                    $this->render('improve_save/improve_success.php', array(
                    'userInfo' =>$this->userInfo,
                    'resultMsg' => '您跳过了手机绑定,可以业管平台的个人设置中继续进行手机绑定,它是找回密码的重要凭证!'
            ));
    }
    
    /**
     * @brief ajax验证密码
     */
    public function ajaxCheckPwdAction(){
        $this->getUserInfo();
        $postArr = RequestUtil::getPOST('json', '');
        $postArr = $this->_formatJson($postArr);
        $params['username'] = $this->userInfo['username'];
        $params['old_passwd'] = md5($postArr->oldPasswd);
        $msgInfo = MbsUserInterface::checkPwd($params);
        //当验证不成功时,返回0
        if (!$msgInfo) {
            echo 0;
        }
    }
    /**
     * @brief ajax短信验证码验证
     */
    public function ajaxCheckCodeAction() {
        session_start();
        $this->getUserInfo();
        $postArr = RequestUtil::getPOST('json', '');
        $postArr = $this->_formatJson($postArr);
        if ($postArr->sms_code) {
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
        $this->getUserInfo();
        $postArr = RequestUtil::getPOST('json', '');
        $postArr = $this->_formatJson($postArr);
        //生成验证码
        $_SESSION['bd_code_' . $postArr->bind_mobile] = rand('100000', '999999');
        //发送短信
        $sms['server_id'] = 0;
        $sms['phone_list'] = $postArr->bind_mobile;
        $sms['content'] = '验证码是：';
        $sms['content'] .= $_SESSION['bd_code_' . $postArr->bind_mobile];
        $sms['content'] .= '。您正在申请业管账号绑定手机号操作，请输入验证码进行验证。如非本人操作，请忽略。【273二手车交易网】';
        SmsMobileInterface::send($sms);
    }
}
