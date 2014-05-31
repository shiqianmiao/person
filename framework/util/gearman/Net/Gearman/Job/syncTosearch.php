<?php
class Net_Gearman_Job_syncTosearch extends Net_Gearman_Job_Common{
    public function getField() {
        $carSaleKey = "";
        $carSaleKeyArray = array();
        foreach (CarSaleElasticConfig::$carSaleMapping as $key=>$value) {
            if ($key == 'cover_photo_url') {
                continue;
            }
            $carSaleKeyArray[] = $key;
        }
        $carSaleKey = implode(',',$carSaleKeyArray);
        $carSaleExtKey = "";
        $carSaleExtKeyArray = array();
        foreach (CarSaleExtElasticConfig::$carSaleExtMapping as $key=>$value) {
            $carSaleExtKeyArray[] = $key;
        }
        $carSaleExtKey = implode(',',$carSaleExtKeyArray);
        return array($carSaleKey, $carSaleExtKey);
    }
    public function run($params){ 
        $postId = $params['id'];
        if (empty($postId)) {
            return 0;
        }
        $db = DBMysqli::createDBHandle(DBConfig::$SERVER_MASTER, DBConfig::DB_CAR);
        list($carSaleField, $carSaleExtField) = $this->getField();
        sleep(1);
        $sql = "select " . $carSaleField . " from car_sale where id=" . $postId;
        $postInfo = DBMysqli::queryRow($db, $sql);
        if (empty($postInfo)) {
            return 0;
        }
        $client = ClientElastic::connection(ElasticSearchConfig::$CAR_SALE_SERVER);
        if ($postInfo['sale_status'] == 1 || $post['status'] > 1) {
            //删除id的索引
            $client->delete($postId);
            //走入数据移动到car_sale_bak

            return;
        } else {
            $sql = "select " . $carSaleExtField . " from car_sale_ext where car_id=" . $postId;
            $postExtInfo = DBMysqli::queryRow($db, $sql);
            if (empty($postExtInfo)) {
                return;
            }
            $postInfo = array_merge($postInfo, $postExtInfo);
        }
        if (!empty($postInfo['cover_photo'])) {
            $postInfo['cover_photo_url'] = $postInfo['cover_photo'];
            $postInfo['cover_photo'] = 1;
        } else {
            $postInfo['cover_photo_url'] = '';
            $postInfo['cover_photo'] = 0;
        }
        $fileName = '/tmp/syncTosearch.log';
        file_put_contents($fileName, json_encode($postInfo)."\n", FILE_APPEND | LOCK_EX);
        //删除id的索引
        $ret = $client->delete($postId);
        //重建索引
        if (isset($ret['ok']) && $ret['ok'] === true) {
            $ret = $client->index($postInfo, $postId);
        }
    }
}
