<?php
require_once dirname(__FILE__) . '/include/ShopPage.class.php';
require_once dirname(__FILE__) . '/include/ShopManageForm.class.php';
require_once API_PATH . '/interface/mbs/MbsDeptInterface2.class.php';
require_once API_PATH . '/interface/MbsDeptInterface.class.php';
require_once API_PATH . '/interface/LocationInterface.class.php';

/**
 * @brief 店铺管理
 * @author miaoshiqian
 * @since 2013-8-20
 */

class ShopManagePage extends ShopPage {
    public function defaultAction() {
        $formData = array(
            'shop_hope' => $this->deptInfo['shop_hope'],
            'shop_slogan' => $this->deptInfo['shop_slogan'],
        );
        $form = new ShopManageForm($formData);
        $formObj = $form->getForm();
        $deptId = $this->deptInfo['id'];
        
        $this->render(array(
            'form' => $formObj,
            'deptId' => $deptId,
        ), 'shop_manage.php');
    }
    
    public function shopMapAction() {
        $deptInfo = $this->deptInfo;
        $loc = '';
        if (!empty($deptInfo['city'])) {
            $loc = LocationInterface::getCityNameById($deptInfo['city']);
        } else if (!empty($deptInfo['province'])) {
            $loc = LocationInterface::getProNameById($deptInfo['province']);
        }
        $this->render(array(
            'dept' => $deptInfo,
            'loc'  => $loc,
        ), 'shop_map.php');
    }
    
    //门店坐标修改
    public function ajaxSavePointAction() {
        $result = array('status' => 0);
        $shopPoint = RequestUtil::getGet('shop_point');
        $deptId = RequestUtil::getGet('dept_id');
        if (empty($deptId)) {
            $result['message'] = '门店id不能为空';
        }
        if (!empty($shopPoint)) {
            $params = array(
                'id' => $deptId,
                'shop_point' => $shopPoint,
            );
            try {
                $ret = MbsDeptInterface2::updateShopPoint($params);
                if ($ret) {
                    $result['status'] = 1;
                    $result['message'] = '保存成功';
                } else {
                    $result['message'] = '保存失败';
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
            }
        }
        echo json_encode($result);
        exit;
    }
    
    //门店口号，寄语等的提交处理
    public function submitAction() {
        $form = new ShopManageForm();
        $formObj = $form->getForm();
        
        $validate = $formObj->validate();
        if ($validate) {
            $deptId = $this->deptInfo['id'];
            $info = $formObj->getValue();
            $params = array(
                'id' => $deptId,
                'shop_slogan' => $info['shop_slogan'],
                'shop_hope'   => $info['shop_hope'],
            );
            try {
                $ret = MbsDeptInterface2::upSloganAndHope($params);
                if ($ret) {
                    ResponseUtil::redirect('/mbs_shop/shopManage/');
                } else {
                    $this->_renderForm($formObj);
                }
            } catch (Exception $e) {
                $this->_renderForm($formObj);
            }
        } else {
            $this->_renderForm($formObj);
        }
    }
    
    private function _renderForm($formObj) {
        $deptId = $this->deptInfo['id'];
        $this->render(array(
            'form' => $formObj,
            'deptId' => $deptId,
        ), 'shop_manage.php');
    }
}
