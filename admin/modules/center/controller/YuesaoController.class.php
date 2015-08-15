<?php

/**
 * @brief 月嫂管理类
 * @author <缪石乾> miaoshiqian@anxin365.com
 * @since  2015-02-28
 */

class YuesaoController extends BcController {
    public function init() {
    }
    
    /**
     * @brief 月嫂列表(不包含老系统里面的，只是本次签约的)
     */
    public function defaultAction() {
        try {
            $code   = $this->userInfo['code'];
            $client = Rpc::client('worker.worker');
            $params = array(
                'filters' => array(
                    array('status', '=', BcEnumerateConfig::WORKER_STATUS_SIGN),
                    array('type', '=', GlobalConfig::WORKER_MONTH),
                ),
                'type' => GlobalConfig::WORKER_MONTH,
            );
            if (in_array('bc.manager', $code)) {
                $params['filters'][] = array('manager_id', '=', $this->userInfo['user']['old_id']);
            }
            $list = $client->getUserList($params);
            $parentalInfo = $manager = array();
            if (!empty($list) && is_array($list)) {
                $managerIds = array(); //经济人ids
                foreach ($list as $li) {
                    $faceParams = array(
                        'file' => Util::getFromArray('FaceUrl', $li, ''),
                        'option' => SelfConfig::$FACE_URL_OPT,
                    );
                    $parentalInfo[] = array(
                        'id'        => $li['id'],//暂时显示老表Id
                        'full_name' => Util::getFromArray('FullName', $li, ''),
                        //'face_url'  => ImgUtil::getUrl($faceParams),
                        'face_url'  => QiNiu::formatImageUrl(Util::getFromArray('face_url', $li, ''), array('width' => 80, 'height' => 80, 'mode' => 1)),
                        'age'       => !empty($li['BornTime']) ? (date("Y") - date("Y", strtotime($li['BornTime']))) : '',
                        'work_age'  => !empty($li['WorkTime']) ? (date("Y") - date("Y", strtotime($li['WorkTime']))) : '',
                        'address'   => Util::getFromArray('Address', $li, ''),
                        'id_check'  => $li['IdentityCertificate'] ? '身份已验证' : '身份未验证',
                        'reg_time'  => $li['CreatedTime'] ? date("Y-m-d", strtotime($li['CreatedTime'])) : '',
                        'manager'   => $li['ManagerId'] ? $li['ManagerId'] : 0,
                    );
                    
                    //记录经济人ids
                    $li['ManagerId'] && ($managerIds[] = $li['ManagerId']);
                }
                
                //获取经济人信息
                $mClient = Rpc::client('sso.manager');
                $managerInfo = $mClient->getById(array('manager_id' => $managerIds));
                if (!empty($managerInfo) && is_array($managerInfo)) {
                    foreach ($managerInfo as $m) {
                        $manager[$m['Id']] = $m['Fullname'];
                    }
                }
            }
            
            $data = array(
                'infoList' => $parentalInfo,
                'manager'  => $manager,
                'code'     => $code,
            );
            $content = $this->view->fetch('yuesao/list.php', $data);
            echo $content;
            
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }
    
    /**
     * @brief 指定id的月嫂的内容,去老表请求数据
     */
    public function iframeShowYuesaoAction() {
        try {
            $code = $this->userInfo['code'];
            $info = array();
            $id = (int) Request::getGET('id', 0);
            if (!empty($id) && is_int($id)) {
                $client = Rpc::client('worker.worker');
                $p = array('foreign_id' => $id, 'type' => GlobalConfig::WORKER_MONTH);
                if (in_array('bc.manager', $code)) {
                    $p['manager_id'] = $this->userInfo['user']['old_id'];
                }
                $info = $client->getUnsignInfo($p); //目前用得育儿嫂ID是老后台的id
                if (empty($info)) {
                    throw new Exception('您填写的月嫂ID不对，无法获取到该ID的月嫂信息!如果您是经纪人、请输入您名下的育儿嫂的ID');
                }
            } else {
                throw new Exception('您填写的月嫂ID不对，请仔细核对');
            }
            $faceParams = array(
                'file' => Util::getFromArray('FaceUrl', $info, ''),
                'option' => SelfConfig::$FACE_URL_OPT,
            );
            $data = array(
                'id'   => $id,
                'info' => array(
                    'face_url'  => ImgUtil::getUrl($faceParams),
                    'full_name' => Util::getFromArray('FullName', $info, ''),
                    'age'       => !empty($info['BornTime']) ? (date("Y") - date("Y", strtotime($info['BornTime']))) : '',
                    'address'   => Util::getFromArray('Address', $info, ''),
                    'id_check'  => isset($info['IdentityCertificate']) && $info['IdentityCertificate'] ? '身份已验证' : '身份未验证',
                    'reg_time'  => isset($info['CreatedTime']) && $info['CreatedTime'] ? date("Y-m-d", strtotime($info['CreatedTime'])) : '',
                ),
            );
            $content = $this->view->fetch('yuesao/iframe_show_yuesao.php', $data);
        } catch (Exception $e) {
            $data = array('errorCode' => 1, 'msg' => $e->getMessage());
            $content = $this->view->fetch('widget/iframe_error.php', $data);
        }
        echo $content;
    }
    
    /**
     * @brief 签约育儿嫂
     */
    public function signYuesaoAction() {
        $id = (int) Request::getPOST('id');
        if (!empty($id) && is_int($id)) {
            try {
                $client = Rpc::client('worker.worker');
                $params = array(
                    'id' => $id,
                    'create_user_id' => $this->userInfo['user']['id'],
                    'type' => GlobalConfig::WORKER_MONTH,
                );
                $client->sign($params);
                echo json_encode(array('errorCode' => 0, 'msg' => '签约月嫂成功！'));
            } catch (Exception $e) {
                echo json_encode(array('errorCode' => 1, 'msg' => $e->getMessage()));
            }
        } else {
            echo json_encode(array('errorCode' => 1, 'msg' => '月嫂的ID不正确，请仔细核对！'));
        }
    }
    
    /**
     * @brief ajax解约某个育儿嫂
     */
    public function ajaxUnsignYuesaoAction() {
        $id = (int) Request::getPOST('id');
        if (!empty($id) && is_int($id)) {
            try {
                $client = Rpc::client('worker.worker');
                $client->unSign(array('id' => $id));
                echo json_encode(array('errorCode' => 0, 'msg' => '解约月嫂成功！'));
            } catch (Exception $e) {
                echo json_encode(array('errorCode' => 1, 'msg' => $e->getMessage()));
            }
        } else {
            echo json_encode(array('errorCode' => 1, 'msg' => '月嫂的ID不正确，请仔细核对！'));
        }
    }
}
