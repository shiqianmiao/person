<?php
require_once FRAMEWORK_PATH . '/mvc/BasePage.class.php';
require_once FRAMEWORK_PATH . '/util/common/Session.class.php' ;
require_once API_PATH . '/interface/SsoInterface.class.php';
require_once API_PATH . '/interface/StaticInterface.class.php';
require_once BACKEND . '/common/Menu.class.php';
require_once BACKEND . '/common/Normal.class.php';

class BackendPage extends BasePage {
    const BC_DOMAIN = '.miaosq.com';
    protected $userInfo;
    protected $menu;

    protected $allApp = array(
        'bc', //bc后台
        'mbs.bc', //业管后台
        'cs', //客服后台
        'zb.bc', //总部后台
        'data', //数据后台
    );

    public function __construct() {
        parent::__construct();
        
        //检查浏览器
        $this->checkAgent();
        
        // 判断用户是否登录
        if ($_GET['fix_bug_slow']) {
            $debugTime = 1;
        }
        if ($debugTime) {
            $beginTime = microtime();
        }
        $this->checkUser();
        if ($debugTime) {
            echo microtime() - $beginTime;
        }
        
        $permissions = (array) $this->userInfo['Permisssions'];
        
        // 取所有频道
        $channels = BackendPageConfig::getChannels();
        
        $routeParams = Bootstrap::getRouteParams();
        $channel = $routeParams['channel'];
        if ( !isset($channels[$channel])) {
            $this->renderError('未配置管理后台[' . $channel . ']');
        }
        
        $channelInfo = $channels[$channel];
        if ($channel != 'default') {
            // 判断频道是否有权限
            $channelCode = !empty($channelInfo['code']) ? $channelInfo['code'] : $channel;
            //$this->checkPermission($channelCode);
            //判断模块是否有权限
            $file = $routeParams['channel_dir'] . '/config/code.inc.php';
            if (file_exists($file)) {
                $codeData = include $file;
                $moduleAction = $routeParams['module'] . '.' . $routeParams['action'];
                $module = $routeParams['module'];
                /*if (isset($codeData[$moduleAction])) {
                    $this->checkPermission($codeData[$moduleAction]);
                } else if (isset($codeData[$module])) {
                    $this->checkPermission($codeData[$module]);
                }*/
            }
        }
        
        // 过滤频道,拥有超级权限的人不用过滤菜单
        if (!isset($permissions['super'])) {
            foreach ($channels as $key => $tmp) {
                $code = empty($tmp['banner_code']) ? $key : $tmp['banner_code'];
                if ($code != 'default' && !isset($permissions[$code])) {
                    unset($channels[$key]);
                } elseif (!$this->deptInfo['is_contract'] && $code == 'mbs_contract') {
                    unset($channels[$key]);
                }
            }
        }
        
        if (!$routeParams['is_ajax'] && !$routeParams['is_iframe']) {
            // 载入菜单
            $this->menu = new Menu((array) $permissions, (array) $channels);

            if ($routeParams['channel'] == 'default') {
                $count = 1;
                $default = '';  
                foreach ($channels as $key=>$item) {
                    if ($count == 2) {
                        $default = defined('SET_DEFAULT_PAGE') ? SET_DEFAULT_PAGE : '/'.$key;
                        $host = $_SERVER['HTTP_HOST'];
                        ResponseUtil::redirect('http://' . $host . $default);
                    }
                    $count ++;
                }
            }
            if ($routeParams['module'] == 'index' && $routeParams['action'] == 'default') {
                $menus = $this->menu->getData();
                $rediUrl = '';
                foreach ($menus['menu'] as $firstMenu) {
                    foreach ($firstMenu['menu'] as $secondMenu) {
                        $rediUrl = $secondMenu['url'];
                        break;
                    }
                    break;
                }
            }
            //设置一些模板变量
            if (defined('SET_MBS_MODULE')) {
                $this->assign('mbsAppModule', SET_MBS_MODULE);
            }
            $channelInfo['url'] = $channel;
            $this->view->assign('channelInfo', $channelInfo);
            
            $this->view->assign('channels', $channels);
            
            $this->view->assign('userInfo', $this->userInfo);
            
            $changePwdUrl = SsoInterface::getChangePwdUrl();
            
            $this->view->assign('changePwdUrl', $changePwdUrl);
            
            $logoutUrl = SsoInterface::getLogoutUrl();
            
            $this->view->assign('logoutUrl', $logoutUrl);
        }
    }

    public function init() {
        parent::init();
        $this->assign('commonStaHtml', StaticInterface::getStaticFiles(array(
            'files'    => array(
                'app/mbs/css/bootstrap.css',
                'app/mbs/css/responsive.css',
                'app/mbs/css/operationmanage.css',
                'g.js',
                'config.js',
            ),
        )));
    }

