<?php

/**
 * @brief 月嫂订单控制器
 * @author miaoshiqian@anxin365.com
 * @since  2015-02-28
 */
class OrderYuesaoController extends BcController {
    public function init() {
    }
    
    /**
     * @brief 添加订单页面、或编辑订单页面
     */
    public function defaultAction() {
        $id = Request::getGET('id', 0);
        $isEdit = !empty($id) ? 1 : 0;
        $info = array();
        try {
            if ($isEdit) {
                $client = Rpc::client('order.yuesaoOrder');
                $info = $client->getById(array('id' => $id));
            } else {
                $wxOrderId  = Request::getGET('wx_order_id', 0); //微信订单过来
                $customerId = Request::getGET('customer_id', 0); //前2步验证得来的客户id
                if (empty($customerId)) {
                    //添加订单的时候不能缺少客户id
                    $this->errorAction(array('添加订单的时候不能缺少客户id'));
                } else {
                    //获取客户信息,第一次添加带出客户手机和姓名、作为订单的联系人供客服参考
                    $client = Rpc::client('customer.customer');
                    $customerInfo = $client->getListById(array('id' => $customerId));
                    $customerInfo = Util::getFromArray('0', $customerInfo, array());
                    $info = array(
                        'customer_name' => $customerInfo['real_name'],
                        'mobile' => $customerInfo['mobile'],
                    );
                }
            }

            $formObj = $this->_setField($info);
            $data = array(
                'info'   => $info,
                'isEdit' => $isEdit,
                'form'   => $formObj,
                'wxOrderId' => isset($wxOrderId) ? $wxOrderId : 0,
                'customerId' => isset($customerId) ? $customerId : 0,
            );
            $content = $this->view->fetch('order/add_yuesao.php', $data);
            echo $content;
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }
    
    /**
     * @brief 匹配月嫂页面
     */
    public function matchAction() {
        $orderInfo = $this->_getOrderInfo();
        if (empty($orderInfo)) {
            $this->errorAction(array('获取订单数据失败！'));
        }
        
        //获取该订单已匹配的育儿嫂的数据
        list($parentalInfo, $managerInfo) = $this->_orderMatched($orderInfo['id']);
        
        $data = array(
            'order'    => $orderInfo,
            'parental' => $parentalInfo,
            'manager'  => $managerInfo,
            'map'      => OrderConfig::getOrderMap($orderInfo['status']),
        );
        $content = $this->view->fetch('order/match_yuesao.php', $data);
        echo $content;
    }
    
    /**
     * @brief 匹配月嫂弹层确认育儿嫂信息
     */
    public function iframeMatchAction() {
        try {
            $info = array();
            $id = (int) Request::getGET('id', 0);
            $orderId = (int) Request::getGET('order_id', 0);
            if (empty($id) || empty($orderId)) {
                throw new Exception('您填写的育儿嫂ID不对或者丢失订单ID，请仔细核对');
            }
            $client = Rpc::client('worker.worker');
            $info = $client->getInfo(array('id' => $id));
            if (empty($info)) {
                throw new Exception("ID为{$id}的月嫂可能还未签约、获取其信息失败！");
            }
            $faceParams = array(
                'file' => Util::getFromArray('FaceUrl', $info, ''),
                'option' => SelfConfig::$FACE_URL_OPT,
            );
            $data = array(
                'id'      => $id,
                'orderId' => $orderId,
                'info'    => array(
                    'face_url'  => ImgUtil::getUrl($faceParams),
                    'full_name' => Util::getFromArray('FullName', $info, ''),
                    'age'       => !empty($info['BornTime']) ? (date("Y") - date("Y", strtotime($info['BornTime']))) : '',
                    'address'   => Util::getFromArray('Address', $info, ''),
                    'id_check'  => isset($info['IdentityCertificate']) && $info['IdentityCertificate'] ? '身份已验证' : '身份未验证',
                    'reg_time'  => isset($info['CreatedTime']) && $info['CreatedTime'] ? date("Y-m-d", strtotime($info['CreatedTime'])) : '',
                    'manager'   => Util::getFromArray('ManagerId', $info, 0),
                ),
            );
            $content = $this->view->fetch('order/iframe_show_yuesao.php', $data);
        } catch (Exception $e) {
            $data = array('errorCode' => 1, 'msg' => $e->getMessage());
            $content = $this->view->fetch('widget/iframe_error.php', $data);
        }
        echo $content;
    }
    
    /**
     * @brief 提交匹配月嫂内容
     */
    public function subMatchAction() {
        $id = (int) Request::getPOST('id', 0);
        $orderId = (int) Request::getPOST('order_id', 0);
        $managerId = (int) Request::getPOST('manager_id', 0);
        $name = Request::getPOST('name', '');
        if (empty($id) || empty($orderId)) {
            echo json_encode(array('errorCode' => 1, 'msg' => '月嫂的ID或者订单ID不正确，请仔细核对！'));
        }
        
        try {
            $client = Rpc::client('order.yuesaoOrder');
            $info = array(
                'order_type' => GlobalConfig::ORDER_TYPE_MONTH,
                'order_id'   => $orderId,
                'manager_id' => $managerId,
                'worker_id'  => $id,
            );
            $params = array(
                'info'    => $info,
                'user_id' => $this->userInfo['user']['id'],
            );
            $client->match($params);
            echo json_encode(array('errorCode' => 0, 'msg' => '为该订单匹配育儿嫂成功！'));
        } catch (Exception $e) {
            echo json_encode(array('errorCode' => 1, 'msg' => $e->getMessage()));
        }
    }
    
    /**
     * @brief 预约面试页面
     */
    public function auditionAction() {
        $orderInfo = $this->_getOrderInfo();
        if (empty($orderInfo)) {
            $this->errorAction(array('获取订单数据失败！'));
        }
        
        //获取该订单已匹配的育儿嫂的数据
        list($parentalInfo, $managerInfo) = $this->_orderMatched($orderInfo['id']);
        
        $data = array(
            'order'    => $orderInfo,
            'parental' => $parentalInfo,
            'manager'  => $managerInfo,
            'map'      => OrderConfig::getOrderMap($orderInfo['status']),
        );
        
        $content = $this->view->fetch('order/audition_yuesao.php', $data);
        echo $content;
    }
    
    /**
     * @brief 提交预约面试信息
     */
    public function ajaxSubmitAuditionAction() {
        $id = Request::getPOST('audition_id', 0); //预约面试记录的id
        $auditionTime = Request::getPOST('audition_time', '');
        $auditionHour = Request::getPOST('audition_hour', 0);
        $auditionAddress = Request::getPOST('audition_address', '');
        $auditionTime = (!empty($auditionTime) && !empty($auditionHour)) ? (strtotime($auditionTime) + 3600 * $auditionHour) : 0;
        if (empty($auditionAddress) || empty($auditionTime)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '面试时间 或者 面试地址填写不对、请仔细核对')));
        }
        if (empty($id)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '缺少预约面试id')));
        }
        
        try {
            $client = Rpc::client('order.parentalOrder');
            $params = array(
                'id'               => $id,
                'audition_time'    => $auditionTime,
                'audition_address' => $auditionAddress,
            );
            $client->setAudition($params);
            exit(json_encode(array('errorCode' => 0, 'msg' => '预约面试成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief 签合同页面
     */
    public function contractAction() {
        $orderInfo = $this->_getOrderInfo();
        if (empty($orderInfo)) {
            $this->errorAction(array('获取订单数据失败！'));
        }
        
        //获取该订单合同状态的数据
        try {
            $client = Rpc::client('order.contract');
            $params = array(
                'order_id'   => $orderInfo['id'],
                'order_type' => GlobalConfig::ORDER_TYPE_MONTH,
            );
            $contractInfo = array();
            $info = $client->getOrderContract($params);
            if (!empty($info) && is_array($info)) {
                foreach ($info as $i) {
                    $contractInfo[] = array(
                        'create_time' => !empty($i['create_time']) ? date("Y-m-d H:i:s", $i['create_time']) : '',
                        'status_text' => isset(BcEnumerateConfig::$CONTRACT_STATUS[$i['status']]) ? BcEnumerateConfig::$CONTRACT_STATUS[$i['status']] : '',
                    );
                }
            }
            
            $data = array(
                'order' => $orderInfo,
                'contractInfo' => $contractInfo,
                'contractStatus' => isset($info['0']['status']) ? $info['0']['status'] : 0,
                'map' => OrderConfig::getOrderMap($orderInfo['status']),
            );
            
            $content = $this->view->fetch('order/contract_yuesao.php', $data);
            echo $content;
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }
    
    /**
     * @brief 订单完成、进行订单结算的页面
     */
    public function overAction() {
        //获取母订单数据
        $orderInfo = $this->_getOrderInfo();
        if (empty($orderInfo)) {
            $this->errorAction(array('获取订单数据失败！'));
        }
        
        //获取订单的档期详情
        $calendarClient = Rpc::client('daily.calendar');
        $calendar = $calendarClient->getCalendar(array('order_id' => $orderInfo['id'], 'order_type' => GlobalConfig::ORDER_TYPE_BABY));
        $restDate = !empty($calendar['rest_time']) ? $calendar['rest_time'] : array(); //档期内请假的时间戳,记录的事当天0点得时间戳
        
        //获取待客户确认的子订单
        $orderSubInfo = array();
        try {
            $orderSubClient = Rpc::client('order.orderSub');
            $subInfo = $orderSubClient->getCheckSub(array('order_id' => $orderInfo['id'], 'order_type' => $orderInfo['order_type']));
            if (!empty($subInfo) && is_array($subInfo)) {
                foreach ($subInfo as $info) {
                    $startDate = !empty($info['start_time']) ? date("Y-m-d", $info['start_time']) : '';
                    $overDate  = !empty($info['over_time']) ? date("Y-m-d", $info['over_time']) : '';
                    //初始化日期数量
                    $dayTotal  = $dayNormal = $dayRest = $weekTotal = $legalTotal = 0;
                    if (!empty($startDate) && !empty($overDate)) {
                        //把正常上班的天数初始化为全部正常上班
                        $startUnix = strtotime($startDate);
                        $overUnix  = strtotime($overDate);
                        $dayTotal  = $dayNormal = ($overUnix - $startUnix) / 86400;
                        
                        $dealUnix = $startUnix; //处理的时间戳、遍历子订单时间范围的每一天来分析
                        do {
                            if (isset($restDate[$dealUnix])) {
                                //说明该处理时间发生过休假事件
                                if (isset(SelfConfig::$REST_TYPE_LONG[$restDate[$dealUnix]])) {
                                    $dayRest += SelfConfig::$REST_TYPE_LONG[$restDate[$dealUnix]];
                                    if (SelfConfig::$REST_TYPE_LONG[$restDate[$dealUnix]] < 1) {
                                        //小于一天的休假、可能半天还属于周末或法定假日.优先判断法定假日
                                        $week = date('w', $dealUnix);
                                        $md = date('Y-m-d', $dealUnix);
                                        if (in_array($md, GlobalConfig::$LEGAL_DATE)) {
                                            //是法定假日上了半天班
                                            $legalTotal += 0.5;
                                        } else if ($week == 0 || $week == 6) {
                                            $weekTotal += 0.5;
                                        }
                                    }
                                }
                            } else {
                                //说明这天正常上班、判断是周末还是法定假日
                                $week = date('w', $dealUnix);
                                $md = date('Y-m-d', $dealUnix);
                                if (in_array($md, GlobalConfig::$LEGAL_DATE)) {
                                    //法定假日上班、比周末优先计算
                                    $legalTotal += 1;
                                } else if ($week == 0 || $week == 6) {
                                    $weekTotal += 1;
                                }
                            }
                            $dealUnix += 86400;
                        } while ($dealUnix <= $overUnix);
                        
                        $dayNormal = $dayNormal - $dayRest - $weekTotal - $legalTotal;
                    }
                    
                    $orderSubInfo[] = array(
                        'sub_id'     => Util::getFromArray('id', $info, 0),
                        'b_amount'   => Util::getFromArray('b_amount', $info, 0),
                        'b_tip'      => Util::getFromArray('b_tip', $info, 0),
                        'b_reward'   => Util::getFromArray('b_reward', $info, 0),
                        'b_wage'     => Util::getFromArray('b_wage', $info, 0),
                        'b_fine'     => Util::getFromArray('b_fine', $info, 0),
                        'c_frozen'   => Util::getFromArray('c_frozen', $info, 0),
                        'plat_amount' => Util::getFromArray('plat_amount', $info, 0),
                        'start_time' => $startDate,
                        'over_time'  => $overDate,
                        'reward_cn'  => Util::getFromArray('b_reward_cn', $info, 0),
                        'fine_cn'    => Util::getFromArray('b_fine_cn', $info, 0),
                        'confirm'    => Util::getFromArray('customer_confirm', $info, 0),
                        'day_total'  => $dayTotal, //总的天数
                        'day_normal' => $dayNormal, //正常上班的天数
                        'day_week'   => $weekTotal, //周末上班的天数
                        'day_legal'  => $legalTotal, //法定假日上班天数
                    );
                }
            }
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
        
        $data = array(
            'order'   => $orderInfo,
            'map'     => OrderConfig::getOrderMap($orderInfo['status']),
            'subInfo' => $orderSubInfo,
        );
        $content = $this->view->fetch('order/over.php', $data);
        echo $content;
    }
    
    /**
     * @brief ajax添加月嫂订单
     */
    public function ajaxAddYuesaoOrderAction() {
        $salary       = Request::getPOST('salary', '');
        $workAge      = Request::getPOST('work_age', '');
        $serviceTimeB = Request::getPOST('service_time_b', 0);
        $serviceDay   = Request::getPOST('service_day', 0);
        $birthday     = Request::getPOST('birthday', 0);
        $hourB        = Request::getPOST('hour_b', 0);
        $isEdit       = Request::getPOST('is_edit', 0);
        $id           = Request::getPOST('id', 0);
        $wxOrderId    = Request::getPOST('wx_order_id', 0);
        $customerId   = Request::getPOST('customer_id', 0);
        
        if ($isEdit && empty($id)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '缺少订单ID无法编辑')));
        }
        if (empty($hourB)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '服务开始的小时必须选择')));
        }
        if (empty($customerId) && !$isEdit) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '缺少客户id无法添加订单')));
        }

        $serviceTimeB = !empty($serviceTimeB) ? strtotime($serviceTimeB) + 3600 * $hourB : 0;
        $info = array(
            'customer_name'   => Request::getPOST('customer_name', ''),
            'mobile'          => Request::getPOST('mobile', ''),
            'birthday'        => !empty($birthday) ? strtotime($birthday) : 0,
            'baby_type'       => Request::getPOST('baby_type', 0),
            'customer_native' => Request::getPOST('customer_native', ''),
            'customer_nation' => Request::getPOST('customer_nation', 0),
            'hold_address'    => Request::getPOST('hold_address', ''),
            'hospital'        => Request::getPOST('hospital', ''),
            'require'         => Request::getPOST('require', ''),
            'mark'            => Request::getPOST('mark', ''),
            'salary'          => !empty($salary) ? trim($salary, ',') : '',
            'work_age'        => !empty($workAge) ? trim($workAge, ',') : '',
            'service_time_b'  => $serviceTimeB,
            'service_time_e'  => !empty($serviceDay) ? $serviceTimeB + 86400 * $serviceDay : 0,
        );
        
        try {
            $client = Rpc::client('order.yuesaoOrder');
            $params = array(
                'info' => $info,
                'user' => $this->userInfo['user']['id'],
                'customer_id' => isset($customerId) ? $customerId : 0,
                'id'   => $id,
            );
            $isEdit ? $client->edit($params) : $client->add($params);
            $msg = $isEdit ? '修改订单成功啦！' : '创建成功啦！';

            if ($wxOrderId) {
                //添加成功后、把微信订单设置为已处理状态
                $wxOrderClient = Rpc::client('order.weixinOrder');
                $wxOrderClient->setDeal(array('id' => $wxOrderId));
            }
            exit(json_encode(array('errorCode' => 0, 'msg' => $msg)));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief ajax添加预约面试结果。
     */
    public function ajaxSubAuditionResultAction() {
        $auditionId = Request::getPOST('audition_id', 0);
        $result = Request::getPOST('result');
        $quote  = Request::getPOST('quote', 0);
        if (empty($auditionId) || empty($result)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '参数缺失')));
        }
        if ($result == BcEnumerateConfig::AUDITION_STATUS_PASS && empty($quote)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '面试通过必须填写订单报价')));
        }
        
        try {
            $client = Rpc::client('order.yuesaoOrder');
            $params = array(
                'result' => $result,
                'audition_id' => $auditionId,
                'quote' => $quote,
            );
            $client->setAuditionResult($params);
            $msgInfo = array('errorCode' => 0, 'msg' => '提交面试结果成功啦！');
            $msgInfo['is_pass'] = $result == BcEnumerateConfig::AUDITION_STATUS_PASS ? 1 : 0;
            exit(json_encode($msgInfo));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief 通过订单id获取订单信息
     */
    private function _getOrderInfo() {
        $orderId = Request::getGET('order_id', 0);
        if (empty($orderId) || !is_numeric($orderId)) {
            $this->errorAction(array('缺少订单ID、无法获取订单详情！'));
        }
        
        try {
            $client = Rpc::client('order.yuesaoOrder');
            $info = $client->getById(array('id' => $orderId));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
        
        $orderInfo = array();
        if (!empty($info) && is_array($info)) {
            $orderInfo = array(
                'id'             => Util::getFromArray('id', $info, 0),
                'order_num'      => OrderHelper::makeOrderNum($info['id']),
                'customer_name'  => Util::getFromArray('customer_name', $info, ''),
                'service_time_b' => !empty($info['service_time_b']) ? date("Y-m-d H:i:s", $info['service_time_b']) : '',
                'service_time_e' => !empty($info['service_time_e']) ? date("Y-m-d H:i:s", $info['service_time_e']) : '',
                'service_type'   => OrderHelper::getYuesaoType($info['birthday']),
                'deposit'        => Util::getFromArray('deposit', $info, 0),
                'status'         => Util::getFromArray('status', $info, 0),
                'order_type'     => GlobalConfig::ORDER_TYPE_MONTH,
                'quote'          => Util::getFromArray('quote', $info, 0),
            );
        }
        return $orderInfo;
    }
    
    /**
     * @brief 获取订单关联的匹配的月嫂信息、（待面试、或者面试中得）
     * @param $orderId 订单ID
     */
    private function _orderMatched($orderId) {
        if (empty($orderId) || !is_numeric($orderId)) {
            return array();
        }
        try {
            $client = Rpc::client('order.yuesaoOrder');
            $auditionInfo = $client->getMatch(array('order_id' => $orderId));
            $workerIds = array();
            $auditionIds = array();
            if (empty($auditionInfo) || !is_array($auditionInfo)) {
                return array(array(), array());
            }
            foreach ($auditionInfo as $audition) {
                $workerIds[] = $audition['worker_id'];
                $auditionIds[$audition['worker_id']] = array(
                    'audition_id'      => Util::getFromArray('id', $audition, 0),
                    'audition_time'    => !empty($audition['audition_time']) ? date("Y-m-d", $audition['audition_time']) : '',
                    'audition_hour'    => !empty($audition['audition_time']) ? date("H", $audition['audition_time']) : '',
                    'audition_address' => Util::getFromArray('audition_address', $audition, ''),
                    'audition_status'  => Util::getFromArray('status', $audition, 0),
                );
            }
            $client = Rpc::client('worker.worker');
            $params = array(
                'filters' => array(
                    array('id', 'in', $workerIds)
                ),
                'type' => GlobalConfig::WORKER_MONTH,
            );
            $info = $client->getUserList($params);
            
            $parentalInfo = array(); //月嫂嫂信息
            $managerInfo = array(); //经纪人信息
            if (!empty($info) && is_array($info)) {
                $managerIds = array();
                foreach ($info as $i) {
                    $faceParams = array(
                        'file' => Util::getFromArray('FaceUrl', $i, ''),
                        'option' => SelfConfig::$FACE_URL_OPT,
                    );
                    
                    $parentalInfo[] = array(
                        'id'        => Util::getFromArray('id', $i, 0),
                        'mobile'    => Util::getFromArray('Mobile', $i, ''),
                        'face_url'  => ImgUtil::getUrl($faceParams),
                        'full_name' => Util::getFromArray('FullName', $i, ''),
                        'age'       => !empty($i['BornTime']) ? (date("Y") - date("Y", strtotime($i['BornTime']))) : '',
                        'address'   => Util::getFromArray('Address', $i, ''),
                        'reg_time'  => isset($i['CreatedTime']) && $i['CreatedTime'] ? date("Y-m-d", strtotime($i['CreatedTime'])) : '',
                        'manager'   => Util::getFromArray('ManagerId', $i, 0),
                        'audition_id'      => isset($auditionIds[$i['id']]['audition_id']) ? $auditionIds[$i['id']]['audition_id'] : 0,
                        'audition_time'    => isset($auditionIds[$i['id']]['audition_time']) ? $auditionIds[$i['id']]['audition_time'] : '',
                        'audition_hour'    => isset($auditionIds[$i['id']]['audition_hour']) ? $auditionIds[$i['id']]['audition_hour'] : '',
                        'audition_address' => isset($auditionIds[$i['id']]['audition_address']) ? $auditionIds[$i['id']]['audition_address'] : '',
                        'audition_status'  => isset($auditionIds[$i['id']]['audition_status']) ? $auditionIds[$i['id']]['audition_status'] : '',
                    );
                    //收集经济人id
                    !empty($i['ManagerId']) && $managerIds[] = $i['ManagerId'];
                }
                
                //获取经纪人信息
                $managerClient = Rpc::client('sso.manager');
                $manager = $managerClient->getById(array('manager_id' => $managerIds));
            }
            
            if (!empty($manager) && is_array($manager)) {
                foreach ($manager as $m) {
                    $managerInfo[$m['Id']] = $m;
                }
                unset($manager);
            }
            
            return array($parentalInfo, $managerInfo);
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }


    /**
     * @brief 添加月嫂订单的表单域验证配置
     * @param $data 表单域的数据
     */
    private function _setField($data = array()) {
        $formObj = new Form();
        //客户姓名
        $customerNameField = array(
            'name'         => 'customer_name',
            'focusMessage' => '只能输入2-7个字',
            'postValue'    => !empty($data['customer_name']) ? $data['customer_name'] : 'null',
            'tipTarget'    => '#customer_name_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '客户姓名不能为空',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 2,
                    'max'  => 7,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );

        //客户手机
        $mobileField = array(
            'name'         => 'mobile',
            'focusMessage' => '请填写正确的联系方式',
            'postValue'    => !empty($data['mobile']) ? $data['mobile'] : 'null',
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

        //宝宝生日
        $birthdayField = array(
            'name'         => 'birthday',
            'tipTarget'    => '#birthday_tip',
            'postValue'    => (!empty($data['birthday']) && $data['birthday'] > 0) ? date("Y-m-d", $data['birthday']) : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '宝宝生日必须填写',
                ),
            ),
        );

        if (!empty($data['service_time_b']) && $data['service_time_b'] > 0) {
            $startDate = date("Y-m-d", $data['service_time_b']);
            $startHour = (int) date("H", $data['service_time_b']);
        }
        //服务开始日期
        $serviceBField = array(
            'name'      => 'service_time_b',
            'tipTarget' => '#service_time_b_tip',
            'next'      => 'hour_b',
            'postValue' => !empty($startDate) ? $startDate : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '服务开始日期必须填写',
                ),
            ),
        );

        $serviceDay = 0;
        if (!empty($data['service_time_e']) && $data['service_time_e'] > 0) {
            $serviceDay = (int) ($data['service_time_e'] - $data['service_time_b']) / 86400;
        }
        //服务天数
        $serviceDayField = array(
            'name'      => 'service_day',
            'tipTarget' => '#service_day_tip',
            'postValue' => !empty($serviceDay) ? $serviceDay : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '服务天数必须填写',
                ),
            ),
        );

        //服务开始小时
        $hourBField = array(
            'name'      => 'hour_b',
            'tipTarget' => '#service_time_b_tip',
            'defaultMessage' => '填写日期、同时选择具体开始的小时数',
            'postValue' => !empty($startHour) ? $startHour : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '服务开始时间必须选择',
                ),
            ),
        );

        //宝宝是（单双胞胎)
        $babyTypeField = array(
            'name'         => 'baby_type',
            'postValue'    => !empty($data['baby_type']) ? $data['baby_type'] : 'null',
            'tipTarget'    => '#baby_type_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '宝宝类型必须选择',
                ),
            ),
        );

        //客户籍贯
        $customerNativeField = array(
            'name'         => 'customer_native',
            'defaultMessage' => '请填写客户籍贯 格式如：福建 厦门',
            'postValue'    => !empty($data['customer_native']) ? $data['customer_native'] : 'null',
            'tipTarget'    => '#customer_native_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::NO_SPECIAL,
                    'not'  => true,
                    'errorMessage' => '格式不对',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 15,
                    'errorMessage' => '格式不对'
                ),
            ),
        );

        //客户民族
        $customerNationField = array(
            'name'         => 'customer_nation',
            'postValue'    => !empty($data['customer_nation']) ? $data['customer_nation'] : 'null',
            'tipTarget'    => '#customer_nation_tip',
            'rules'        => array(
            ),
        );

        //生产医院
        $hospitalField = array(
            'name'      => 'hospital',
            'postValue' => !empty($data['hospital']) ? $data['hospital'] : 'null',
            'tipTarget' => '#hospital_tip',
            'rules' => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 100,
                    'errorMessage' => '字数超出限制了、限制100字以内'
                ),
            ),
        );

        //上户地址
        $holdAddressField = array(
            'name'      => 'hold_address',
            'postValue' => !empty($data['hold_address']) ? $data['hold_address'] : 'null',
            'tipTarget' => '#hold_address_tip',
            'rules' => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 100,
                    'errorMessage' => '字数超出限制了、限制100字以内'
                ),
            ),
        );

        //月嫂薪资范围
        $salaryField = array(
            'name'         => 'salary',
            'postValue'    => !empty($data['salary']) ? $data['salary'] : 'null',
            'tipTarget'    => '#salary_tip',
            'rules'        => array(
            ),
        );
        //月嫂工作年限
        $workAgeField = array(
            'name'         => 'work_age',
            'postValue'    => !empty($data['work_age']) ? $data['work_age'] : 'null',
            'tipTarget'    => '#work_age_tip',
            'rules'        => array(
            ),
        );

        //特殊需求
        $requireField = array(
            'name'      => 'require',
            'postValue' => !empty($data['require']) ? $data['require'] : 'null',
            'tipTarget' => '#require_tip',
            'rules' => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 300,
                    'errorMessage' => '字数超出限制了、限制300字以内'
                ),
            ),
        );

        //订单备注
        $markField = array(
            'name'      => 'mark',
            'postValue' => !empty($data['mark']) ? $data['mark'] : 'null',
            'tipTarget' => '#mark_tip',
            'rules' => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 200,
                    'errorMessage' => '字数超出限制了、限制200字以内'
                ),
            ),
        );

        $formObj->addFields(array(
            $customerNameField,
            $mobileField,
            $birthdayField,
            $serviceBField,
            $serviceDayField,
            $customerNativeField,
            $hospitalField,
            $holdAddressField,
            $requireField,
            $salaryField,
            $babyTypeField,
            $workAgeField,
            $hourBField,
            $customerNationField,
            $markField,
        ));
        //返回该表单域对象
        return $formObj;
    }
}
