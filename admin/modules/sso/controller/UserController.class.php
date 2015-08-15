<?php
require_once FRAMEWORK_PATH . '/util/rpc/Rpc.class.php';
require_once FRAMEWORK_PATH . '/util/yac/YacCache.class.php';

/**
 * Class UserController
 * @author guochaohui@iyuesao.com
 * @since  2015-01-11
 */
class UserController extends BaseController {

    private $_defaultUrl;

    public function init() {
        $this->_defaultUrl = 'http://' . UrlConfig::getBcUrl() . '/center';
    }

    public function defaultAction($params) {
        echo 'hello world!';
    }

    public function loginPageAction() {
        $tpl              = 'login.php';
        $data['referUrl'] = Request::getREQUEST('refer', $this->_defaultUrl);
        $content          = $this->view->fetch($tpl, $data);
        echo $content;
    }

    /**
     * @brief 用户登录
     */
    public function loginAction() {
        $refer              = urldecode(Request::getPOST('refer', $this->_defaultUrl));
        $params['username'] = Request::getPost('username', '');
        $params['passwd']   = Request::getPost('passwd', '');

        //自动登录
        if (Request::getPOST('submit', '') == '') {
            $this->loginAuto();
            Response::redirect($refer);
        }
        $userClient = Rpc::client('sso.user');

        try {
            $checkRet = (int)$userClient->checkUserInfo($params);
            //用户登陆失败，跳转登陆页面
            //TODO:之后把登陆改为ajax登录，并提示用户名或密码错误
            if ($checkRet < 1) {
                Response::redirect($this->_defaultUrl);
            }
            //用户登陆成功， 设置m_AUTH_STRING，设置cookie

            $ssoTicket = $userClient->encodeSsoTicket($params['username'], md5($params['passwd']));
            setcookie('m_AUTH_STRING', $ssoTicket, time() + 3600, '/', UrlConfig::getBcUrl());
            $nowTime = time();

            //记录登录日志
            $logParams = array(
                'login_time' => $nowTime,
                'login_ip'   => Request::getIp(),
                'login_type' => 1,
                'username'   => $params['username']
            );
            $userClient->addLoginLog($logParams);
            //更新最后一次登录时间
            $userClient->updateUser(array('last_login_time' => $nowTime), array('username' => $params['username']));
            if ($checkRet > 0) {
                Response::redirect($refer);
            }

        } catch (Yar_Server_Exception $e) {
            var_dump($e->getMessage());
        }

    }
    /**
     * @brief
     */

    /**
     * @brief 新增用户
     */

    public function addUserAction() {
        $nowTime             = time();
        $params['username']  = 'water';
        $params['passwd']    = 'water';
        $params['email']     = 'guochaohui@iyuesao.com';
        $params['sex']       = 1;
        $params['mobile']    = '18959262503';
        $params['telephone'] = '18959262503';

        $userClient = Rpc::client('sso.user');

        try {
            $hasRet = (int)$userClient->hasUsername(array('username' => $params['username']));
            if ($hasRet > 0) {
                echo '该用户名已存在！';
                exit;
            }
            $addRet = $userClient->addUser($params);
        } catch (Yar_Server_Exception $e) {
            var_dump($e->getMessage());
        }
        print_r($addRet);
        exit;

    }

    /**
     * @brief 查询用户名是否存在
     */
    public function hasUsernameAction() {
//        $params['username'] = Request::getPost('username');
        $params['username'] = 'water';
        $userClient         = Rpc::client('sso.user');

        try {
            $hasRet = $userClient->hasUsername($params);
        } catch (Yar_Server_Exception $e) {
            var_dump($e->getMessage());
        }
        print_r($hasRet);
        exit;
    }

    /**
     * @brief 利用cookie自动登陆
     */
    public function loginAuto() {

        $ssoTicket  = $_COOKIE['m_AUTH_STRING'];
        $userClient = Rpc::client('sso.user');
        $userInfo        = $userClient->decodeSsoTicket($ssoTicket);
        //自动登录
        $userId = $userClient->getUserInfo('id',['username' => $userInfo[0]]);
        // 所有操作执行完毕，跳转到原页面
        $refer = urldecode(Request::getPOST('refer', $this->_defaultUrl));
        Response::redirect($refer);
    }

    /**
     * @brief 退出登录
     */
    public function logoutAction() {
        $bcUrl = UrlConfig::getBcUrl();
        setcookie('m_AUTH_STRING', '', time() - 1, '/', $bcUrl);
        Response::redirect('http://' . $bcUrl.'/sso/user/loginPage');
    }

}



