<?php
class Net_Gearman_Job_logCollector extends Net_Gearman_Job_Common{
    
    public function run($params){

        //$params = json_decode($json);
        LogInterface::add($params);
    }
}