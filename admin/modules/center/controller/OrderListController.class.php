<?php
/**
 * @brief 简介：订单列表
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月25日 下午3:58:50
 */

class OrderListController extends BcController {
    public function init() {
        
    }
    /**
     * @brief 订单列表页面
     */
    public function defaultAction() {
        $data = array(
            'datagrid' => $this->getDataGrid(),
            'statusMap' => BcEnumerateConfig::$ORDER_PARENTAL_STATUS,
        );
        $content = $this->view->fetch('order_list/index.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/orderList/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '订单编号', 'width:10%');
        $dataGrid->setCol('field2', '订单联系人', 'width: 10%');
        $dataGrid->setCol('field3', '育儿嫂姓名', 'width:10%');
        $dataGrid->setCol('field4', '订单状态', 'width:15%');
        $dataGrid->setCol('field5', '起止时间', 'width:25%');
        $dataGrid->setCol('field6', '操作', 'width:30%');

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
            $client  = Rpc::client('order.parentalOrder');
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            //构建参数
            $filters = array();
            $status  = Request::getGET('status', 0);
            $cusName = urldecode(Request::getGET('customer_name', ''));
            $wkName  = urldecode(Request::getGET('worker_name', ''));
            $ordNum  = Request::getGET('order_num', 0);
            if (!empty($status)) {
                $filters[] = array('status', '=', $status);
            }
            if (!empty($cusName)) {
                $filters[] = array('customer_name', 'like', '%' . $cusName . '%');
            }
            if (!empty($wkName)) {
                $filters[] = array('parental_name', 'like', '%' . $wkName . '%');
            }
            if (!empty($ordNum)) {
                $filters[] = array('id', '=', ($ordNum - 1000000));
            }
            //如果都没有任何筛选条件的情况下、默认展示过滤掉关闭的订单
            if (empty($filters)) {
                $filters[] = array('status', '>', 0);
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
            foreach ($info as $order) {
                $id = Util::getFromArray('id', $order, 0);
                $serviceTimeB = !empty($order['service_time_b']) ? '起：' . date("Y-m-d H", $order['service_time_b']) . '点' : '';
                $serviceTimeE = !empty($order['service_time_e']) ? '止：' . date("Y-m-d H", $order['service_time_e']) . '点' : '';
                $row['field1'] = !empty($order['id']) ? 1000000 + $order['id'] : '-';
                $row['field2'] = Util::getFromArray('customer_name', $order, '');
                $row['field3'] = Util::getFromArray('parental_name', $order, '-');
                $row['field4'] = isset(BcEnumerateConfig::$ORDER_PARENTAL_STATUS[$order['status']]) ?
                    BcEnumerateConfig::$ORDER_PARENTAL_STATUS[$order['status']] : '-';
                $row['field5'] = $serviceTimeB . '<br/>' . $serviceTimeE;
                $row['field6'] = '<a target="_blank" href="/center/orderList/detail/?id=' . $id . '">查看</a>';
                if (in_array('bc.*', $code) || in_array('bc.service', $code)) {
                    $row['field6'] .= ' | <a target="_blank" href="/center/order/default/?id=' . $id . '">修改订单</a>';
                }
                if (in_array('bc.*', $code) || in_array('bc.service', $code) || in_array('bc.manager', $code)) {
                    if ($order['status'] == BcEnumerateConfig::PARENTAL_ORDER_STATUS_SERVE) {
                        $row['field6'] .= ' | <a target="_blank" href="/center/orderCommon/log/?order_id=' . $id
                            . '&order_type='.GlobalConfig::ORDER_TYPE_BABY.'">日志追踪</a>';
                    }
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
     * @brief 订单详情查看页面
     */
    public function detailAction() {
        try {
            $orderId = Request::getGET('id', 0);
            if (empty($orderId) || !is_numeric($orderId)) {
                $this->errorAction(array('缺少订单ID、无法获取订单详情！'));
            }
            
            $client = Rpc::client('order.parentalOrder');
            $info = $client->getById(array('id' => $orderId));
            
            $orderInfo = array();
            if (!empty($info) && is_array($info)) {
                //育儿嫂的薪资范围
                $salary = '';
                $salaryInfo = !empty($info['salary']) ? explode(',', $info['salary']) : array();
                if (!empty($salaryInfo) && is_array($salaryInfo)) {
                    foreach ($salaryInfo as $s) {
                        $salary .= isset(BcEnumerateConfig::$SALARY_PARENTAL[$s]) ? '、' . BcEnumerateConfig::$SALARY_PARENTAL[$s] : '';
                        $salary = trim($salary, '、');
                    }
                }
                //育儿嫂的工龄
                $workAge = '';
                $workAgeInfo = !empty($info['work_age']) ? explode(',', $info['work_age']) : array();
                if (!empty($workAgeInfo) && is_array($workAgeInfo)) {
                    foreach ($workAgeInfo as $w) {
                        $workAge .= isset(BcEnumerateConfig::$WORK_AGE_PARENTAL[$w]) ? '、' . BcEnumerateConfig::$WORK_AGE_PARENTAL[$w] : '';
                        $workAge = trim($workAge, '、');
                    }
                }
                
                $orderInfo = array(
                    'id'              => Util::getFromArray('id', $info, 0),
                    'status'          => Util::getFromArray('status', $info, 0),
                    'worker_id'       => Util::getFromArray('worker_id', $info, 0),
                    'quote'           => Util::getFromArray('quote', $info, 0),
                    'mobile'          => Util::getFromArray('mobile', $info, ''),
                    'status_text'     => isset(BcEnumerateConfig::$ORDER_PARENTAL_STATUS[$info['status']]) 
                                         ? BcEnumerateConfig::$ORDER_PARENTAL_STATUS[$info['status']] : '',
                    'order_type'      => GlobalConfig::ORDER_TYPE_BABY,
                    'order_num'       => OrderHelper::makeOrderNum($info['id']),
                    'customer_name'   => Util::getFromArray('customer_name', $info, ''),
                    'service_time_b'  => !empty($info['service_time_b']) ? date("Y-m-d H:i:s", $info['service_time_b']) : '',
                    'service_time_e'  => !empty($info['service_time_e']) ? date("Y-m-d H:i:s", $info['service_time_e']) : '',
                    'birthday'        => !empty($info['birthday']) ? date("Y-m-d H:i:s", $info['birthday']) : '',
                    'baby_type'       => isset(BcEnumerateConfig::$BABY_TYPE[$info['baby_type']]) ? 
                                         BcEnumerateConfig::$BABY_TYPE[$info['baby_type']] : '',
                    'customer_nation' => isset(BcEnumerateConfig::$CUSTOMER_NATION[$info['customer_nation']]) ? 
                                         BcEnumerateConfig::$CUSTOMER_NATION[$info['customer_nation']] : '',
                    'customer_native' => Util::getFromArray('customer_native', $info, ''),
                    'require'         => Util::getFromArray('require', $info, ''),
                    'service_type'    => OrderHelper::getParentalType($info['birthday']),
                    'hold_address'    => Util::getFromArray('hold_address', $info, ''),
                    'salary'          => $salary,
                    'work_age'        => $workAge,
                    'tip'             => !empty($info['tip']) ? sprintf('%.2f', ($info['tip'] / 100)) : 0,
                );
                
                //获取点赞、吐槽数据
                $praiseClient = Rpc::client('customer.praise');
                $praiseParams = array(
                    'order_id'   => $orderInfo['id'],
                    'order_type' => GlobalConfig::ORDER_TYPE_BABY,
                );
                list($praiseCn, $complaintCn) = $praiseClient->getOrderCount($praiseParams);
                
                $code = $this->userInfo['code'];
                //操作数组
                $ops = isset(OrderConfig::$ORDER_OP[$info['status']]) ? OrderConfig::$ORDER_OP[$info['status']] : array();
                $opUrl = '';
                if (!empty($ops) && is_array($ops)) {
                    foreach ($ops as $k => $op) {
                        switch ($op) {
                            case 1: //匹配育儿嫂
                                if (!empty($info['worker_id'])) { //已经面试成功之后就不能再匹配了
                                    break;
                                }
                                if (in_array('bc.*', $code) || in_array('bc.service', $code) || in_array('bc.manager', $code)) {
                                    $opUrl .= '<a target="_blank" href="/center/order/match/?order_id=' . $orderId . '" class="btn btn-success">匹配育儿嫂</a>';
                                }
                                break;
                            case 2: //预约面试
                                if (!empty($info['worker_id'])) { //已经面试成功之后就不能再预约了
                                    break;
                                }
                                if (in_array('bc.*', $code) || in_array('bc.service', $code)) {
                                    $opUrl .= '<a target="_blank" href="/center/order/audition/?order_id=' . $orderId . '" class="btn btn-success">预约面试</a>';
                                }
                                break;
                            case 3: //结算订单
                                $orderSubClient = Rpc::client('order.orderSub');
                                $subInfo = $orderSubClient->getCheckSub(array('order_id' => $orderId, 'order_type' => $orderInfo['order_type']));
                                if (count($subInfo) > 0 && (in_array('bc.*', $code) || in_array('bc.service', $code))) {
                                    $opUrl .= '<a target="_blank" href="/center/order/over/?order_id=' . $orderId . '" class="btn btn-success">结算订单</a>';
                                }
                                break;
                            case 4: //签合同&余款
                                if ((in_array('bc.*', $code) || in_array('bc.service', $code)) && !empty($info['worker_id'])) {
                                    $opUrl .= '<a target="_blank" href="/center/order/contract/?order_id=' . $orderId . '" class="btn btn-success">签合同 & 余款</a>';
                                }
                                break;
                            case 5: //续约订单
                                if (in_array('bc.*', $code) || in_array('bc.service', $code)) {
                                    $opUrl .= '<a target="_blank" data-action-type="continue_order" class="btn btn-danger">续约订单</a>';
                                }
                                break;
                            case 6: //终止订单
                                if (in_array('bc.*', $code) || in_array('bc.service', $code)) {
                                    $opUrl .= '<a target="_blank" data-id="'.$orderId.'" data-action-type="stop_order" class="btn btn-danger">终止订单</a>';
                                }
                                break;
                            case 7: //修改档期
                                if (in_array('bc.*', $code) || in_array('bc.service', $code)) {
                                    $opUrl .= '<a target="_blank" data-id="'.$orderId.'" data-action-type="chg_schedule" class="btn btn-success">修改订单服务时间</a>';
                                }
                            default:
                                break;
                        }
                    }
                }
            }

            if ($orderInfo['status'] == OrderConfig::STATUS_SERVE || $orderInfo['status'] == OrderConfig::STATUS_OVER) {
                $this->view->assign('doServe', 1); //可以展示订单服务中做得一些内容
                //获取订单点评数据
                $commentClient = Rpc::client('comment.comment');
                $commentParams = array(
                    'filters'  => array(
                        'order_id'   => $orderId,
                        'order_type' => GlobalConfig::ORDER_TYPE_BABY,
                        'to_user_id' => $info['worker_id'],
                    ),
                );
                $commentInfo = $commentClient->getList($commentParams);
                $this->view->assign('comment', $commentInfo);
                $this->view->assign('comCat', SelfConfig::$PARENTAL_COMMENT_CAT);//育儿嫂点评评分类别
                //获取回访列表数据
                $visitClient = Rpc::client('order.visit');
                $visitParams = array(
                    'filters' => array(
                        array('order_id', '=', $orderId),
                        array('order_type', '=', GlobalConfig::ORDER_TYPE_BABY),
                    ),
                );
                $visitInfo = $visitClient->getList($visitParams);
                $this->view->assign('visit', $visitInfo);
                //获取处罚列表数据
                $punishClient = Rpc::client('order.punish');
                $punishParams = array(
                    'filters' => array(
                        array('order_id', '=', $orderId),
                        array('order_type', '=', GlobalConfig::ORDER_TYPE_BABY),
                    ),
                );
                $punishInfo = $punishClient->getList($punishParams);
                $this->view->assign('punish', $punishInfo);
            }
            $data = array(
                'info'      => $orderInfo,
                'opUrl'     => $opUrl,
                'code'      => $code,
                'praise'    => !empty($praiseCn) ? $praiseCn : 0,
                'complaint' => !empty($complaintCn) ? $complaintCn : 0,
            );
            $content = $this->view->fetch('order_list/detail.php', $data);
            echo $content;
            
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }
    
    /**
     * @brief 添加详情页的订单回访内容
     */
    public function ajaxSubmitVisitAction() {
        $orderId = Request::getPOST('order_id', 0);
        $orderType = Request::getPOST('order_type', 0);
        $visitType = Request::getPOST('visit_type', 0);
        $visitCont = Request::getPOST('visit_cont', '');
        
        if (empty($orderId) || empty($orderType) || empty($visitType) || empty($visitCont)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数不对、无法添加回访')));
        }
        
        try {
            $client = Rpc::client('order.visit');
            $params = array(
                'user_id' => $this->userInfo['user']['id'],
                'info'    => array(
                    'order_id'   => $orderId,
                    'order_type' => $orderType,
                    'type'       => $visitType,
                    'brief'      => $visitCont,
                    'visiter_name' => $this->userInfo['user']['real_name'],
                ),
            );
            $client->add($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '添加回访成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }

    /**
     * @brief ajax提交育儿嫂订单的合同和定金信息
     */
    public function ajaxSubContractAction() {
        $orderId  = Request::getPOST('order_id', 0);
        $contract = Request::getPOST('contract', 0);
        $deposit  = Request::getPOST('deposit', 0);

        if (empty($contract) || empty($deposit)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数不对、无法提交!')));
        }
        
        try {
            $client = Rpc::client('order.contract');
            $params = array(
                'order_id'   => $orderId,
                'contract'   => $contract,
                'deposit'    => $deposit,
                'user_id'    => $this->userInfo['user']['id'],
                'order_type' => GlobalConfig::ORDER_TYPE_BABY,
            );
            $client->add($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '提交成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief ajax 设置子订单为客户已确认
     */
    public function ajaxSetConfirmAction() {
        $subId = Request::getPOST('sub_id', 0);
        if (empty($subId)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '子订单id缺失、无法确认')));
        }
        
        try {
            $client = Rpc::client('order.orderSub');
            $client->setConfirm(array('id' => $subId, 'user_info' => $this->userInfo['user']));
            exit(json_encode(array('errorCode' => 0, 'msg' => '确认成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief 查看考勤
     */
    public function iframeShowDailyAction() {
        $data = array();
        $content = $this->view->fetch('order/iframe_show_calendar.php', $data);
        echo $content;
    }

    /**
     * @brief 终止订单弹出层、填写订单终止时间
     */
    public function iframeStopAction() {
        $orderId = Request::getGET('id', 0);
        if (empty($orderId)) {
            $data = array('errorCode' => 1, 'msg' => '缺少订单id');
            $content = $this->view->fetch('widget/iframe_error.php', $data);
            exit($content);
        }

        $data = array('orderId' => $orderId);
        $content = $this->view->fetch('order_list/iframe_stop.php', $data);
        echo $content;
    }

    /**
     * @brief 终止育儿嫂订单提交操作
     */
    public function ajaxStopOrderAction() {
        $orderId  = Request::getPOST('order_id', 0);
        $stopDate = Request::getPOST('stop_date', 0);
        $stopHour = Request::getPOST('stop_hour', 0);

        if (empty($orderId) || empty($stopDate) || empty($stopHour)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数缺失、无法终止!')));
        }

        try {
            $client = Rpc::client('order.orderCommon');
            $stopParams = array(
                'order_type' => GlobalConfig::ORDER_TYPE_BABY,
                'order_id'   => $orderId,
                'stop_time'  => strtotime($stopDate) + (3600 * $stopHour),
            );
            $client->stopOrder($stopParams);
            exit(json_encode(array('errorCode' => 0, 'msg' => '终止成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }

    /**
     * @brief 修改育儿嫂档期
     */
    public function iframeScheduleAction() {
        $orderId = Request::getGET('id', 0);
        if (empty($orderId)) {
            $data = array('errorCode' => 1, 'msg' => '缺少订单id');
            $content = $this->view->fetch('widget/iframe_error.php', $data);
            exit($content);
        }

        $data = array('orderId' => $orderId);
        $content = $this->view->fetch('order_list/iframe_chg_schedule.php', $data);
        echo $content;
    }

    /**
     * @brief 修改育儿嫂档期提交操作
     */
    public function ajaxChgScheduleAction() {
        $orderId  = Request::getPOST('order_id', 0);
        $startDate = Request::getPOST('new_start_date', 0);
        $startHour = Request::getPOST('new_start_hour', 0);
        $endDate   = Request::getPOST('new_end_date', 0);
        $endHour   = Request::getPOST('new_end_hour', 0);

        if (empty($startDate) || empty($startHour) || empty($endDate) || empty($endHour) || empty($orderId)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数缺失、无法终止!')));
        }

        try {
            $client = Rpc::client('order.orderCommon');
            $stopParams = array(
                'order_type' => GlobalConfig::ORDER_TYPE_BABY,
                'order_id'   => $orderId,
                'start_time' => strtotime($startDate) + (3600 * $startHour),
                'end_time'   => strtotime($endDate) + (3600 * $endHour),
            );
            $client->changeServiceTime($stopParams);
            exit(json_encode(array('errorCode' => 0, 'msg' => '修改档期成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
}