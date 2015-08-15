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
class CarSaleExtElasticConfig {
    public static $carSaleExtMapping = array(
            'plate_province_id' => array(
                'type' => 'integer',
                ),
            'plate_city_id' => array(
                'type' => 'integer',
                ),
            'seller_name' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'telephone' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'telephone2' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'create_user_id' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'safe_business' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'ip' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'car_number' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'ckb_time' => array(
                'type' => 'integer',
                ),
            'ckb_check_user_id' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'ckb_store_id' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'ckb_content' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'freeze_time' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'customer_id' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'update_user' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'note' => array(
                'type' => 'string',
                'index' => 'no',
                ),
            'stop_time' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'order_source' => array(
                'type' => 'integer',
                ),
            'check_time' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'check_user_id' => array(
                'type' => 'integer',
                'index' => 'no',
                ),
            'inside_note' => array(
                'type' => 'string',
                'index' => 'no',
                ),
    );
}



