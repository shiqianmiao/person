<?php
/**
 * @package              v3
 * @subpackage           
 * @author               $Author:   weihongfeng$
 * @file                 $HeadURL$
 * @version              $Rev$
 * @lastChangeBy         $LastChangedBy$
 * @lastmodified         $LastChangedDate$
 * @copyright            Copyright (c) 2013, www.273.cn
 */
require dirname(__FILE__) . '/../../../../conf/config.inc.php';
require_once CONF_PATH . '/elasticsearch/ElasticSearchConfig.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';
require_once FRAMEWORK_PATH . '/util/elasticsearch/conf/CarSaleElasticConfig.class.php';
require_once FRAMEWORK_PATH . '/util/elasticsearch/conf/CarSaleExtElasticConfig.class.php';
require_once FRAMEWORK_PATH . '/util/elasticsearch/include/ClientElastic.class.php';
require_once FRAMEWORK_PATH . '/util/db/DBMysqli.class.php';

class CarSaleData {
    const CAR_NUM = 1000;
    public static function getCar($field1, $field2, $id=0) {
        $sql = "select id," . $field1 . " from car_sale where id>" . $id . " order by id asc limit 0," . self::CAR_NUM;
        $db = DBMysqli::createDBHandle(DBConfig::$SERVER_SLAVE, DBConfig::DB_CAR);
        $carInfo = DBMysqli::queryAll($db, $sql);
        $maxId = 0;
        if (empty($carInfo)) {
            return array(array(), $maxId);
        }
        $inArray = $carInfo[0]['id'];
        $carIndexInfo = array();
        foreach ($carInfo as $key=>$value) {
            $carIndexInfo[$value['id']] = $value;
            if ($key == 0) {
                continue;
            }
            $inArray .= (',' . $value['id']);
            $maxId = $value['id'];
        }
        $sqlExt = "select car_id," . $field2 . " from car_sale_ext where car_id in (" . $inArray . ")";
        $carExtInfo = DBMysqli::queryAll($db, $sqlExt);
        foreach ($carExtInfo as $key=>$value) {
            if (!isset($carIndexInfo[$value['car_id']])) {
                unset($carIndexInfo[$value['car_id']]);
                continue;
            }
            $carIndexInfo[$value['car_id']] = array_merge($value, $carIndexInfo[$value['car_id']]);
        }
        return array($carIndexInfo, $maxId);
    }
    public static function getField() {
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

    public static function run() {
        echo "---------开始导入全量------------\n";
        $maxId = 0;
        $carInfo = array();
        list($field1, $field2) = self::getField();
        $client = ClientElastic::connection(ElasticSearchConfig::$CAR_SALE_SERVER);
        $num = 0;
        do {
            list($carInfo, $maxId) = self::getCar($field1, $field2, $maxId);
            $numNow = count($carInfo);
            echo "------准备导入".$numNow."条数据-----\n";
            if ($maxId != 0 && !empty($carInfo)) {
                $client->beginBulk();
                foreach ($carInfo as $key=>$value) {
                    if ($value['cover_photo']) {
                        $value['cover_photo_url'] = $value['cover_photo'];
                        $value['cover_photo'] = 1;
                    } else {
                        $value['cover_photo_url'] = '';
                        $value['cover_photo'] = 0;
                    }
                    $id = $value['id'];
                    unset($value['id']);
                    unset($value['car_id']);
                    $client->index($value, $id);
                }
                $ret = $client->commitBulk();
                $client->refresh();
                if (isset($ret['items'])) {
                    $num += $numNow;
                    echo "-------本次导入".$numNow."条数据-----总共已导入".$num."条数据--------\n";
                } else {
                    echo "-----导入失败-----\n";
                }
            }
        } while ($maxId != 0);
        echo "导入结束\n";
    }
}
CarSaleData::run();
