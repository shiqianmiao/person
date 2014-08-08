<?php
require_once FRAMEWORK_PATH . '/mvc/BasePage.class.php';
require_once FRAMEWORK_PATH . '/util/common/Session.class.php' ;
require_once API_PATH . '/interface/SsoInterface.class.php';
require_once API_PATH . '/interface/car/CarUserInterface.class.php';
require_once API_PATH . '/interface/MbsDeptInterface.class.php';
require_once API_PATH . '/interface/StaticInterface.class.php';
require_once API_PATH . '/interface/BackendNumShowInterface.class.php';
require_once API_PATH . '/interface/mbs/MbsSmsEmailInterface.class.php';
require_once API_PATH . '/interface/GetLastSqlInterface.class.php';
require_once BACKEND . '/common/Menu.class.php';
require_once BACKEND . '/common/Normal.class.php';
require_once BACKEND . '/common/Search.class.php';
require_once COM_PATH . '/car/CarAppVars.class.php';

class BackendPage extends BasePage {

    protected $userInfo;
    protected $deptInfo;
    protected $menu;
    protected $isInfoUser = 0; //是否终止的车源从特殊表取，同时业管不让搜索

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
        //检测是否需要显示提示设置服务之星的tips
        $this->checkStarTips();

        //检查是否需要配置特殊数据处理
        if (!empty(CarAppVars::$COPY_MANAGER)) {
            if (CarAppVars::$COPY_MANAGER['0'] == '*') {
                //所有业管帐号都从那张表取
                $this->isInfoUser = 1;
            } else if (in_array($this->userInfo['user']['username'], CarAppVars::$COPY_MANAGER)) {
                $this->isInfoUser = 1;
            }
        }
        $this->view->assign('isInfoUser', $this->isInfoUser);
        
        //取得门店信息
        if (!empty($this->userInfo['user']['dept_id'])) {
            $this->deptInfo = MbsDeptInterface::getDeptInfoByDeptId(array('id' => $this->userInfo['user']['dept_id']));
        }

        $permissions = (array) $this->userInfo['Permisssions'];

        // 取所有频道
        $channels = BackendPageConfig::getChannels();

        $routeParams = Bootstrap::getRouteParams();
        $channel = $routeParams['channel'];
        if ($channel != 'universal' && !isset($channels[$channel])) {
            $this->renderError('未配置管理后台[' . $channel . ']');
        }
        
        $channelInfo = $channels[$channel];
        if ($channel != 'default' && $channel != 'universal') {
            // 判断频道是否有权限
            $channelCode = !empty($channelInfo['code']) ? $channelInfo['code'] : $channel;
            $this->checkPermission($channelCode);
            //判断模块是否有权限
            $file = $routeParams['channel_dir'] . '/config/code.inc.php';
            if (file_exists($file)) {
                $codeData = include $file;
                $moduleAction = $routeParams['module'] . '.' . $routeParams['action'];
                $module = $routeParams['module'];
                if (isset($codeData[$moduleAction])) {
                    $this->checkPermission($codeData[$moduleAction]);
                } else if (isset($codeData[$module])) {
                    $this->checkPermission($codeData[$module]);
                }
            }
        }

