<?php
/**
 * Class BcController
 * @author chenchaoyang@iyuesao.com
 * @since  2015-01-11
 */
class BcController extends BaseController{
    protected $userInfo = array();
    protected $menu = array();
    /**
     * @var int 默认当前页数
     */
    protected $currentPage = 1;
    /**
     * @var int 默认每页显示数量
     */
    protected $pageSize = 20;
    /**
     * @var int 默认最大显示条数
     */
    protected $maxSize = 6000;

    public function __construct(){
        parent::__construct();
        $this->_setBcAutoload();
        $this->checkUser();
        $this->_filterMenu();
        $this->view->assign('menu', $this->menu);
        $this->view->assign('view', $this->view);
        $this->view->assign('menuUri', $this->_getUri());
        $this->view->assign('userInfo', $this->userInfo ? $this->userInfo : array());
    }

    /**
     *
     */
    private function _filterMenu() {
        $module = Router::getModule();
        if (empty($module)) {
            return;
        }
        $menuFile = PROJECT_PATH . '/modules/' . Router::getModule() . '/conf/menu.php';
        if (!is_file($menuFile)) {
            trigger_error('菜单文件不存在', E_USER_ERROR);
        }
        $menu = include $menuFile;
        if (empty($this->userInfo['code']) || !is_array($this->userInfo['code'])) {
            $this->errorAction(array('检测到您没有bc后台的任何权限，如有疑问请向总部管理员反映！'));
        }
        
        $menuInfo = array();
        if (!in_array('bc.*', $this->userInfo['code']) && !empty($menu) && is_array($menu)) {
            $code = $this->userInfo['code'];
            //如果不是超级管理员的话、执行过滤菜单操作
            foreach ($menu as $k => $m) {//$m:一级菜单配置数组
                if (in_array($m['code'], $code)) {
                    //拥有一级菜单的大code、说明有下面的所有子菜单权限
                    $menuInfo[$k] = $m;
                } else if (!empty($m['sub_menu']) && is_array($m['sub_menu'])) {
                    //判断各个子菜单是否有有权限的
                    $menuInfo[$k] = $m; //先默认全部子菜单都有权限、后面再判断来unset
                    foreach ($m['sub_menu'] as $subK => $subM) {
                        if (!in_array($subM['code'], $code)) {
                            //说明没有这个子菜单的权限,过滤掉
                            unset ($menuInfo[$k]['sub_menu'][$subK]);
                        }
                    }
                }
                
                if (empty($menuInfo[$k]['sub_menu']) || count($menuInfo[$k]['sub_menu']) == 0) {
                    //如果过滤完后、该一级菜单下面的子菜单都没有的话、把大菜单也过滤掉
                    unset($menuInfo[$k]);
                }
            }
        } else {
            $menuInfo = $menu;
        }
        $this->menu = $menuInfo;
    }
    
    /**
     * @brief 获取url里面的uri
     */
    private function _getUri() {
        $module = strtolower(Router::getModule());
        $uri = '';
        $first = '';
        if (!empty($module)) {
            $uri .= $module . '.'; //用于判断二级菜单是否命中
            $first .= $module . '.'; //用于判断一级菜单是否命中
        }
        $controller = strtolower(Router::getController());
        if (!empty($controller)) {
            $uri .= $controller . '.';
            $first .= $controller . '.';
        }
        $action = strtolower(Router::getAction());
        if (!empty($action)) {
            $uri .= $action;
        }
        
        return array(
            'first' => trim($first, '.'),
            'uri'   => trim($uri, '.'),
        );
    }
    
    /**
     * @brief 设置autoload目录
     */
    private function _setBcAutoload() {
        //把当前模块目录下面的conf目录的文件设为自动加载
        $moduleDir = PROJECT_PATH;
        $module = Router::getModule();
        if (!empty($module)) {
            $moduleDir .= '/modules/' . $module;
        }
        $moduleConf  = $moduleDir . '/conf/';
        if (is_dir($moduleConf)) {
            AutoLoader::setAutoDir($moduleConf);
        }
        
        //添加当前模块下面的common目录为自动加载
        if (is_dir($moduleDir . '/common/')) {
            AutoLoader::setAutoDir($moduleDir . '/common/');
        }
        //添加当前模块下面的include目录为自动加载
        if (is_dir($moduleDir . '/include/')) {
            AutoLoader::setAutoDir($moduleDir . '/include/');
        }
    }

    /**
     * @brief 获取用户信息
     */
    public function checkUser() {
        //bc后台的权限，若以后增加其他后台需要配置成数组
        $permissionApp = 'bc';

        //初始化rpc 
        $userClient = Rpc::client('sso.user');

        //cookie中读取m_AUTH_STRING
        $cookieMAuthString = $_COOKIE['m_AUTH_STRING'];

        //获取BC后台的URL，每个人的URL都不同，UrlConfig.class.php中配置自己本地的url
        $bcUrl = UrlConfig::getBcUrl();
        //如果cookie中的数据正常，自动登录并从Session中获取用户信息
        if(!empty($cookieMAuthString)) {
            $sessUesrInfo = Session::getValue('backend-user-info-' . $permissionApp);
            $userInfoErr = 0;
            $TicketInfo = $userClient->decodeSsoTicket($cookieMAuthString);
            if (!empty($sessUesrInfo['user']['username']) && $cookieMAuthString == $sessUesrInfo['cookie']) {
                if(count($TicketInfo) < 4) {
                    setcookie('m_AUTH_STRING', '', time()-1, '/', $bcUrl);
                    $userInfoErr = 1;
                } elseif(time() - $TicketInfo['3'] > 300) {
                    $newTicket = $userClient->encodeSsoTicket($TicketInfo[0], $sessUesrInfo['user']['passwd']);
                    setcookie('m_AUTH_STRING', $newTicket, 0, '/', $bcUrl);
                    $this->userInfo = $sessUesrInfo;
                    return true;
                } else {
                    $this->userInfo = $sessUesrInfo;
                    return true;
                }
            }

            //userInfo为空或者错误的时候，从数据库中获取用户信息
            if(!$userInfoErr) {
                $userInfo['code'] = Permission::getCode($TicketInfo[0]);
                $userInfo['user'] = $userClient->getUserInfo('*', array('username' => $TicketInfo[0]));
                $userInfo['group_name'] = Permission::getGroupName($userInfo['user']['username']);
                $userInfo['cookie'] = $cookieMAuthString;
                if(!empty($userInfo['user']['username'])) {
                    Session::setValue('backend-user-info-' . $permissionApp, $userInfo);
                    $this->userInfo = $userInfo;
                    return true;
                } else {
                    setcookie('m_AUTH_STRING', '', time()-1, '/', $bcUrl);
                    $this->renderError('你没有权限，请尝试刷新页面，或重新登录', 'not-login');
                }
            }
        }
        Session::delete('backend-user-info-' . $permissionApp);
        Response::redirect('http://' . $bcUrl . '/sso/user/loginPage');
    }

    /**
     * @desc 拼接get参数
     * @return string
     */
    public function getQueryString() {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $queryString = isset($url['query']) ? $url['query'] : '';
        parse_str($queryString,$params);
        return http_build_query($params);
    }

}