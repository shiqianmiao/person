<?php
class Net_Gearman_Job_paymentNotify extends Net_Gearman_Job_Common{
    
    public function run($params){

        include_once(CONF_PATH . '/payment/NotifyConfig.class.php');
        foreach (NotifyConfig::$arr as $val) {

            include_once(API_PATH . '/interface/' . $val[0] . '.class.php');
            //LogInterface::add($params);
            $r = call_user_func($val, $params);
            $count = 1;
            while ($r == false && $count < 3) {
                $count++;
                $r = call_user_func($val, $params);
            }
        }
    }
}
