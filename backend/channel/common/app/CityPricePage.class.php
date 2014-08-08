<?php
require_once API_PATH . '/interface/mbs/MbsCityPriceStandardInterface.class.php';

class CityPricePage {

    public function defaultAction() {
        $city = (int)RequestUtil::getGET('city', 0);
        $info = array();
        if ($city) {
            $info = MbsCityPriceStandardInterface::getInfoByCity(array('city' => $city));
        }
        echo json_encode($info);
    }
}
