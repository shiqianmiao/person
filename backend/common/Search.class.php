<?php
require_once API_PATH . '/interface/LocationInterface.class.php';

class Search {
    public static $TYPE_NAME = array(
            1   => '轿车',
            2   => '越野车/SUV',
            3   => '面包车',
            4   => '商务车/MPV',
            5   => '货车',
            6   => '跑车',
            7   => '皮卡',
            8   => '客车',
    );
    
    //全部颜色映射关系
    public static $COLOR_TEXT_ALL = array(                        
            2 => array('caption'=>'黑色','color'=>'#000000','bg'=>'#ffffff'),
            1 => array('caption'=>'白色','color'=>'#ffffff','bg'=>'#000000'),
            33 => array('caption'=>'灰色','color'=>'#94979C','bg'=>'#000000'),
            26 => array('caption'=>'红色','color'=>'#d81207','bg'=>'#000000'),
            35 => array('caption'=>'银白色','color'=>'#EFEFEF','bg'=>'#000000'),
            25 => array('caption'=>'橙色','color'=>'#f15e00','bg'=>'#000000'),
            7 => array('caption'=>'绿色','color'=>'#8FAD53','bg'=>'#000000'),
            13 => array('caption'=>'蓝色','color'=>'#072d98','bg'=>'#000000'),
            15 => array('caption'=>'粉红','color'=>'#ff9ed5','bg'=>'#000000'),
            16 => array('caption'=>'紫色','color'=>'#cd39a9','bg'=>'#000000'),
            20 => array('caption'=>'金色','color'=>'#dbcf97','bg'=>'#000000'),
            24 => array('caption'=>'黄色','color'=>'#f8be2e','bg'=>'#000000'),
            31 => array('caption'=>'褐色','color'=>'#994129','bg'=>'#000000'),
            3 => array('caption'=>'银灰色','color'=>'#D7DADF','bg'=>'#000000'),
            38 => array('caption'=>'香滨','color'=>'#f0daab','bg'=>'#000000'),
    );
    
    //搜索的信息归属
    public static $SEARCH_CAR_BELONG = array(
            '0' => '不限',
            '1' => '本店',
            '2' => '个人信息',
            '3' => '个人录入',
    );
    
    //变速箱类型
    public static $GEARBOX_TYPE = array(
            '0' => '不限',
            '1' => '手动',
            '2' => '自动',
    );
    
    //获取省列表
    public static function getProList() {
        $provinceList = LocationInterface::getProvinceListByChar();
        ksort($provinceList);
        return $provinceList;
    }
}