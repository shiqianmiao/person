<?php
/**
 * Class BcController
 * @author chenchaoyang@iyuesao.com
 * @since  2015-01-11
 */
class MsController extends BaseController{
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
        $this->_setCommonSta();
        $this->view->assign('view', $this->view);
    }

    /**
     * @desc 公共sta文件设置
     */
    private function _setCommonSta() {
        $commonStaHtml = 'g.js,config.js,app/agency/js/bootstrap.min.js,app/agency/css/bootstrap.min.css';
        //$commonStaHtml = 'app/agency/css/bootstrap.min.css';
        $commonStaHtml = $this->view->helper('sta', array('files' => $commonStaHtml));
        $this->view->assign('commonStaHtml', $commonStaHtml);
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
}