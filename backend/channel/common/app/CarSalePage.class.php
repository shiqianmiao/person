<?php
require_once FRAMEWORK_PATH . '/util/common/Session.class.php' ;
require_once BACKEND . '/widget/DataGridWidget.class.php';
require_once API_PATH . '/interface/CarSaleInterface.class.php';
require_once API_PATH . '/interface/CarSaleBakInterface.class.php';
require_once API_PATH . '/interface/MbsUserInterface.class.php';
require_once API_PATH . '/interface/MbsDeptInterface.class.php';
require_once API_PATH . '/interface/mbs/MbsCustomerInterface.class.php';

class CarSalePage extends BasePage {

    public function defaultAction() {

        $sessUesrInfo = Session::getValue('backend-user-info-mbs.bc');
        $id = (int) RequestUtil::getGET('id', 0);
        $customer = (int) RequestUtil::getGET('customer', 0);
        $carInfo = array();
        if ($id) {
            $carInfo = CarSaleInterface::getCarDetail(array('id' => $id));
            if (empty($carInfo)) {
                $carInfo = CarSaleBakInterface::getCarDetail(array('id' => $id));
            }
            if (!empty($carInfo)) {
                $followUser = MbsUserInterface::getInfoByUser(array('username' => $carInfo['follow_user_id']));
                $carInfo['follow_user_name'] = $followUser['real_name'];
                $dept = MbsDeptInterface::getDeptInfoByDeptId(array('id' => $carInfo['store_id']));
                $carInfo['store_name'] = $dept['dept_name'];
                $customer = MbsCustomerInterface::getInfoForId(array('id' => $carInfo['customer_id']));
                $carInfo['customer_name'] = $customer['real_name'];
                $carInfo['idcard'] = $customer['idcard'];
                $carInfo['safe_force_time'] = $carInfo['safe_force_time']>0 ? date('Y-m-d', $carInfo['safe_force_time']) : '';
                if ($carInfo['safe_business_time']) {
                    $carInfo['safe_business_time'] = date('Y-m-d', $carInfo['safe_business_time']);
                } else {
                    $carInfo['safe_business_time'] = '';
                }
                if ($carInfo['card_time']) {
                    $carInfo['card_time'] = date('Y-m-d', $carInfo['card_time']);
                } else {
                    $carInfo['card_time'] = '';
                }
                
                // 判断是否本店的信息，如果是那么车主的电话就显示，如果不是车主的电话就置空
                if ($carInfo['store_id'] != $sessUesrInfo['user']['dept_id']) {
                    $carInfo['telephone'] = '';
                } else {
                    //去获取加密后的电话号码
                    include_once API_PATH . '/interface/MbsCarProtectInfoInterface.class.php';
                    $params = array(
                        'fields' => '*',
                        'cond'   => array('car_id' => $id),
                    );
                    $result = MbsCarProtectInfoInterface::getRow($params);
                    $carInfo['telephone'] = !empty($result['mobile']) ? $result['mobile'] : '';
                }
            }
        }
        echo json_encode($carInfo);
    }
}
