<?php

require_once dirname(__FILE__) . '/include/BcProfilePage.class.php';
require_once API_PATH . '/interface/SmsMobileInterface.class.php';
require_once API_PATH . '/interface/ExtPhoneInterface.class.php';
require_once API_PATH . '/interface/MbsDeptInterface.class.php';

/**
 * @author        chenhan <chenhan@273.cn>
 * @since         2013-6-26
 * @copyright     Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc          业管个人安全增强:个人设置
 */

class ProfilePage extends BcProfilePage {
    
    /**
     * @brief 个人资料查看和修改
     */
    public function userViewAction() {
        $this->getUserInfo();
        
        //资料查看
        $params = array();
        $params['id'] = $this->userInfo['id'];
        $this->userInfo = MbsUserInterface::getInfoById($params);
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('profile/user_view.php', array(
                    'userInfo' =>$this->userInfo 
            ));
        }
        
        //修改资料
        $params['username'] = $this->userInfo['username'];
        $params['real_name'] = RequestUtil::getPOST('real_name', '');
        $params['email'] = RequestUtil::getPOST('email', '');
        $params['mobile'] = RequestUtil::getPOST('mobile', '');
        $params['old_mobile'] = RequestUtil::getPOST('old_mobile', '');
        $params['telephone'] = RequestUtil::getPOST('telephone', '');
        
        //检查真实姓名是否存在
        /*if (empty($params['real_name'])) {
            $this->render('profile/user_view.php', array(
                    'userInfo' => $this->userInfo,
                    'resultMsg' => array(
                            'error' => '请您输入真实姓名'
                    ),
            ));
        }*/
        
        //检查移动电话是否存在
        if (empty($params['mobile'])) {
            $this->render('profile/user_view.php', array(
                    'userInfo' => $this->userInfo,
                    'resultMsg' => array(
                            'error' => '请您输入移动电话'
                    ),
            ));
        }
        
