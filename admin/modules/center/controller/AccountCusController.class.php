<?php
/**
 * @brief 简介：客户的账户
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月25日 下午3:58:50
 */

class AccountCusController extends BcController {
    /**
     * @brief 订单列表页面
     */
    public function defaultAction() {
        $data = array(
            'datagrid' => $this->getDataGrid(),
        );
        $content = $this->view->fetch('customer_account/index.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/accountCus/ajaxGetData/?' . $this->getQueryString());
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
            $client = Rpc::client('order.customerAccount');
            $customerClient = Rpc::client('customer.customer');
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            //构建参数
            $filters = $cusFilters = array();
            $mobile = Request::getGET('mobile', '');
            $name   = urldecode(Request::getGET('real_name', ''));
            $list   = $customerIds = $cusInfo = array();

            if (!empty($name) || !empty($mobile)) {
                //客户筛选条件不空、那么先去筛选出客户id、然后去获取账户列表
                $cusParams = array(
                    'mobile'    => !empty($mobile) ? $mobile : '',
                    'real_name' => !empty($name) ? $name : '',
                    'limit'     => $this->pageSize,
                    'offset'    => $offset,
                );
                $customers = $customerClient->getList($cusParams);
                if (!empty($customers) && is_array($customers)) {
                    foreach ($customers as $cus) {
                        $customerIds[] = $cus['id'];
                    }
                }
                $info = $client->getList(array('filters' => array(array('customer_id', 'in', $customerIds))));
                $allCount = $customerClient->getCount($cusParams);
            } else {
                //没有筛选条件、按分页全部获取
                $params = array(
                    'limit'   => $this->pageSize,
                    'offset'  => $offset,
                    'filters' => array(),
                );
                $info = $client->getList($params);
                $allCount = $client->getCount(array('filters' => array()));
            }

            if (!empty($info) && is_array($info)) {
                $code = $this->userInfo['code'];
                if (empty($customers)) {
                    //获取客户ids、去批量获取客户基本信息
                    foreach ($info as $cus) {
                        $customerIds[] = $cus['customer_id'];
                    }
                    $customers = $customerClient->getListById(array('id' => $customerIds));
                }

                if (!empty($customers) && is_array($customers)) {
                    //格式化客户信息
                    foreach ($customers as $cus) {
                        $cusInfo[$cus['id']] = array(
                            'name' => $cus['real_name'],
                            'mobile' => $cus['mobile'],
                        );
                    }
                }

                //格式化表格数据
                foreach ($info as $account) {
                    $row['field1'] = isset($cusInfo[$account['customer_id']]) ? $cusInfo[$account['customer_id']]['name'] : '';
                    $row['field2'] = isset($cusInfo[$account['customer_id']]) ? $cusInfo[$account['customer_id']]['mobile'] : '';
                    $row['field3'] = !empty(GlobalConfig::$BANK_CONF[$account['bank_name']]) ? GlobalConfig::$BANK_CONF[$account['bank_name']] : '';
                    $row['field4'] = Util::getFromArray('bank_branch', $account, '');
                    $row['field5'] = Util::getFromArray('bank_card', $account, '');
                    $row['field6'] = !empty($account['balance']) ? sprintf("%.2f", $account['balance'] / 100) : 0;
                    $row['field7'] = '';
                    if (in_array('bc.*', $code) || in_array('bc.finance', $code)) {
                        $row['field7'] .= '<a href="javascript:;" data-name="'.$row['field1'].'" data-mobile="'.$row['field2'].'" data-id="'.$account['id'].'" data-action-type="recharge">充值</a>';
                        $row['field7'] .= ' | <a href="javascript:;" data-id="' . $account['id'] . '" data-action-type="edit">编辑</a>';
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
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
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
            $client = Rpc::client('order.customerAccount');
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
            $content = $this->view->fetch('customer_account/iframe_edit.php', $data);
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
            $client = Rpc::client('order.customerAccount');
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
            $client = Rpc::client('order.customerAccount');
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
            $content = $this->view->fetch('customer_account/iframe_check_account.php', $data);
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
            exit(json_encode(array('errorCode' => 1, 'msg' => '账户信息还不完善无法退款')));
        }
        if (empty($id) || empty($outCard) || empty($checkAmount)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数不对、无法提交')));
        }
        
        try {
            $client = Rpc::client('order.customerAccount');
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
    
    /**
     * @brief 客户充值页面
     */
    public function iframeRechargeAction() {
        $id = Request::getGET('id', 0);
        $name = Request::getGET('name', '');
        $mobile = Request::getGET('mobile', '');
        if (empty($id) || empty($mobile)) {
            echo $this->view->fetch('widget/iframe_error.php', array('msg' => '缺少账户id或手机号'));
            exit;
        }
        try {
            $data = array(
                'datagrid' => $this->getLogGrid($id),
                'accountId' => $id,
                'name' => $name,
                'mobile' => $mobile,
            );
            $content = $this->view->fetch('customer_account/iframe_recharge.php', $data);
            echo $content;
        } catch (Exception $e) {
            echo $this->view->fetch('widget/iframe_error.php', array('msg' => $e->getMessage()));
            exit;
        }
    }

    /**
     * @brief 设置表格头
     */
    public function getLogGrid($accountId = 0) {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/accountCus/ajaxGetLog/?account_id=' . $accountId);
        $dataGrid->setCol('field1', '充值金额', 'width:30%');
        $dataGrid->setCol('field2', '操作人', 'width: 30%');
        $dataGrid->setCol('field3', '充值时间', 'width:30%');
        $dataGrid->setFormatCallback(array($this, 'logFormat'));
        return $dataGrid;
    }

    /**
     * @brief 过滤数据处理
     */
    public function logFormat($name, $row) {
        return empty($row[$name]) && $row[$name] != 0 ? '' : $row[$name];
    }

    public function ajaxGetLogAction() {
        $dataGrid = $this->getLogGrid();
        $this->currentPage = (int) Request::getGET('page', 1);
        $allCount = 0;
        try {
            $client  = Rpc::client('order.customerRechargeLog');
            $limit = 3; //限制每页3条
            $offset  = ($this->currentPage - 1) * $limit;
            //构建参数
            $accountId = Request::getGET('account_id', 0);
            $filters = array(
                array('account_id', '=', $accountId),
            );
            $params = array(
                'limit'   => $limit,
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
            foreach ($info as $log) {
                $row['field1'] = !empty($log['amount']) ? ($log['amount'] / 100) . '元' : '0元';
                $row['field2'] = Util::getFromArray('create_user_name', $log, '');
                $row['field3'] = !empty($log['create_time']) ? date('Y-m-d H:i', $log['create_time']) : '';
                $list[] = $row;
            }
        }
        $dataGrid->setData($list);
        //设置分页信息
        $maxSize = $allCount > $this->maxSize ? $this->maxSize : $allCount;
        $dataGrid->setPager($maxSize, $this->currentPage, $limit);
        $data = $dataGrid->getData();
        echo json_encode(array('data' => $data));
    }
    
    /**
     * @brief 客户充值提交处理 
     */
    public function ajaxSubRechargeAction() {
        $id     = Request::getPOST('id', 0);
        $amount = Request::getPOST('amount', 0);
        $images = Request::getPOST('images', '');
        $images = !empty($images) ? json_decode($images, true) : array();

        if (empty($images) || !is_array($images)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '充值图片不能为空！')));
        }
        if (empty($id) || empty($amount)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数缺失、无法充值')));
        }

        try {
            $client = Rpc::client('order.customerAccount');
            $params = array(
                'id'      => $id,
                'amount'  => $amount,
                'user_id' => $this->userInfo['user']['id'],
            );
            $client->recharge($params);
            //保存充值日志记录
            $recordParams = array(
                'account_id' => $id,
                'images'     => $images,
                'amount'     => $amount,
                'user_id'    => $this->userInfo['user']['id'],
                'user_name'  => $this->userInfo['user']['real_name'],
            );
            $logClient = Rpc::client('order.customerRechargeLog');
            $logClient->add($recordParams);
            exit(json_encode(array('errorCode' => 0, 'msg' => '充值成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
}