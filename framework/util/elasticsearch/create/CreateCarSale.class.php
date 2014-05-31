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
require_once FRAMEWORK_PATH . '/util/elasticsearch/conf/CarSaleElasticConfig.class.php';
require_once FRAMEWORK_PATH . '/util/elasticsearch/conf/CarSaleExtElasticConfig.class.php';
require_once FRAMEWORK_PATH . '/util/elasticsearch/include/ClientElastic.class.php';

class CreateCarSale {
    public static function run() {
        $client = ClientElastic::connection(ElasticSearchConfig::$CAR_SALE_SERVER);
        $ret = $client->map(array_merge(CarSaleElasticConfig::$carSaleMapping, CarSaleExtElasticConfig::$carSaleExtMapping),
                CarSaleElasticConfig::$carSaleConfig
            );
        var_dump($ret);
    }
}
CreateCarSale::run();
