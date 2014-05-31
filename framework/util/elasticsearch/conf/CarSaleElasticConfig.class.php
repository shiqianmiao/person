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
class CarSaleElasticConfig {

    public static $carSaleMapping = array(
        'create_time' => array(
                'type' => 'integer',
            ),
        'update_time' => array(
                'type' => 'integer',
            ),
        'type_id' => array(
                'type' => 'integer',
            ),
        'brand_id' => array(
                'type' => 'integer',
            ),
        'maker_id' => array(
                'type' => 'integer',
            ),
        'series_id' => array(
                'type' => 'integer',
            ),
        'model_id' => array(
                'type' => 'integer',
            ),
        'cover_photo' => array(
                'type' => 'integer',
            ),
        'cover_photo_url' => array(
                'type' => 'string',
                'index' => 'no',
            ),
        'deal_province_id' => array(
                'type' => 'integer',
            ),
        'deal_city_id' => array(
                'type' => 'integer',
            ),
        'deal_district_id' => array(
                'type' => 'integer',
            ),
        'car_color' => array(
                'type' => 'integer',
            ),
        'price' => array(
                'type' => 'float',
            ),
        'kilometer' => array(
                'type' => 'float',
            ),
        'description' => array(
                'type' => 'string',
                'index' => 'no',
            ),
        'card_time' => array(
                'type' => 'integer',
            ),
        'displacement' => array(
                'type' => 'float',
            ),
        'gearbox_type' => array(
                'type' => 'integer',
            ),
        'safe_force_time' => array(
                'type' => 'integer',
            ),
        'year_check_time' => array(
                'type' => 'integer',
            ),
        'safe_business_time' => array(
                'type' => 'integer',
            ),
        'transfer_num' => array(
                'type' => 'integer',
            ),
        'maintain_address' => array(
                'type' => 'integer',
            ),
        'use_type' => array(
                'type' => 'integer',
            ),
        'sale_type' => array(
                'type' => 'integer',
            ),
        'store_id' => array(
                'type' => 'integer',
            ),
        'follow_user_id' => array(
                'type' => 'integer',
            ),
        'car_body_type' => array(
                'type' => 'integer',
            ),
        'status' => array(
                'type' => 'integer',
            ),
        'ext_phone' => array(
                'type' => 'string',
                'index' => 'no',
            ),
        'ckb_check' => array(
                'type' => 'integer',
            ),
        'ckb_level' => array(
                'type' => 'integer',
            ),
        'list_rank' => array(
                'type' => 'integer',
            ),
        'buy_price' => array(
                'type' => 'float',
                'index' => 'no',
            ),
        'first_price' => array(
                'type' => 'float',
                'index' => 'no',
            ),
        'ad_note' => array(
                'type' => 'string',
                'index' => 'no',
            ),
        'year_group' => array(
                'type' => 'integer',
            ),
        'is_repeat' => array(
                'type' => 'integer',
            ),
        'order_status' => array(
                'type' => 'integer',
            ),
        'index_update_time' => array(
                'type' => 'integer',
            ),
        'is_approve' => array(
                'type' => 'integer',
            ),
        'stick_time' => array(
                'type' => 'integer',
            ),
        'source_type' => array(
                'type' => 'integer',
            ),
        'sale_status' => array(
                'type' => 'integer',
            ),
        'visit_time' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'safe_business_cash' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'owner_user_id' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'follow_time' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'car_type_check' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'mark_type' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'condition_id' => array(
                'type' => 'integer',
            ),
        'member_id' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'member_name' => array(
                'type' => 'string',
                'index' => 'no',
            ),
        'add_source' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'info_type' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'is_seven' => array(
                'type' => 'integer',
            ),
        'is_sync' => array(
                'type' => 'integer',
            ),
        'model_text' => array(
                'type' => 'string',
                'index' => 'no',
            ),
        'customer_main_id' => array(
                'type' => 'integer',
                'index' => 'no',
            ),
        'emission_standards' => array(
                'type' => 'integer',
            ),
        'title' => array(
                'type' => 'string',
                'indexAnalyzer' => 'ik',
                'searchAnalyzer' => 'ik',
            ),
    );
    public static $carSaleConfig = array(
        "enable" => "false",
    );
}
