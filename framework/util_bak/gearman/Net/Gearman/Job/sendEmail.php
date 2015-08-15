<?php
class Net_Gearman_Job_sendEmail extends Net_Gearman_Job_Common{
    
    public function run($params){

        $requireParams = array(
            'title',
            'content',
            'mailList'
        );
        foreach ($requireParams as $value) {

            if (!isset($params[$value]) || $params[$value] == '') {
            	return ;
            }
        }
        $smtp = new smtp(SmtpConfig::$smtpServer, SmtpConfig::$smtpServerport, true, SmtpConfig::$smtpUser, SmtpConfig::$smtpPass);
        $smtp->debug = false;
        
        if (is_array($params['mailList'])) {
        	$mailList = implode(',', $params['mailList']);
        } elseif (is_string($params['mailList'])) {
            $mailList = $params['mailList'];
        } else {
            return ;
        }

        if (isset($params['mailType']) && in_array($params['mailType'], array('TEXT', 'HTML'))) {
        	$mailType = $params['mailType'];
        } else {
            $mailType = 'HTML';
        }

        if (isset($params['charset']) && in_array($params['charset'], array('gb2312', 'gbk', 'utf-8'))) {
        	$charset = $params['charset'];
        } else {
            $charset = 'utf-8';
        }
        $r = $smtp->sendmail($mailList, SmtpConfig::$smtpUsermail, $params['title'], $params['content'], $mailType, $charset);
    }
}