        // 过滤频道
        $numberArray = BackendNumShowInterface::getNumber($this->userInfo);
        foreach ($channels as $key => $tmp) {
            if (isset($numberArray[$tmp['code']])) {
                $channels[$key]['text'] .= ' <font color="red">'.$numberArray[$tmp['code']].'</font>';
            }
            $code = empty($tmp['banner_code']) ? $key : $tmp['banner_code'];
            if ($code != 'default' && !isset($permissions[$code])) {
                unset($channels[$key]);
            } elseif (!$this->deptInfo['is_contract'] && $code == 'mbs_contract') {
                unset($channels[$key]);
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
                if ($rediUrl && $_SERVER['REQUEST_URI'] != $rediUrl) {
                    //判断业管是否欠费，欠费则跳转到缴费页面
                    $payUrl = "http://bc.corp.273.cn/pay/dept/iframePaymentManagement";
                    if ($this->userInfo['user']['role_id'] == 27) {
                        require_once API_PATH . '/interface/MbsDeptClauseInterface.class.php';
                        $hasRight = MbsDeptClauseInterface::hasRight(array('dept_id'=>$this->userInfo['user']['dept_id'],'right'=>1));
                        if (!$hasRight) {
                            ResponseUtil::redirect($payUrl);
                        }
                    }
                    $host = $_SERVER['HTTP_HOST'];
                    ResponseUtil::redirect('http://' . $host . $rediUrl);
                }
            }
            // 设置一些模板变量
            if (defined('SET_MBS_MODULE')) {
                $this->assign('mbsAppModule', SET_MBS_MODULE);
            }
            $channelInfo['url'] = $channel;
            $this->view->assign('channelInfo', $channelInfo);
            
            $this->view->assign('channels', $channels);
            
            $this->view->assign('userInfo', $this->userInfo);
            
            $this->view->assign('deptInfo', $this->deptInfo);
            
            $changePwdUrl = SsoInterface::getChangePwdUrl();
            //print_r($changePwdUrl); exit;
            $this->view->assign('changePwdUrl', $changePwdUrl);
            
            $logoutUrl = SsoInterface::getLogoutUrl();
            
            $this->view->assign('logoutUrl', $logoutUrl);
            //home.273.com.cn
            $id = rand(11111,99999);
            $key = $this->makeKey($id, $this->userInfo['user']['username']);
            $commulicationUrl = 'http://home.273.com.cn/member/login.php?onlineId='.intval($id).'&username='.intval($this->userInfo['user']['username']).'&key='.$key;
            $this->view->assign('commulicationUrl', $commulicationUrl);
            $videoUrl = 'http://mbs.corp.273.cn/mbs_video/videoAll/iframeIndex';
            $this->view->assign('videoUrl', $videoUrl);
            //高级搜索相关变量
            $proList = Search::getProList();
            $this->view->assign('provinceList',$proList);
            $searchPathUrl = '/mbs_index/Search/advancedSearch';
            $this->view->assign('searchPathUrl', $searchPathUrl);
        }
    }

    public function makeKey($onlineId, $userId, $mid = null) {
        $string = md5($onlineId . $userId . $mid);
        $array = str_split($string, 2);
        $newSort = array(4, 5, 6, 0, 1, 2, 13, 14, 15, 3, 7, 8, 12, 11, 10, 9);
        $newArray = array();
        foreach ($newSort as $k) {
            $newArray[] = $array[$k];
        }
        return implode('', $newArray);
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
        if ($code=='video' || $code == 'shop') {
            return true;
        }
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
                    setcookie('m_AUTH_STRING', '', time()-1, '/', '.corp.273.cn');
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
                        setcookie('m_AUTH_STRING', $newPassport, 0, '/', '.corp.273.cn');
                    } else {
                        setcookie('m_AUTH_STRING', '', time()-1, '/', '.corp.273.cn');
                        $error = 1;
                    }
                } else {
                    $this->userInfo = $sessUesrInfo;
                    return true;
                }
            }
            if (!$error) {
                /*
                include_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';
                session_start();
                LoggerGearman::logWarn(array(
                    'data' => $_COOKIE['m_AUTH_STRING'] .  var_export($_SESSION, true) . var_export($_SERVER, true),
                    'identity' => 'sso.debug.' . $_COOKIE['m_AUTH_STRING'],
                ));
                */
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
                if ($ret['user'] && $ret['user']['username']) {
                    $ret['car_user_info'] = CarUserInterface::getRowUser(array( // car_user 客服后台用户信息
                        'filters'    => array(array('mbs_user_id', '=', $ret['user']['username'])),
                    ));
                }
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
                    setcookie('m_AUTH_STRING', '', time()-1, '/', '.corp.273.cn');
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
    
    //检测是否需要显示门店管理处的服务之星
    private function checkStarTips() {
        if (empty($this->userInfo['user']['dept_id'])) {
            return false;
        }
        include_once API_PATH . '/interface/mbs/MbsUserInterface2.class.php';
        $uParams = array(
            'filters' => array(
                array('dept_id', '=', $this->userInfo['user']['dept_id']),
                array('status', '=', 1),
                array('is_server_star', '=', 1),
            ),
            'fields' => 'id',
            'limit' => 1,
        );
        
        $starUserList = MbsUserInterface2::getUserList($uParams);
        $cn = $starUserList['total'];
        $showTips = $cn > 0 ? 0 : 1;
        if ($showTips && ($this->userInfo['user']['role_id'] == 26 || $this->userInfo['user']['role_id'] == 27)) {
            $showTips = 1;
        } else {
            $showTips = 0;
        }
        $this->view->assign('showStarTips', $showTips);
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
                $numberArray = BackendNumShowInterface::getNumber($this->userInfo);
                $menuData    = $this->menu->getData();
                if (is_array($menuData['menu'])) {
                    foreach ($menuData['menu'] as $mKey => $mItem) {
                        $childMenu = $mItem['menu'];
                        if (is_array($childMenu)) {
                            foreach ($childMenu as $cKey => $cItem) {
                                //数量的KEY
                                $numKey = urlencode($cItem['text']) . $cItem['url'] . $cItem['code'];
                                if ($numberArray[$numKey]) {
                                    $menuData['menu'][$mKey]['menu'][$cKey]['text'] .= '<span class="mr10">' . $numberArray[$numKey] . '</span>';
                                }
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
/*
    protected function renderError($msg='操作失败，请重试！ ', $code=1) {
        $routeParams = Bootstrap::getRouteParams();
        $url = '/default/error/';
        if ($routeParams['is_ajax']) {
            $url .= 'ajax/';
        } else if ($routeParams['is_iframe']) {
            $url .= 'iframe/';
        } else {
            $url .= 'default/';
        }
        $url .= '?errorMessage=' . rawurlencode($msg) . '&errorCode=' . rawurlencode($code);
        
        $this->redirect($url);
    }
    
    protected function redirect($url) {
        if (!empty(BackendPageConfig::$JSONP_CALLBACK)) {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= BackendPageConfig::$JSONP_CALLBACK_NAME . '=' . rawurlencode(BackendPageConfig::$JSONP_CALLBACK);
        }
        
        $routeParams = Bootstrap::getRouteParams();
        
        ResponseUtil::redirect($url, $routeParams['is_iframe']);
    }
    */
}
