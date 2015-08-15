<?php
class Net_Gearman_Job_dispatchJob extends Net_Gearman_Job_Common{
    
    public function run($params){

        //$params =  json_decode($str);
        Bootstrap::run($params);
    }
}