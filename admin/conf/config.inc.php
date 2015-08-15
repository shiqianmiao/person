<?php
/**
 * @file                 $HeadURL$
 * @version              $Rev$
 * @lastChangeBy         $LastChangedBy$
 * @lastmodified         $LastChangedDate$
 */
//开启调试模式
$_SERVER['DEBUG'] = true;
define('PROJECT_PATH', dirname(dirname(__FILE__)));
define('FRAMEWORK_PATH', PROJECT_PATH . '/../framework');
define('API_PATH', FRAMEWORK_PATH . '/../api');
define('CONF_PATH', FRAMEWORK_PATH . '/../conf/conf');