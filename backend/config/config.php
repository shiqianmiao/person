<?php
/*
 * @desc 全局配置文件
 * @author 缪石乾 <miaosq@273.cn>
 * @since 2013-9-3
 */

require_once dirname(__FILE__) . '/../../conf/config.inc.php';

if (!defined('DEBUG_STATUS')) {
    define('DEBUG_STATUS', true);
}

if (! defined('BACKEND')) {
    define('BACKEND',            dirname(dirname(__FILE__)));  // Backend 根目录
}

if (! defined('FRAMEWORK_PATH')) {
    define('FRAMEWORK_PATH', dirname(__FILE__) . '/../../framework'); // 基础类库目录
}

if (! defined('API_PATH')) {
    define('API_PATH', dirname(__FILE__) . '/../../api'); // 基础类库目录
}
if (! defined('DATA_PATH')) {
    define('DATA_PATH', dirname(__FILE__) . '/../../data'); // 基础类库目录
}

if (! defined('CONF_PATH')) {
    define('CONF_PATH', dirname(__FILE__) . '/../../conf/debug');
}

if (! defined('COM_PATH')) {
    define('COM_PATH', dirname(__FILE__) . '/../../conf/common');
}
