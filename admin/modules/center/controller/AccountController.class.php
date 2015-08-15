<?php
/**
 * @brief 简介：服务者的账户
 * @author 缪石乾 <miaoshiqian@anxin365.com>
 * @date 2015年1月25日 下午3:58:50
 */

class AccountController extends BcController {
    public function init() {
        
    }

    /**
     * @brief 订单列表页面
     */
    public function defaultAction() {
        $data = array(
            'datagrid' => $this->getDataGrid(),
        );
        $content = $this->view->fetch('account/index.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/account/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '姓名', 'width:10%');
        $dataGrid->setCol('field2', '手机', 'width: 15%');
        $dataGrid->setCol('field3', '银行名称', 'width:17%');
        $dataGrid->setCol('field4', '银行支行名称', 'width:15%');
        $dataGrid->setCol('field5', '银行卡号', 'width:18%');
        $dataGrid->setCol('field6', '账户余额', 'width:10%');
        $dataGrid->setCol('field7', '操作', 'width:15%');
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
            $client = Rpc::client('order.workerAccount');
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            //构建参数
            $filters = array();
            $status  = Request::getGET('status', 0);
            $cusName = Request::getGET('customer_name', '');
            if (!empty($status)) {
                $filters[] = array('status', '=', $status);
            }
            if (!empty($cusName)) {
                $filters[] = array('customer_name', 'like', '%' . $cusName . '%');
            }
            $params = array(
                'limit'   => $this->pageSize,
                'offset'  => $offset,
                'filters' => $filters,
            );
            $info = $client->getList($params);
            $allCount = $client->getCount(array('filters' => $filters));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }

        $list = array();
        if (!empty($info) && is_array($info)) {
            $code = $this->userInfo['code'];
            //手机服务者id数组去取服务者的数据
            $workerIds = $workers = array();
            foreach ($info as $worker) {
                $workerIds[] = $worker['worker_id'];
            }
            //获取阿姨的基本信息
            $workerClient = Rpc::client('worker.worker');
            $p = array(
                'filters' => array(array('id', 'in', $workerIds)),
                'type' => GlobalConfig::WORKER_BABY,
            );
            $parentalInfo = $workerClient->getUserList($p); //获取育儿嫂信息
            $p['type'] = GlobalConfig::WORKER_MONTH;
            $yuesaoInfo = $workerClient->getUserList($p); //获取月嫂信息
            if (!empty($parentalInfo) && is_array($parentalInfo)) {
                foreach ($parentalInfo as $p) {
                    if (isset($p['FullName'])) {
                        $workers[$p['id']] = array(
                            'name' => Util::getFromArray('FullName', $p, ''),
                            'mobile' => Util::getFromArray('Mobile', $p, ''),
                        );
                    }
                }
            }
            if (!empty($yuesaoInfo) && is_array($yuesaoInfo)) {
                foreach ($yuesaoInfo as $p) {
                    if (isset($p['FullName'])) {
                        $workers[$p['id']] = array(
                            'name' => Util::getFromArray('FullName', $p, ''),
                            'mobile' => Util::getFromArray('Mobile', $p, ''),
                        );
                    }
                }
            }
            //格式化表格数据
            foreach ($info as $worker) {
                $row['field1'] = isset($workers[$worker['worker_id']]) ? $workers[$worker['worker_id']]['name'] : '';
                $row['field2'] = isset($workers[$worker['worker_id']]) ? $workers[$worker['worker_id']]['mobile'] : '';
                $row['field3'] = !empty(GlobalConfig::$BANK_CONF[$worker['bank_name']]) ? GlobalConfig::$BANK_CONF[$worker['bank_name']] : '';
                $row['field4'] = Util::getFromArray('bank_branch', $worker, '');
                $row['field5'] = Util::getFromArray('bank_card', $worker, '');
                $row['field6'] = !empty($worker['balance']) ? sprintf("%.2f", $worker['balance'] / 100) : 0;
                $row['field7'] = '<a href="javascript:;" data-id="'.$worker['id'].'" data-action-type="edit">编辑</a> ';
                if ($worker['balance'] > 0) {
                    $row['field7'] .= '| <a href="javascript:;"  data-id="'.$worker['id'].'" data-action-type="check_account">结算</a>';
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
            echo $this->view->fetch('widget/iframe_error.php', array('msg' => '缺少账户id'));
            exit;
        }
        try {
            $client = Rpc::client('order.workerAccount');
            $info = $client->getById(array('id' => $id));
            $accountInfo = array(
                'bank_name'   => Util::getFromArray('bank_name', $info, 0),
                'bank_branch' => Util::getFromArray('bank_branch', $info, ''),
                'bank_card'   => Util::getFromArray('bank_card', $info, ''),
                'id'          => Util::getFromArray('id', $info, 0),
            );
            $data = array(
                'info' => $accountInfo,
            );
            $content = $this->view->fetch('account/iframe_edit.php', $data);
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
        $bankName = Request::getPOST('bank_name', 0);
        $bankCard = Request::getPOST('bank_card', '');
        $bankBranch = Request::getPOST('bank_branch', '');
        
        if (empty($id) || empty($bankName) || empty($bankCard)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数不对、无法提交')));
        }
        
        try {
            $client = Rpc::client('order.workerAccount');
            $params = array(
                'info' => array(
                    'bank_name'   => $bankName,
                    'bank_branch' => $bankBranch,
                    'bank_card'   => $bankCard,
                ),
                'id' => $id,
            );
            $client->edit($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '修改成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief 弹层结算服务者的账户
     */
    public function checkAccountAction() {
        $id = Request::getGET('id', 0);
        if (empty($id)) {
            echo $this->view->fetch('widget/iframe_error.php', array('msg' => '缺少账户id'));
            exit;
        }
        try {
            $client = Rpc::client('order.workerAccount');
            $info = $client->getById(array('id' => $id));
            $accountInfo = array(
                'balance'     => !empty($info['balance']) ? sprintf("%.2f", $info['balance'] / 100) : 0,
                'bank_name'   => Util::getFromArray('bank_name', $info, 0),
                'bank_branch' => Util::getFromArray('bank_branch', $info, ''),
                'bank_card'   => Util::getFromArray('bank_card', $info, ''),
                'id'          => Util::getFromArray('id', $info, 0),
            );
            $data = array(
                'info' => $accountInfo,
            );
            $content = $this->view->fetch('account/iframe_check_account.php', $data);
            echo $content;
        } catch (Exception $e) {
            echo $this->view->fetch('widget/iframe_error.php', array('msg' => $e->getMessage()));
            exit;
        }
    }
    
    /**
     * @brief 提交结算处理
     */
    public function ajaxCheckAccountAction() {
        $id          = Request::getPOST('id', 0);
        $bankName    = Request::getPOST('bank_name', 0);
        $bankCard    = Request::getPOST('bank_card', '');
        $bankBranch  = Request::getPOST('bank_branch', '');
        $outCard     = Request::getPOST('out_card', '');
        $checkAmount = Request::getPOST('check_amount', 0);
        
        if (empty($bankName) || empty($bankCard)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '账户信息还不完善无法结算')));
        }
        if (empty($id) || empty($outCard) || empty($checkAmount)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数不对、无法提交')));
        }
        
        try {
            $client = Rpc::client('order.workerAccount');
            $params = array(
                'id'          => $id,
                'amount'      => $checkAmount,
                'out_card'    => $outCard,
                'bank_name'   => $bankName,
                'bank_branch' => $bankBranch,
                'bank_card'   => $bankCard,
                'user_id'     => $this->userInfo['user']['id'],
            );
            $client->checkAccount($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '结算成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
}