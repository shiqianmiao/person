<?php
require_once dirname(__FILE__) . '/include/SsoData.class.php';
/**
 * @since 2014-8-20
 * @author miaoshiqian
 */

class AccountPage extends Page {
    public $bcDomain   = '.miaosq.com';
    public $defaultUrl = 'http://bc.miaosq.com/';
    public function loginAction() {
        
        $ssoTicket = $_COOKIE['m_AUTH_STRING'];
        
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->loginAuto();
            $this->render('login.php', array(
                'refer' => RequestUtil::getGET('refer',$this->defaultUrl),
            ));
        }
        //再次验证用户名和密码的合法性
        $refer = urldecode(RequestUtil::getPOST('refer', $this->defaultUrl));
        $valid = RequestUtil::getPOST('valid', '');
        $account = RequestUtil::getPOST('account', '');
        $password = RequestUtil::getPOST('password', '');
        if (md5(strtolower($valid)) != $_COOKIE['validString'] && !DEBUG_STATUS) {
             $this->render('login.php', array(
                 'resultMsg' => '验证码错误',
                 'account' => $account,
                 'password' => $password,
                 'refer' => $refer,
             ));
        }
        $password = md5($password);
        $row = SsoData::getUserWhenLogin($account, $password);
        if (empty($row[0])) {
            $this->render('login.php', array(
                'resultMsg' => $row[1],
                'refer' => $refer,
            ));
        }
        
        //设置m_AUTH_STRING，设置cookie
        $ssoTicket = SsoData::encodeSsoTicket($account, $password);
        setcookie('m_AUTH_STRING', $ssoTicket, 0, '/', $this->bcDomain);
        
        $userTable = new SsoUserModel();
        $logTable = new SsoLogModel();
        
        $loginTime = time();
        $loginCount = empty($row[0]['login_count']) ? 0 : $row[0]['login_count'];
        $loginCount++;
        $userTable->update(array(
            'last_login_time' => $loginTime,
            'login_count' => $loginCount
        ), array(
            array('username', '=', $account),
        ));
        $row = array(
            'account' => $account,
            'in_time' => time(),
            'ip' => RequestUtil::getIp(),
        );
        $logTable->insert($row);
        
        //所有操作执行完毕，跳转到原页面

