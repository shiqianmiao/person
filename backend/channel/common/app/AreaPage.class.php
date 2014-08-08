<?php
/**
 * @author 范孝荣 <fanxr@273.cn>
 * @since 12/12/13
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @version $id$
 * @desc 
 */
require_once API_PATH . '/interface/MbsDeptInterface.class.php';
require_once API_PATH . '/interface/mbs/MbsGroupInterface.class.php';
require_once API_PATH . '/interface/MbsUserInterface.class.php';

class AreaPage {

    public function defaultAction() {
        $pro = (int) RequestUtil::getGET('province_area', 0); //省级区域中心
        $curCity = (int) RequestUtil::getGET('city_area', 0); //市级区域中心
        $cityArea = MbsDeptInterface::getCityAreaByProvinceAreaId(array('province_area_id' => $pro));
        $html = '<option value="0">市级区域中心</option>';
        foreach ($cityArea as $city) {
            $selected = $city['id'] == $curCity ? ' selected ' : '';
            $html .= "<option value='{$city['id']}'{$selected}>{$city['dept_name']}</option>";
        }
        echo $html;
    }

    public function getDeptAction() {
        $city = (int) RequestUtil::getGET('city_area', 0);
        $deptInfo = array();
        if ($city) {
            $deptInfo = MbsDeptInterface::getDeptsInfoByParentId(array('parent_id' => $city, 'field' => '*'));
        }
        $html = '<option value="0" selected>门店</option>';
        foreach ($deptInfo as $dept) {
            $html .= '<option value="'.$dept['id'].'" >'.$dept['dept_name'].'</option>';
        }
        echo $html;
    }

    public function getGroupAction() {
        $dept = (int) RequestUtil::getGET('dept_id', 0);
        $groupInfo = array();
        if ($dept) {
            $groupInfo = MbsGroupInterface::getGroupsByDeptId(array('dept_id' => $dept));
        }
//        $html = '<option value="0" selected>业务组</option>';
        $html = '';
        foreach ($groupInfo as $group) {
            $html .= '<option value="'.$group['id'].'" >'.$group['group_name'].'</option>';
        }
        echo $html;
    }

    public function getUserAction() {
        $group = (int) RequestUtil::getGET('group_id', 0);
        $userInfo = array();
        if ($group) {
            $groupInfo = MbsGroupInterface::getGroupInfoById(array('id' => $group));
            $users = explode(',', $groupInfo['user_list']);
            $userInfo = MbsUserInterface::getInfoByIds($users);
            $html = '<option value="0" selected>业务员</option>';
            foreach ($userInfo as $user) {
                $html .= '<option value="' . $user['username'] . '" >' . $user['real_name'].'</option>';
            }
        }
        echo $html;
    }

    public function getDeptUserAction() {
        $dept = (int) RequestUtil::getGET('dept_id', 0);
        $userInfo = array();
        if ($dept) {
            $userInfo = MbsUserInterface::getUsersByDept(array('dept_id' => $dept));
            $html = '<option value="0" selected>业务员</option>';
            foreach ($userInfo as $user) {
                $html .= '<option value="' . $user['username'] . '" >' . $user['real_name'].'</option>';
            }
        }
        echo $html;
    }
}