    // 检测是否拥用$code的权限，如果没有权限将提示错误
    protected function checkPermission ($code) {
        if (isset($this->userInfo['Permisssions'][$code])) {
            return true;
        }

        $this->renderError('对不起！您没有相关操作权限', 'no-permission');
    }
    //检查浏览器
    protected function checkAgent() {
        if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') || strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0')) {
            $this->renderError('low_agent');
        }
    }

    // 检测用户是否已登录，如果没有则跳转到登录页，如果已登录，设置userInfo
    final function checkUser() {
        if (!empty($_COOKIE['m_AUTH_STRING'])) {
            $permissionApp = 'bc';
            if (defined('PERMISSION_APP_NAME')) {
                $permissionApp = PERMISSION_APP_NAME;
            }
            $sessUesrInfo = Session::getValue('backend-user-info-' . $permissionApp);
            $error = 0;
            if (!empty($sessUesrInfo['user']['username']) && $_COOKIE['m_AUTH_STRING'] == $sessUesrInfo['cookie']) {
                $info = SsoInterface::parsePassport(array('passport' => $_COOKIE['m_AUTH_STRING']));
                if (count($info) < 4) {
                    setcookie('m_AUTH_STRING', '', time()-1, '/', SELF::BC_DOMAIN);
                    $error = 1;
                } elseif (time() - $info[3] > 300 && false) {
                    if (substr($sessUesrInfo['user']['passwd'],0,16) == $info[1]) {
                        $info[3] = time();
                        $newText = implode('-',$info);
                        $newPassport = SsoInterface::getPassport(array('account_id'=>$info[0], 
                                        'passwd' => $sessUesrInfo['user']['passwd'], 
                                        'create_time' => $info[2], 
                                        'check_time' => time()
                        ));
                        setcookie('m_AUTH_STRING', $newPassport, 0, '/', SELF::BC_DOMAIN);
                    } else {
                        setcookie('m_AUTH_STRING', '', time()-1, '/', SELF::BC_DOMAIN);
                        $error = 1;
                    }
                } else {
                    $this->userInfo = $sessUesrInfo;
                    return true;
                }
            }
            if (!$error) {
                $ret = array();
                $infoAndCode = array();
                if ($permissionApp == 'bc') {
                    $infoAndCode = SsoInterface::getInfoAndCode(array(
                        'app' => $permissionApp,
                        'passport' => $_COOKIE['m_AUTH_STRING'],
                    ));
                } else {
                    $infoAndCode = SsoInterface::getInfoAndCode(array(
                        'app' => 'bc#' . $permissionApp,
                        'passport' => $_COOKIE['m_AUTH_STRING'],
                    ));
                }
                $ret['Permisssions'] = $infoAndCode['code'];
                $ret['user'] = $infoAndCode['user'];
                $ret['Permisssions'] = array_flip($ret['Permisssions']);
                $ret['cookie'] = $_COOKIE['m_AUTH_STRING'];
                $ret['role'] = SsoInterface::getGroupByUser(array('field' => 'code', 'user_id' => $ret['user']['id']));
                $roleArr = array();
                if (!empty($ret['role'])) {
                    foreach ($ret['role'] as $key => $role) {
                        $roleArr[$role['code']] = $key;
                    }
                }
                $ret['role'] = $roleArr;
                unset($roleArr);
                if (!empty($ret['user']['username'])) {
                    /*
                    session_start();
                    $_SESSION['sso_tmp'] = 1;
                    */
                    Session::setValue('backend-user-info-' . $permissionApp, $ret);
                    $this->userInfo = $ret;
                    return true;
                } else {
                    setcookie('m_AUTH_STRING', '', time()-1, '/', SELF::BC_DOMAIN);
                    $this->renderError('你没有权限，请尝试刷新页面，或重新登录', 'not-login');
                }
            }
        }
        foreach ($this->allApp as $bcApp) {
            Session::delete('backend-user-info-' . $bcApp);
        }
        $routeParams = Bootstrap::getRouteParams();

        if (!SsoInterface::login(array('passport' => $_COOKIE['m_AUTH_STRING']))) {
            $redirectUrl = SsoInterface::getLoginUrl();
            if ($routeParams['is_ajax']) {
                $this->renderError('你没有权限，请尝试刷新页面，或重新登录', 'not-login');
            }
            else if ($routeParams['is_iframe']) {
                ResponseUtil::parentRedirect($redirectUrl);
            } else {
                ResponseUtil::redirect($redirectUrl);
            }
        }
    }

    protected function render($data, $tpl = '') {
        $routeParams = Bootstrap::getRouteParams();
        $content = '';
        if ($routeParams['ajax']) {
            if (!empty($tpl)) {
                $data['data'] = $this->view->fetch($tpl, $data);
                if (isset($data['errorCode']) && $data['errorCode'] > 0) {
                    $data['errorMessage'] = $data['data'];
                }
            }
            $content = $data;
        } else {
            if (!empty($tpl)) {
                $data['fileBody'] = $tpl;
            }
            if ($routeParams['iframe']) {
                $content = $this->view->fetch(BackendPageConfig::IFRAME_SUPER_TPL, $data);
            } elseif (isset(Normal::$NORMAL_MODULE[$routeParams['channel']][$routeParams['module']])) {
                parent::render($data, $tpl);
            } else {
                //小菜单也要把数字调出来，以菜单的URL地址进行匹对，因为权限编号很多会重复
                $menuData    = $this->menu->getData();
                if (is_array($menuData['menu'])) {
                    foreach ($menuData['menu'] as $mKey => $mItem) {
                        $childMenu = $mItem['menu'];
                        if (is_array($childMenu)) {
                            foreach ($childMenu as $cKey => $cItem) {
                                //数量的KEY
                                $numKey = urlencode($cItem['text']) . $cItem['url'] . $cItem['code'];
                            }
                        }
                    }
                }
                $this->view->assign('menu', $menuData);
                $content = $this->view->fetch(BackendPageConfig::WHOLE_SUPER_TPL, $data);
            }
        }
        $this->view->output($content, $routeParams['content_type'], $routeParams['jsonp']);
        XhprofLog::logXhprof('backend');
        exit;
    }
}
