<?php
/**
 * @brief 简介：订单公共处理模块(不限订单类型的统一操作)
 * @author 缪石乾 <miaoshiqian@anxin365.com>
 * @date 2015年3月11日 下午3:58:50
 */

class OrderCommonController extends BcController {
    /**
     * @brief 添加订单第一步、检索客户手机号
     */
    public function addOrderOneAction() {
        $orderType = Request::getGET('type', 0);
        if (empty($orderType)) {
            $this->errorAction(array('缺少下单类型'));
        }

        $info = array();
        $wxOrderId = Request::getGET('wx_order_id', 0); //微信订单过来
        if (!empty($wxOrderId)) {
            //获取微信订单的信息
            try {
                $client = Rpc::client('order.weixinOrder');
                $wxInfo = $client->getById(array('id' => $wxOrderId));
                $info['mobile'] = Util::getFromArray('mobile', $wxInfo, '');
                $info['customer_name'] = Util::getFromArray('name', $wxInfo, '');
                $info['hold_address'] = Util::getFromArray('address', $wxInfo, '');
            } catch (Exception $e) {
                $this->errorAction(array($e->getMessage()));
            }
        }
        //构建表单系统
        $formObj = new Form();
        //客户手机
        $mobileField = array(
            'name'         => 'mobile',
            'focusMessage' => '请填写正确的联系方式',
            'postValue'    => !empty($info['mobile']) ? $info['mobile'] : 'null',
            'tipTarget'    => '#mobile_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '联系方式不能为空',
                ),
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::PHONE,
                    'errorMessage' => '格式不正确',
                ),
            ),
        );
        $formObj->addField($mobileField);
        $data = array(
            'form'      => $formObj,
            'orderType' => $orderType,
        );
        $content = $this->view->fetch('order/add_order_1.php', $data);
        echo $content;
    }

    /**
     * @brief 添加订单第二步
     */
    public function addOrderTwoAction() {
        $orderType = Util::getFromArray('order_type', $_POST, 0);
        $wxOrderId = Util::getFromArray('wx_order_id', $_POST, 0);
        $mobile    = Util::getFromArray('mobile', $_POST, 0);
        if (empty($orderType) || empty($mobile)) {
            $this->errorAction(array('参数缺失、无法继续进行'));
        }

        $info = $orderList = array();
        $editMobile = true; //默认允许修改
        try {
            //去获取该手机号的客户、不存在返回空数组
            $client = Rpc::client('customer.customer');
            $customerInfo = $client->getList(array('mobile' => $mobile));
            $info = Util::getFromArray('0', $customerInfo, array());
            if (!empty($info['mobile'])) {
                //已经存在该手机号的客户了、那么手机号就不允许修改、只能改姓名
                $editMobile = false;
                //获取该手机号目前相关的还没开始服务的订单列表、供客服参考是否重复下单
                $orderClient = $orderType == 1 ? Rpc::client('order.parentalOrder') : Rpc::client('order.yuesaoOrder');
                $orderParams = array(
                    'filters' => array(
                        array('mobile', '=', $mobile),
                        array('status', '>', 0),
                        array('status', '<', BcEnumerateConfig::PARENTAL_ORDER_STATUS_SERVE),
                    ),
                );
                $orderList = $orderClient->getList($orderParams);
            }

            if (!empty($wxOrderId) && empty($customerInfo)) {
                //该手机号的客户不存在、并且存在微信订单的id、那么默认值传微信订单的客户信息过去给客服参考
                $client = Rpc::client('order.weixinOrder');
                $wxInfo = $client->getById(array('id' => $wxOrderId));
                $info['mobile'] = Util::getFromArray('mobile', $wxInfo, '');
                $info['real_name'] = Util::getFromArray('name', $wxInfo, '');
            }
            $info['mobile'] = empty($info['mobile']) ? $mobile : $info['mobile'];
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }

        //构建表单系统
        $formObj = new Form();
        //客户姓名
        $customerNameField = array(
            'name'         => 'real_name',
            'focusMessage' => '只能输入2-6个字',
            'postValue'    => !empty($info['real_name']) ? $info['real_name'] : 'null',
            'tipTarget'    => '#real_name_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '客户姓名不能为空',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 2,
                    'max'  => 6,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );
        //客户手机
        $mobileField = array(
            'name'         => 'mobile',
            'focusMessage' => '请填写正确的联系方式',
            'postValue'    => !empty($info['mobile']) ? $info['mobile'] : 'null',
            'tipTarget'    => '#mobile_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '联系方式不能为空',
                ),
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::PHONE,
                    'errorMessage' => '格式不正确',
                ),
            ),
        );
        $formObj->addField($mobileField);
        $formObj->addField($customerNameField);

        $data = array(
            'form'       => $formObj,
            'orderType'  => $orderType,
            'wxOrderId'  => $wxOrderId,
            'info'       => $info,
            'editMobile' => $editMobile, //是否允许改手机号.
            'orderList'  => $orderList, //该手机号目前已经下得在等待处理的订单列表、展示出来供客服参考
            'statusText' => $orderType == 1 ? BcEnumerateConfig::$ORDER_PARENTAL_STATUS : BcEnumerateConfig::$ORDER_YUESAO_STATUS,
        );
        $content = $this->view->fetch('order/add_order_2.php', $data);
        echo $content;
    }

    /**
     * @biref 下单第三步
     */
    public function addOrderThreeAction() {
        $orderType = Util::getFromArray('order_type', $_POST, 0);
        $wxOrderId = Util::getFromArray('wx_order_id', $_POST, 0);
        $mobile    = Util::getFromArray('mobile', $_POST, '');
        $realName  = Util::getFromArray('real_name', $_POST, '');
        if (empty($orderType) || empty($mobile)) {
            $this->errorAction(array('参数缺失、无法继续进行'));
        }

        try {
            $customerClient = Rpc::client('customer.customer');
            $params = array(
                'real_name' => $realName,
                'mobile'    => $mobile,
            );
            $customerId = $customerClient->register($params);
            if (empty($customerId)) {
                throw new Exception('检验注册客户信息是、没有正确接收到rpc返回的客户id、无法继续');
            }
            $orderUrl = $orderType == 1 ? '/center/order/default/' : '/center/orderYuesao/default/';
            $orderUrl .= '?customer_id=' . $customerId;
            $orderUrl .= !empty($wxOrderId) ? '&wx_order_id=' . $wxOrderId : '';
            //跳转到最后一步发布页
            Response::redirect($orderUrl);
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }

    /**
     * @brief 提交服务者点评内容(月嫂 或 育儿嫂的)
     */
    public function ajaxSubCommentAction() {
        $orderId   = Request::getPOST('order_id', 0);
        $orderType = Request::getPOST('order_type', 0);
        $workerId  = Request::getPOST('worker_id', 0);
        $comment   = Request::getPOST('comment', '');
        $yuesao1   = Request::getPOST('yuesao_1', 0);
        $yuesao2   = Request::getPOST('yuesao_2', 0);
        $yuesao3   = Request::getPOST('yuesao_3', 0);
        $yuesao4   = Request::getPOST('yuesao_4', 0);
        $yuesao5   = Request::getPOST('yuesao_5', 0);
        $parental1 = Request::getPOST('parental_1', 0);
        $parental2 = Request::getPOST('parental_2', 0);
        $parental3 = Request::getPOST('parental_3', 0);
        $parental4 = Request::getPOST('parental_4', 0);
        $parental5 = Request::getPOST('parental_5', 0);

        if (empty($orderId) || empty($orderType) || empty($comment) || empty($workerId)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数缺失!')));
        }

        try {
            $params = array(
                'order_id'     => $orderId,
                'order_type'   => $orderType,
                'from_user_id' => $this->userInfo['user']['id'],
                'to_user_id'   => $workerId,
                'comment'      => $comment,
                'is_backend'   => 1,
            );
            if ($orderType == GlobalConfig::ORDER_TYPE_BABY) {
                if (empty($parental1) || empty($parental2) || empty($parental3) || empty($parental4) || empty($parental5)) {
                    throw new Exception('5项评分没有评完整、请务必全部评完');
                }
                $params['parental_1'] = $parental1;
                $params['parental_2'] = $parental2;
                $params['parental_3'] = $parental3;
                $params['parental_4'] = $parental4;
                $params['parental_5'] = $parental5;
            } else if ($orderType == GlobalConfig::ORDER_TYPE_MONTH) {
                if (empty($yuesao1) || empty($yuesao2) || empty($yuesao3) || empty($yuesao4) || empty($yuesao5)) {
                    throw new Exception('5项评分没有评完整、请务必全部评完');
                }
                $params['yuesao_1'] = $yuesao1;
                $params['yuesao_2'] = $yuesao2;
                $params['yuesao_3'] = $yuesao3;
                $params['yuesao_4'] = $yuesao4;
                $params['yuesao_5'] = $yuesao5;
            }
            $client = Rpc::client('comment.comment');
            $client->add($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '点评成功！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }

    /**
     * @brief 添加详情页的订单处罚内容
     */
    public function ajaxSubPunishAction() {
        $orderId     = Request::getPOST('order_id', 0);
        $orderType   = Request::getPOST('order_type', 0);
        $punishType  = Request::getPOST('punish_type', 0);
        $punishBrief = Request::getPOST('punish_brief', '');
        $workerId    = Request::getPOST('worker_id', 0);
        $quote       = Request::getPOST('quote', 0);

        if (empty($orderId) || empty($orderType) || empty($punishType) || empty($punishBrief) || empty($workerId) || empty($quote)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数不对、无法添加处罚')));
        }
        try {
            $client = Rpc::client('order.punish');
            if ($punishType == BcEnumerateConfig::PUNISH_NORMAL) {
                $punish = 100 * 100; //一般违纪罚款100元、10000分
            } else {
                //红黄牌、按照配置的处罚比例来处罚
                $percent = BcEnumerateConfig::$PUNISH_PERCENT[$punishType];
                //订单定价里面属于服务者的部分、按照配置的佣金比例来推算
                $incomePercent = 1 - OrderPriceConfig::PLAT_INCOME_PERCENT;
                if ($orderType == GlobalConfig::ORDER_TYPE_MONTH) {
                    //收入比例改为月嫂的
                    $incomePercent = 1 - OrderPriceConfig::YUESAO_PLAT_INCOME_PERCENT;
                }
                $percent *= $incomePercent;
                $punish = $percent * $quote;
            }
            $params = array(
                'user_id'    => $this->userInfo['user']['id'],
                'order_id'   => $orderId,
                'order_type' => $orderType,
                'worker_id'  => $workerId,
                'type'       => $punishType,
                'brief'      => $punishBrief,
                'real_name'  => $this->userInfo['user']['real_name'],
                'punish'     => $punish,
            );
            $client->add($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '添加处罚成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }

    /**
     * @brief 设置订单点评操作
     */
    public function ajaxSetCommentAction() {
        $id     = Request::getPOST('id', 0);
        $status = Request::getPOST('status', 0);

        if (empty($id) || empty($status)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数缺失、无法提交操作')));
        }
        try {
            $client = Rpc::client('comment.comment');
            $params = array(
                'user_id' => $this->userInfo['user']['id'],
                'id'      => $id,
                'status'  => $status,
            );
            $client->setStatus($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '设置成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }

    /**
     * @brief 服务者工作日志查看列表
     */
    public function logAction() {
        $data = array(
            'datagrid' => $this->getLogGrid(),
        );
        $content = $this->view->fetch('order/worker_log.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getLogGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/orderCommon/ajaxGetLog/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '工作内容', 'width:33%');
        $dataGrid->setCol('field2', '完成时间', 'width: 33%');
        $dataGrid->setCol('field3', '记录时间', 'width:34%');

        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }

    /**
     * @brief 过滤数据处理
     */
    public function fieldFormat($name, $row) {
        return empty($row[$name]) && $row[$name] != 0 ? '' : $row[$name];
    }

    public function ajaxGetLogAction() {
        $orderId = Request::getGET('order_id', 0);
        $orderType = Request::getGET('order_type', 0);
        if (empty($orderId) || empty($orderType)) {
            $this->errorAction(array('参数缺失!'));
        }
        $dataGrid = $this->getLogGrid();
        $this->currentPage = (int) Request::getGET('page', 1);
        $allCount = 0;
        try {
            $logClient = Rpc::client('daily.workLog');
            $taskClient = Rpc::client('daily.task');
            $filters = array(
                'order_id' => $orderId,
                'order_type' => $orderType,
            );
            $countFilters = array(
                array('order_id', '=', $orderId),
                array('order_type', '=', $orderType),
            );
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            $startTime = Request::getGET('start_time', 0);
            $endTime   = Request::getGET('end_time', 0);
            if (!empty($startTime)) {
                $startUnix = strtotime($startTime);
                $filters['start_create_time'] = $startUnix;
                $countFilters[] = array('create_time', '>=', $startUnix);
            }
            if (!empty($endTime)) {
                $endUnix = strtotime($endTime) + 86400;
                $filters['end_create_time'] = $endUnix;
                $countFilters[] = array('create_time', '<=', $endUnix);
            }
            $params = array(
                'limit'   => $this->pageSize,
                'offset'  => $offset,
                'filters' => $filters,
                'order'   => array('task_finish_time' => 'desc'),
            );
            $info = $logClient->getWorkLogList($params);
            $allCount = $logClient->getCount(array('filters' => $countFilters));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }

        $list = array();
        if (!empty($info) && is_array($info)) {
            $code = $this->userInfo['code'];
            foreach ($info as $log) {
                $taskInfo = $taskClient->get(array('task_id' => $log['task_id']));
                $row['field1'] = $taskInfo['name'];
                $row['field2'] = !empty($log['task_finish_time']) ? date('Y-m-d H', $log['task_finish_time']) . '点' : '';
                $row['field3'] = !empty($log['create_time']) ? date('Y-m-d H:i:s', $log['create_time']) : '';;
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
}