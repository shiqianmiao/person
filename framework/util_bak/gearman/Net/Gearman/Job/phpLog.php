<?php
class Net_Gearman_Job_phpLog extends Net_Gearman_Job_Common{
    
    public function run($params){
        $model = new PhpLogModel();
        $model->insert($params);
    }
}
