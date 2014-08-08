<?php

/**
 * @brief 流程分发
 * @author aozhongxu
 */
class Dispatch {

    public static function run($module, $action) {

        $filePath   = SSO_SITE . '/app/' . ucfirst($module) . 'Page.class.php';
        $className  = ucfirst($module) . 'Page';
	$action = preg_replace('/(^[A-Z])/', strtolower(substr($action, 0, 1)), $action);
        $actionName = $action . 'Action';
        
        require_once $filePath;
        $app = new $className();
        $app->$actionName();
    }

}
