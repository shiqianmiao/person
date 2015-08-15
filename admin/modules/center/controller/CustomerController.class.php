<?php
/**
 * @brief 简介：客户信息管理模块
 * @author 缪石乾 <miaoshiqian@anxin365.com>
 * @date 2015年3月16日 下午3:58:50
 */

class CustomerController extends BcController {

    /**
     * @brief 订单列表页面
     */
    public function defaultAction() {
        $data = array(
            'datagrid' => $this->getDataGrid(),
        );
        $content = $this->view->fetch('customer/list.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/customer/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '姓名', 'width:20%');
        $dataGrid->setCol('field2', '手机', 'width: 20%');
        $dataGrid->setCol('field3', '创建时间', 'width:20%');
        $dataGrid->setCol('field4', '操作', 'width:40%');
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
            $client = Rpc::client('customer.customer');
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            //构建参数
            $params = array(
                'real_name' => urldecode(Request::getGET('real_name', '')),
                'mobile'    => Request::getGET('mobile', ''),
                'limit'     => $this->pageSize,
                'offset'    => $offset,
            );
            $info = $client->getList($params);
            $allCount = $client->getCount($params);
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }

        $list = array();
        if (!empty($info) && is_array($info)) {
            $code = $this->userInfo['code'];
            //格式化表格数据
            foreach ($info as $customer) {
                $row['field1'] = Util::getFromArray('real_name', $customer, '');
                $row['field2'] = Util::getFromArray('mobile', $customer, '');
                $row['field3'] = !empty($customer['create_time']) ? date('Y-m-d H:i', $customer['create_time']) : '';
                $row['field4'] = '';
                if (in_array('bc.*', $code) || in_array('bc.service', $code)) {
                    $row['field4'] .= '<a href="javascript:;" data-id="'.$customer['id'].'" data-action-type="edit">编辑</a> ';
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
     * @brief 弹层编辑服务者的账户信息
     */
    public function iframeEditAction() {
        $id = Request::getGET('id', 0);
        if (empty($id)) {
            echo $this->view->fetch('widget/iframe_error.php', array('msg' => '缺少客户id'));
            exit;
        }
        try {
            $client = Rpc::client('customer.customer');
            $info = $client->getListById(array('id' => $id));
            $info = Util::getFromArray('0', $info, array());
            $accountInfo = array(
                'real_name' => Util::getFromArray('real_name', $info, ''),
                'mobile'    => Util::getFromArray('mobile', $info, ''),
                'id'        => Util::getFromArray('id', $info, 0),
            );
            $data = array(
                'info' => $accountInfo,
            );
            $content = $this->view->fetch('customer/iframe_edit.php', $data);
            echo $content;
        } catch (Exception $e) {
            echo $this->view->fetch('widget/iframe_error.php', array('msg' => $e->getMessage()));
            exit;
        }
    }
    
    /**
     * @brief 编辑账户信息提交处理
     */
    public function subEditAction() {
        $id = Request::getPOST('id', 0);
        $realName = Request::getPOST('real_name', '');
        if (empty($id) || empty($realName)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数不对、无法提交')));
        }
        try {
            $client = Rpc::client('customer.customer');
            $params = array(
                'real_name' => $realName,
                'id' => $id,
            );
            $client->editName($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '修改成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
}