        ResponseUtil::redirect($refer);
        
    }


    public function showImageAction() {
        require_once FRAMEWORK_PATH . '/util/image/Image.class.php';
        $cookieName = 'validString';
        $image = new Image();
        define('VALID_WIDTH',60);
        define('VALID_HEIGHT',20);                                             
        define('VALID_CODE_TYPE', 3);                                          
        define('VALID_CODE_LENGTH', 4);                                        
        $valid = $image->imageValidate(VALID_WIDTH, VALID_HEIGHT,              
                VALID_CODE_LENGTH, VALID_CODE_TYPE,'#3e3e3e','#B6B6B6');       
        setcookie($cookieName, md5(strtolower($valid)), 0, '/', '.corp.273.cn');
        header("Pragma: no-cache");
        header("Cache-control: no-cache");
        $image->display();                      
    }
    
    /**
     * @brief 修改密码
     */
    public function changePwdAction() {
        //检查是否登录
        include_once API_PATH . '/interface/MbsUserInterface.class.php';
        include_once API_PATH . '/interface/SsoInterface.class.php';
        if (!empty($_COOKIE['m_AUTH_STRING'])) {
            $sessUesrInfo = Session::getValue('backend-user-info');
            if (!empty($sessUesrInfo) && $_COOKIE['m_AUTH_STRING'] == $sessUesrInfo['cookie']) {
                $this->userInfo = $sessUesrInfo;
            } else {
                $this->userInfo = SsoInterface::getUserInfo(array('passport' => $_COOKIE['m_AUTH_STRING']));
            }
        }
        //如果未登录则跳转到sso登录界面
        if (empty($this->userInfo)) {
            $this->render('login.php');
        }
        if (RequestUtil::getPOST('submit', '') == '') {
            $this->render('change_pwd.php');
        }
        // 再次验证用户名和密码的合法性
        $params = array();
        $account = $this->userInfo['username'];
        $oldPasswd = RequestUtil::getPOST('oldPasswd', '');
        $newPasswd = RequestUtil::getPOST('newPasswd', '');
        $notPasswd = RequestUtil::getPOST('notPasswd', '');
        
        $oldPasswd = md5($oldPasswd);
        $newPasswd = md5($newPasswd);
        $notPasswd = md5($notPasswd);
        
        //同时修改oracle和mysql两边的密码
        $params['account_id'] = $account;
        $params['username'] = $account;
        $params['passwd'] = $oldPasswd;
        $params['old_passwd'] = $oldPasswd;
        $params['new_passwd'] = $newPasswd;
        $row = SsoInterface::checkPwd($params);
        if (empty($row)) {
            $this->render('change_pwd.php', array(
                    'resultMsg' => '密码错误，修改失败!',
            ));
        }
        if ($newPasswd != $notPasswd) {
            $this->render('change_pwd.php', array(
                    'resultMsg' => '两次输入的密码不一致!',
            ));
        }
        if ($oldPasswd == $newPasswd) {
            $this->render('change_pwd.php', array(
                    'resultMsg' => '新密码不能和旧密码一致!',
            ));
        }
        $ret = SsoInterface::changePwd($params);
        $retFromOracle = MbsUserInterface::changePwd($params);
        
        //有一边的数据库没有update成功,就还原
        if (empty($ret) || !$retFromOracle) {
            $params['new_passwd'] = $params['old_passwd'];
            $ret = SsoInterface::changePwd($params);
            $retFromOracle = MbsUserInterface::changePwd($params);
            $this->render('change_pwd.php', array(
                    'resultMsg' => '修改失败!',
            ));
        }
        setcookie('m_AUTH_STRING', '', time() - 1, '/', '.corp.273.cn');
        $this->render('login.php', array(
                'resultMsg' => '修改密码成功!请使用新密码重新登录',
        ));
    }
    
    public function loginAuto() {
        
        $ssoTicket = $_COOKIE['m_AUTH_STRING'];
        
        $arr = SsoData::decodeSsoTicket($ssoTicket);
        $account = $arr;
        
        $row = SsoData::getUser($account);
        if (empty($row)) {
            return ;
        }
        
        // 所有操作执行完毕，跳转到原页面
        $refer = urldecode(RequestUtil::getGET('refer', $this->defaultUrl));
        
        ResponseUtil::redirect($refer);
    }
    
    public function logoutAction() {
        setcookie('m_AUTH_STRING', '', time() - 1, '/', '.miaosq.com');
        $refer = RequestUtil::getGet('refer', $this->defaultUrl);
        ResponseUtil::redirect($refer);
    }
    
    /**
     * @brief 外部接口，获取用户信息
     */
    public function getUserInfoAction() {
        $ssoTicket = RequestUtil::getPOST('m_AUTH_STRING', '');
        $arr = SsoData::decodeSsoTicket($ssoTicket, true);
        $account = $arr[0];
        $pass = $arr[1];
        $row = SsoData::getUser($account);
        if ($pass != substr($row['passwd'], 0, 16)) {
            include_once SSO_SITE . '/model/SuperPasswdModel.class.php';
            $superModel = new SuperPasswdModel();
            $superPass = $superModel->getOne('passwd', array(array('status', '=', 0)));
            if ($pass != substr(md5($superPass), 0, 16)) {
                $row = array();
            }
        }
        echo json_encode($row);
    }
    
    /**
     * @brief 外部接口，获取权限码
     */
    public function getAllCodeAction() {
        
        $app = RequestUtil::getPOST('app', '');
        $ssoTicket = RequestUtil::getPOST('m_AUTH_STRING', '');
        // if (!SsoData::checkSso($ssoTicket)) {
        //     return ;
        // }
        
        $codeList = SsoData::getAllCode($ssoTicket, $app);
        echo json_encode($codeList);
    }

    public function getInfoAndCodeAction() {
        $ret = array();
        $app = RequestUtil::getPOST('app', '');
        $ssoTicket = RequestUtil::getPOST('m_AUTH_STRING', '');
        $arr = SsoData::decodeSsoTicket($ssoTicket);
        $account = $arr;
        $row = SsoData::getUser($account);
        $ret['user'] = $row;
        $app = explode('#', $app);
        $codeList = array();
        if (isset($app[1])) {
            foreach ($app as $item) {
                $code = SsoData::getAllCode($ssoTicket, $item);
                $codeList = array_merge($codeList, $code);
            }
        } else {
            $codeList = SsoData::getAllCode($ssoTicket, $app[0]);
        }
        $ret['code'] = $codeList;
        echo json_encode($ret);
    }
    
    /**
     * @brief 外部接口，判断用户是否拥有某权限
     */
    public function checkCodeAction() {
        
        $code = RequestUtil::getPOST('code', '');
        $app = RequestUtil::getPOST('app', '');
        $ssoTicket = RequestUtil::getPOST('m_AUTH_STRING', '');
        
        if (!SsoData::checkSso($ssoTicket)) {
            return ;
        }
        
        $ret = SsoData::checkCode($ssoTicket, $code, $app);
        
        echo json_encode($ret);
    }
    
    /**
     * @brief 验证m_AUTH_STRING
     */
    public function checkUserAction() {
        $ssoTicket = RequestUtil::getPOST('m_AUTH_STRING', '');
        $ret = SsoData::checkSso($ssoTicket);
        echo json_encode($ret);
    }
}
