<?php
require_once dirname(__FILE__) . '/Helper.class.php';
require_once dirname(__FILE__) . '/../util/http/Response.class.php';
require_once dirname(__FILE__) . '/Router.class.php';

class View {
    // 模板目录
    private $_dirs = array();

    public function __construct() {
        self::addDir(PROJECT_PATH . '/view');
        if (Router::getModule() != '') {
            self::addDir(PROJECT_PATH . '/modules/' . Router::getModule() . '/view');
        }
    }
    
    public function addDir($dir) {
        if (empty($dir)) return false;
        
        if (is_string($dir) && is_dir($dir) && !in_array($dir, $this->_dirs)) {
            array_unshift($this->_dirs, $dir);
            return true;
        } 
        
        return false;
    }

    /**
     * 给模板赋值
     * @param type $key
     * @param type $val
     */
    public function assign($key, $val='') {
        if (is_array($key)) {
            foreach ((array)$key as $k => $v){
                $this->$k = $v;
            }
        } else {
            $this->$key = $val;
        }
    }
    
    public function fetch($templateFile, $values = array()) {
        if ($values) {
            $this->assign($values);
        }

        ob_start();

        $error = true;
        foreach ($this->_dirs as $tplPath) {
            if (is_file($tplPath . '/' . $templateFile)) {
                $error = false;
                include $tplPath . '/' . $templateFile;
                break;
            }
        }
        
        $ret = ob_get_contents();
        ob_end_clean();

        if ($error) {
            throw new Exception('模块文件不存在[' . $templateFile . ']');
        }

        return $ret;
    }
    
    /**
     * 使用插件
     * @param 插件名称 $funcName 对应plugin/helper/$funcName_helper.php中的$funcName_helper方法
     * @param mix $params
     * @return mix
     */
    public function helper($funcName, $params=array()) {
        return Helper::call($funcName, $params);
    }

    /**
     * 载入子模板，在模板中使用
     * @param string $fileName 子模板文件名，相对于模板目录指定
     * @param array $params 要传给子模板的变量
     */
    protected function load($fileName, $params=array()) {
        foreach ($this->_dirs as $tplPath) {
            if (is_file($tplPath . '/' . $fileName)) {
                extract((array)$params);
                include $tplPath . '/' . $fileName;
                return;
            }
        }
        throw new Exception('文件"'.$fileName.'"不存在');
    }
}
