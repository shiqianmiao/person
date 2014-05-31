<?php
require_once dirname(__FILE__) . '/../util/common/Helper.class.php';
require_once dirname(__FILE__) . '/../util/http/ResponseUtil.class.php';

class BaseView {
    // 模板目录
    private static $_dirPath = array();

    public function __construct($config = array()) {
        if (!empty($config['template_path'])) {
            self::addDir($config['template_path']);
        }
        
        if (!empty($config['helper_path'])) {
            if (is_string($config['helper_path'])) {
                Helper::addDir($config['helper_path']);
            } else if (is_array($config['helper_path'])) {
                foreach ($config['helper_path'] as $dir) {
                    Helper::addDir($dir);
                }
            }
        }
    }
    
    public function addHelperDir($dir) {
        return Helper::addDir($dir);
    }
    
    public static function addDir($dir) {
        if (empty($dir)) return false;
        
        if (is_string($dir)) {
            if (is_dir($dir) && !in_array($dir, self::$_dirPath)) {
                array_unshift(self::$_dirPath, $dir);
                return true;
            }
        } else if (is_array($dir)) {
            foreach ($dir as $_dir) {
                if (is_dir($_dir) && !in_array($_dir, self::$_dirPath)) {
                    array_unshift(self::$_dirPath, $_dir);
                }
            }
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

    public function output($content, $contentType = '',  $jsonp = '') {
        if (!empty($contentType)) {
            ResponseUtil::setContentType($contentType, $jsonp);
        }
        ResponseUtil::output($content);
    }
    
    public function fetch($templateFile, $values = array()) {
        if ($values) {
            $this->assign($values);
        }

        ob_start();

        $error = true;
        foreach (self::$_dirPath as $tplPath) {
            if (is_file($tplPath . '/' . $templateFile)) {
                $error = false;
                include $tplPath . '/' . $templateFile;
                break;
            }
        }
        
        $ret = ob_get_contents();
        ob_end_clean();

        if ($error) {
            trigger_error('模块文件不存在[' . $templateFile . ']', E_USER_ERROR);
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
        foreach (self::$_dirPath as $tplPath) {
            if (is_file($tplPath . '/' . $fileName)) {
                extract((array)$params);
                include $tplPath . '/' . $fileName;
                return;
            }
        }
        trigger_error('文件"'.$fileName.'"不存在', E_USER_NOTICE);
    }
}
