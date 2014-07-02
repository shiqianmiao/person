<?php

require_once WEB_V3 . '/common/WebBasePage.class.php' ;
//require_once(FRAMEWORK_PATH. '/util/gearman/ClientGearman.class.php');

/**
 *
 * @author    miaoshiqian
 * @since     2013-3-7
 * @desc      
 */
class DefaultPage extends WebBasePage{
    
    function defaultAction() {

        $staHtml .= 'g.js,config.js,app/person/web/css/index_v2.css';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));

        $this->render(array('staHtml' => $staHtml), 'default.php');

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

