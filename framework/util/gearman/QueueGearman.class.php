<?php
/**
 * 基于gearman的队列
 *
 * @author    林云开 <cloudop@gmail.com>
 * @since     2013-2-28
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 */
require_once('ClientGearman.class.php');
class QueueGearman{
    
    public static function enQueue($module, $action, $args){
        
        $gearman = ClientGearman::instance();
        $params = array('module' => $module, 'action' => $action, 'args' => $args);
        return $gearman->doBackground('dispatchJob', $params);
    }
}
