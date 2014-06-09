<?php

//require_once WEB_V3 . '/common/WebV3Page.class.php' ;
//require_once(FRAMEWORK_PATH. '/util/gearman/ClientGearman.class.php');

/**
 * 车源详情页控制器
 *
 * @author    戴志君 <dzjzmj@gmail.com>
 * @since     2013-3-7
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc      
 */
class DefaultPage {
    //put your code here
    
    function defaultAction() {
        echo '网站建设中...';
    }
    
    function testGearmanAction(){
        
        $gearman = ClientGearman::instance();
        $params = array('module' => 'WorkerExample', 'action' => 'resizeWorker', 'r' => rand(10000, 99999));
        $gearman->doBackground('dispatchJob', $params);
    }
    
    function testQueueAction(){
        
        require_once(FRAMEWORK_PATH. '/util/gearman/QueueGearman.class.php');
        $content = rand(10000, 99999);
        $sms = array('18659303230', $content);
        QueueGearman::enQueue('WorkerExample', 'smsQueue', $sms);
    }
    
    function developmentAction(){

        include_once API_PATH . '/interface/LogInterface.class.php';
        $t = array(
            'data' => 'ttt',
        );
        LogInterface::add($t);
    
        require_once(FRAMEWORK_PATH. '/util/gearman/LoggerGearman.class.php');
        //日志现在会记录在内网mysql里面 wcar库的 log表里面
        LoggerGearman::logWarn('今天啊，我说');
//        LoggerGearman::logDebug('天气还是不错的');
//        LoggerGearman::logInfo('但是呢，有点冷');
//        LoggerGearman::logFatal('肚子一天都不太舒服');
    }
    
    
    function cityAction() {
        echo 'city';
        print_r($_GET);
    }
}