        //开始保存个人资料
        $row = MbsUserInterface::userViewSave($params);
        if ($row) {
            $this->userInfo = MbsUserInterface::getInfoById($params);
            //修改固定转接号绑定的业务员电话号码
            $deptInfo = MbsDeptInterface::getDeptInfoByDeptId(array('id' => $this->userInfo['dept_id']));
            ExtPhoneInterface::updateUserAllBind(array(
                'follow_user' => $this->userInfo['username'],
                'new_mobile' => $this->userInfo['mobile'],
                'dept_city' => $deptInfo['city'],
            ));
            $this->render('profile/user_view.php', array(
                    'userInfo' => $this->userInfo,
                    'resultMsg' => array(
                            'success' => '保存成功'
                    ),
            ));
        }
        $this->render('profile/user_view.php', array(
                'userInfo' => $this->userInfo,
                'resultMsg' => array(
                        'error' => '修改失败'
                ),
        ));
    }
    
    /**
     * @brief 修改密码
     */
    public function changePwdAction() {
        $this->getUserInfo();
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('profile/change_pwd.php');
        }
        
        $params = array();
        $params['username'] = $this->userInfo['username'];
        $oldPasswd = RequestUtil::getPOST('oldPasswd', '');
        $newPasswd = RequestUtil::getPOST('newPasswd', '');
        $notPasswd = RequestUtil::getPOST('notPasswd', '');
        
        //检查新密码的规范性
        $errorMsg = $this->_checkPasswd($newPasswd, $notPasswd);
        if ($errorMsg) {
            $this->render('profile/change_pwd.php', array(
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
            $this->render('profile/change_pwd.php', array(
                    'resultMsg' => array(
                            'error' => '旧密码错误'
                    ),
            ));
        }
        
        //开始修改密码
        $ret = MbsUserInterface::changePwd($params);
        if (empty($ret)) {
            $this->render('profile/change_pwd.php', array(
                    'resultMsg' => array(
                            'error' => '修改密码失败'
                    ),
            ));
        }
        
        $this->userInfo = MbsUserInterface::getInfoByUsername($params);
        $this->render('common/success.php', array(
                'resultMsg' => '恭喜,修改密码成功!',
                'url' => 'http://sso.corp.273.cn/profile/userView/'
        ));
    }
    
    /**
     * $brief 更换绑定手机验证
     */
    public function changeBindMobileAction() {
        $this->getUserInfo();
        
        //检查是否绑定了手机
        $params = array();
        $params['id'] = $this->userInfo['id'];
        $this->userInfo = MbsUserInterface::getInfoById($params);
        if (empty($this->userInfo['bind_mobile'])) {
            $this->render('profile/bind_error.php', array(
                    'resultMsg' => '您没有绑定手机'
            ));
        }
        
        //更换绑定手机首页
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('profile/change_bind_mobile.php', array(
                    'userInfo' => $this->userInfo
            ));
        }
        
        //取得原先绑定的手机号
        $params['bind_mobile'] = RequestUtil::getPOST('bind_mobile', '');
        $params['sms_code'] = RequestUtil::getPOST('sms_code', '');
        $this->_getBindMobile($params['bind_mobile']);
        
        //验证短信验证码
        session_start();
        if (empty($params['sms_code']) || $_SESSION['bd_code_' . $params['bind_mobile']] != $params['sms_code']) {
            $this->render('profile/change_bind_mobile.php', array(
                    'userInfo' =>$this->userInfo,
                    'resultMsg' => array(
                            'error' => '手机验证码错误'
                    ),
            ));
        }
            
        //开始绑定手机
        $ret = MbsUserInterface::checkBindMobile($params);
        if (empty($ret)) {
            $this->render('profile/change_bind_mobile.php', array(
                    'userInfo' =>$this->userInfo,
                    'resultMsg' => array(
                            'error' => '手机验证失败'
                    ),
            ));
        }
        
        //通过手机验证,则生成操作码
        $opCode = $this->_getOpCode();
        $this->render('profile/bind_new_mobile.php', array(
                'userInfo' =>$this->userInfo,
                'opCode' => $opCode,
        ));
    }
    
    /**
     * @brief 绑定新手机
     */
    public function bindNewMobileAction() {
        //检查操作码,不存在则是非法访问
        session_start();
        $opCode = RequestUtil::getPOST('opCode', '');
        if (!$_SESSION[$opCode]) {
            $this->render('profile/bind_error.php', array(
                    'resultMsg' => '非法操作!'
            ));
        }
        
        //验证短信验证码
        $this->getUserInfo();
        $params = array();
        $params['id'] = $this->userInfo['id'];
        $params['bind_mobile'] = RequestUtil::getPOST('bind_mobile', '');
        $params['sms_code'] = RequestUtil::getPOST('sms_code', '');
        
        if (empty($params['sms_code']) || $_SESSION['bd_code_' . $params['bind_mobile']] != $params['sms_code']) {
            $this->render('profile/bind_new_mobile.php', array(
                    'opCode' => $opCode,
                    'userInfo' =>$this->userInfo,
                    'resultMsg' => array(
                            'error' => '手机验证码错误'
                    ),
            ));
        }
        
        //开始绑定新手机
        $row = MbsUserInterface::bindMobile($params);
        if (empty($row)) {
            $this->render('profile/bind_new_mobile.php', array(
                    'opCode' => $opCode,
                    'resultMsg' => array(
                            'error' => '绑定失败'
                    ),
            ));
        }
        $this->render('profile/bind_success.php', array(
                'resultMsg' => '恭喜,更换绑定手机号码成功'
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
        $this->_getBindMobile($postArr->bind_mobile);
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
        $this->_getBindMobile($postArr->bind_mobile);
        //生成验证码
        $_SESSION['bd_code_' . $postArr->bind_mobile] = rand('100000', '999999');
        //发送短信
        $sms['server_id'] = 0;
        $sms['phone_list'] = $postArr->bind_mobile;
        $sms['content'] = '验证码是：';
        $sms['content'] .= $_SESSION['bd_code_' . $postArr->bind_mobile];
        $sms['content'] .= '。您正在申请更换业管绑定手机号操作，请输入验证码进行验证。 如非本人操作，请忽略。【273二手车交易网】';
        SmsMobileInterface::send($sms);
    }
    
    /**
     * @brief 当绑定手机没有从json传过来时,从库中取得绑定手机号
     * @param unknown_type $bindMobile
     */
    private function _getBindMobile(&$bindMobile) {
        if (!isset($bindMobile) || empty($bindMobile)) {
            $this->_getUserInfo();
            $bindMobile = $this->userInfo['bind_mobile'];
        }
    }
}
