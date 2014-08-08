<?php
require_once API_PATH . '/interface/mbs/CarBuyInterface.class.php';
require_once API_PATH . '/interface/MbsUserInterface.class.php';
require_once API_PATH . '/interface/MbsDeptInterface.class.php';
require_once API_PATH . '/interface/mbs/MbsCustomerInterface.class.php';

class CarBuyPage {

    public function defaultAction() {

        $sessUesrInfo = Session::getValue('backend-user-info-mbs.bc');
        $id=(int)RequestUtil::getGET('id', 0);
        $carInfo = array();
        if ($id) {
            $carInfo = CarBuyInterface::getCarInfoById(array('id' => $id));
            if (!empty($carInfo)) {
                $followUser = MbsUserInterface::getInfoByUser(array('username' => $carInfo['follow_user_id']));
                $carInfo['follow_user_name'] = $followUser['real_name'];
                $dept = MbsDeptInterface::getDeptInfoByDeptId(array('id' => $carInfo['store_id']));
                $carInfo['store_name'] = $dept['dept_name'];
                $carInfo['dept_province'] = $dept['province'];
                $carInfo['dept_city'] = $dept['city'];
                $customer = MbsCustomerInterface::getInfoForId(array('id' => $carInfo['customer_id']));
                $carInfo['customer_name'] = $customer['real_name'];
                $carInfo['idcard'] = $customer['idcard'];
                
                // 判断是否本店的信息，如果是那么车主的电话就显示，如果不是车主的电话就置空
                if ($carInfo['store_id'] != $sessUesrInfo['user']['dept_id']) {
                    $carInfo['telephone'] = '';
                }
            }
        }
        echo json_encode($carInfo);
    }
}
