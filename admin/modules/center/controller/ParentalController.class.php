<?php

/**
 * Class IndexController
 * @author miaoshiqian@iyuesao.com
 * @since  2015-01-11
 */

class ParentalController extends BcController {
    /**
     * @brief 育儿嫂列表
     */
    public function defaultAction() {
        $data = array(
            'datagrid' => $this->getDataGrid(),
            'code' => $this->userInfo['code'],
            'statusMap' => WorkerConfig::$WORKER_STATUS,
        );
        $content = $this->view->fetch('parental/list.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/parental/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '头像', 'width:15%');
        $dataGrid->setCol('field2', '姓名(ID)/手机', 'width: 15%');
        $dataGrid->setCol('field3', '年龄', 'width:10%');
        $dataGrid->setCol('field4', '家乡', 'width:15%');
        $dataGrid->setCol('field5', '从业经验', 'width:10%');
        $dataGrid->setCol('field6', '经纪人', 'width:15%');
        $dataGrid->setCol('field7', '操作', 'width:20%');

        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }

    /**
     * @brief 过滤数据处理
     */
    public function fieldFormat($name, $row) {
        return empty($row[$name]) && $row[$name] != 0 ? '' : $row[$name];
    }

    public function ajaxGetDataAction() {
        $dataGrid = $this->getDataGrid();
        $this->currentPage = (int) Request::getGET('page', 1);
        $allCount = 0;
        try {
            $client = Rpc::client('worker.worker');
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            //构建参数
            $filters = array(
                array('type', '=', GlobalConfig::WORKER_BABY),
            );
            $status = Request::getGET('status', BcEnumerateConfig::WORKER_STATUS_SIGN);
            $mobile = urldecode(Request::getGET('mobile', ''));
            $wkName  = urldecode(Request::getGET('real_name', ''));
            if (isset($status)) {
                $filters[] = array('status', '=', $status);
            }
            if (!empty($mobile)) {
                $filters[] = array('telephone', '=', $mobile);
            }
            if (!empty($wkName)) {
                $filters[] = array('real_name', '=', $wkName);
            }
            $params = array(
                'limit'   => $this->pageSize,
                'offset'  => $offset,
                'filters' => $filters,
            );
            $info = $client->getUserList($params);
            $allCount = $client->getCount(array('filters' => $filters));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }

        $list = array();
        if (!empty($info) && is_array($info)) {
            $code = $this->userInfo['code'];
            $managerIds = $manager = array();
            foreach ($info as $i) {
                $managerIds[] = $i['manager_id'];
            }
            //获取经济人信息
            $mClient = Rpc::client('sso.user');
            $managerInfo = $mClient->getById(array('id' => $managerIds));
            if (!empty($managerInfo) && is_array($managerInfo)) {
                foreach ($managerInfo as $m) {
                    $manager[$m['id']] = $m['real_name'];
                }
            }
            foreach ($info as $worker) {
                $photo = QiNiu::formatImageUrl(Util::getFromArray('face_url', $worker, ''), array('width' => 80, 'height' => 80, 'mode' => 1));
                $row['field1'] = '<img class="img-circle" width="80" src="'.$photo.'"/>';
                $row['field2'] = Util::getFromArray('real_name', $worker, '') . ' (ID:<font style="color:red">' . $worker['id'] . '</font>)';
                $row['field2'] .= '<br/>' . '手机：' . $worker['telephone'];
                $row['field3'] = !empty($worker['birthday']) ? (date("Y") - date("Y", $worker['birthday'])) . ' 岁' : '';
                $row['field4'] = '家乡开发中';
                $row['field5'] = !empty($worker['service_time']) ? (date("Y") - date("Y", $worker['service_time'])) . ' 年' : '';
                $row['field6'] = !empty($manager[$worker['manager_id']]) ? $manager[$worker['manager_id']] : '';
                $row['field7'] = '';
                $row['field7'] = '<a target="_blank" href="/center/parental/detail/?id=' . $worker['id'] . '">查看</a>';
                if (in_array('bc.*', $code) || $this->userInfo['user']['id'] == $worker['manager_id']) {
                    $row['field7'] .= ' | <a href="javascript:;" data-name="' . $worker['real_name'] . '" data-action-type="unsign" data-id=" ' . $worker['id'] . ' ">解约</a>';
                    $row['field7'] .= ' | <a href="/center/worker/default/?id='.$worker['id'].'">编辑信息</a>';
                }
                $list[] = $row;
            }
        }
        $dataGrid->setData($list);
        //设置分页信息
        $maxSize = $allCount > $this->maxSize ? $this->maxSize : $allCount;
        $dataGrid->setPager($maxSize, $this->currentPage, $this->pageSize);
        $data = $dataGrid->getData();
        echo json_encode(array('data' => $data));
    }
    
    /**
     * @brief 指定id的育儿嫂的内容,去老表请求数据
     */
    public function iframeShowParentalAction() {
        try {
            $code = $this->userInfo['code'];
            $info = array();
            $id = (int) Request::getGET('id', 0);
            if (!empty($id) && is_int($id)) {
                $client = Rpc::client('worker.worker');
                $p = array('foreign_id' => $id);
                if (in_array('bc.manager', $code)) {
                    $p['manager_id'] = $this->userInfo['user']['old_id'];
                }
                $info = $client->getUnsignInfo($p); //目前用得育儿嫂ID是老后台的id
                if (empty($info)) {
                    throw new Exception('您填写的育儿嫂ID不对，无法获取到该ID的育儿嫂信息!如果您是经纪人、请输入您名下的育儿嫂的ID');
                }
            } else {
                throw new Exception('您填写的育儿嫂ID不对，请仔细核对');
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
            $content = $this->view->fetch('parental/iframe_show_parental.php', $data);
        } catch (Exception $e) {
            $data = array('errorCode' => 1, 'msg' => $e->getMessage());
            $content = $this->view->fetch('widget/iframe_error.php', $data);
        }
        echo $content;
    }
    
    /**
     * @brief 签约育儿嫂
     */
    public function signParentalAction() {
        $id = (int) Request::getPOST('id');
        if (!empty($id) && is_int($id)) {
            try {
                $client = Rpc::client('worker.worker');
                $params = array(
                    'id' => $id,
                    'create_user_id' => $this->userInfo['user']['id'],
                    'type' => GlobalConfig::WORKER_BABY,
                );
                $client->sign($params);
                echo json_encode(array('errorCode' => 0, 'msg' => '签约育儿嫂成功！'));
            } catch (Exception $e) {
                echo json_encode(array('errorCode' => 1, 'msg' => $e->getMessage()));
            }
        } else {
            echo json_encode(array('errorCode' => 1, 'msg' => '育儿嫂的ID不正确，请仔细核对！'));
        }
    }
    
    /**
     * @brief ajax解约某个育儿嫂
     */
    public function ajaxUnsignParentalAction() {
        $id = (int) Request::getPOST('id');
        if (!empty($id) && is_int($id)) {
            try {
                $client = Rpc::client('worker.worker');
                $client->unSign(array('id' => $id));
                echo json_encode(array('errorCode' => 0, 'msg' => '解约育儿嫂成功！'));
            } catch (Exception $e) {
                echo json_encode(array('errorCode' => 1, 'msg' => $e->getMessage()));
            }
        } else {
            echo json_encode(array('errorCode' => 1, 'msg' => '育儿嫂的ID不正确，请仔细核对！'));
        }
    }

    /**
     * 临时编辑服务者的头像和简介
     */
    public function iframeEditAction() {
        $id = (int) Request::getGET('id');
        if (empty($id)) {
            $data = array('errorCode' => 1, 'msg' => '缺少服务者id');
            $content = $this->view->fetch('widget/iframe_error.php', $data);
            echo $content;exit;
        }
        $workerInfo = array();
        $client = Rpc::client('worker.worker');
        $workerInfo = $client->getInfo(array('id' => $id));

        $data = array(
            'workerId' => $id,
            'workerInfo' => $workerInfo,
        );
        $content = $this->view->fetch('parental/iframe_edit_face.php', $data);
        echo $content;
    }

    /**
     * @brief 提交服务者头像和简介
     */
    public function ajaxSubEditAction() {
        $id = (int) Request::getPOST('id');
        $desc = Request::getPOST('desc', '');
        $face = Request::getPOST('face_url', '');
        $face = json_decode($face, true);
        $face = $face['0']['key'];
        if (empty($id) || empty($desc) || empty($face)) {
            echo json_encode(array('errorCode' => 1, 'msg' => '头像和简介都必须填写！'));
            exit;
        }
        try {
            $client = Rpc::client('worker.worker');
            $params = array(
                'face_url' => $face,
                'description' => $desc,
                'worker_id' => $id,
            );
            $client->editFace($params);
            echo json_encode(array('errorCode' => 0, 'msg' => '修改成功啦！'));
        } catch (Exception $e) {
            echo json_encode(array('errorCode' => 1, 'msg' => $e->getMessage()));
        }
    }
}
