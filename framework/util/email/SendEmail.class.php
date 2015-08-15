<?php
/**
 * @brief 简介：发送邮件工具类
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年2月10日 上午11:11:17
 */
require_once dirname(__FILE__) . '/../../../conf/common/SmtpConfig.class.php';
require_once dirname(__FILE__) . '/Email.class.php';

class SendEmail {
    /**
     * @brief 发送邮件方法
     * @param $params['title'] 邮件标题
     * @param $params['content'] 邮件内容
     * @param $params['mailList'] 收件箱、可以一个、可以是数组发送多个
     */
    public static function send($params) {
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
        $smtp = new smtp(SmtpConfig::$smtpServer, SmtpConfig::$smtpServerport, true, SmtpConfig::$smtpUser, base64_decode(SmtpConfig::$smtpPass));
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