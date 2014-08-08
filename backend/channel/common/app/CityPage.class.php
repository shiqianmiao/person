<?php
require_once API_PATH . '/interface/LocationInterface.class.php'; 
require_once API_PATH . '/interface/MbsUserInterface.class.php'; 
require_once API_PATH . '/interface/MbsDeptInterface.class.php'; 
class CityPage {
    public function defaultAction() { 
        $pro = (int) RequestUtil::getGET('province', 0);
        $curCity = (int) RequestUtil::getGET('city', 0); // 指定选中的城市
        $cityInfo = LocationInterface::getCityListByProvinceId(array('province_id' => $pro));
        $html = '<option value="">--城市--</option>';
        foreach ($cityInfo as $city) {
            $selected = $city['id'] == $curCity ? ' selected ' : '';
            $html .= "<option value='{$city['id']}'{$selected}>{$city['name']}</option>";
        }
        echo $html;
    }
    public function getDeptAction() {
        $city = (int) RequestUtil::getGET('city', 0);
        $pro = (int) RequestUtil::getGET('province', 0);
        $deptInfo = array();
        if ($city) {
            $deptInfo = MbsDeptInterface::getDeptsByCity(array('id' => $city));
        } elseif ($pro) {
            $deptInfo = MbsDeptInterface::getDeptsByProvince(array('id' => $pro));
        }
        $html = '<option value="" selected>--门店--</option>';
        foreach ($deptInfo as $dept) {
            $html .= '<option value="'.$dept['id'].'" >'.$dept['dept_name'].'</option>';
        }
        echo $html;
    }

    /**
     * 根据城市编号获取区县信息
     */
    public function getDistrictAction() {
        $cityId = (int) RequestUtil::getGET('city', 0);
        $curDistrict = (int) RequestUtil::getGET('district', 0);
        $html = '';
        if ($cityId > 0) {
            $districtList = LocationInterface::getDistrictListByCityId(array(
                'city_id'   => $cityId,
            ));
            if ($districtList) {
                foreach ($districtList as $district) {
                    $selected = $curDistrict == $district['id'] ? " selected " : '';
                    $html .= "<option value='{$district['id']}'{$selected}>{$district['name']}</option>";
                }
            }
        }

        echo json_encode(array(
            'html'    => "<option value=''>区/县</option>" . $html,
            'error'   => empty($html) ? 1 : 0, // 1:没有区县信息
        ));
    }
    
    public function getUserAction() {
        $dept = (int) RequestUtil::getGET('dept_id', 0);
        $departure = RequestUtil::getGET('departure', 0);
        $users = array();
        $html = '<option value="" selected>人员</option>';
        if ($dept) { 
            $users = MbsUserInterface::getUsersByDept(array('dept_id'=>$dept));
            foreach ($users as $user) {
                $html .= '<option value="'.$user['username'].'" >'.$user['real_name'].'</option>';
            }
            if ($departure == 1) {
                $html .= '<option value="other">离职人员</option>';
            }
        }
        echo $html;
    }
}
