<?php
/**
 * @brief 简介：自动加载类
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月9日 下午3:34:41
 */
class AutoLoader {
    /**
     * @brief 自动加载类文件
     * @param 需要自动加载的类名称,不需要自己传入
     */
    public static function autoLoad($className) {
        $fileName = $className . '.class.php';
        $paths = get_include_path();
        $paths = !empty($paths) ? explode(PATH_SEPARATOR, $paths) : array('');
        foreach ($paths as $path) {
            if (file_exists($path . $fileName)) {
                require_once $fileName;
                return true;
            }
        }
        
        throw new Exception("类{$fileName}不存在，请检查是否require了该文件.");
    }
    
    /**
     * @brief 设置自动加载目录
     * @param $path 您需要添加到自动加载里面的目录,如果有子目录的话，会自动递归加载类文件
     */
    public static function setAutoDir($path) {
        if (!is_dir($path)) {
            return false;
        }
        
        $path = rtrim($path, '/') . '/';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);
        
        //打开目录
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false) {
            //排除掉当前目录和上一个目录
            if ($file == "." || $file == "..") {
                continue;
            }
            $file = $path . $file;
            if (is_dir($file)) {
                self::setAutoDir($file);
            }
        }
    }
}

//注册自定义的自动加载方法
spl_autoload_register (array('AutoLoader', 'autoLoad'));