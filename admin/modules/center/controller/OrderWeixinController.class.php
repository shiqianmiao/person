<?php
/**
 * @brief 简介：微信订单列表
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年3月12日 下午3:58:50
 */

class OrderWeixinController extends BcController {
    public function init() {
        
    }
    /**
     * @brief 订单列表页面
     */
    public function defaultAction() {
        $data = array(
            'datagrid' => $this->getDataGrid(),
            'statusMap' => BcEnumerateConfig::$ORDER_WEIXIN_STATUS,
        );
        $content = $this->view->fetch('order_weixin/index.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/orderWeixin/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '客户手机', 'width:12%');
        $dataGrid->setCol('field2', '客户姓名', 'width: 10%');
        $dataGrid->setCol('field3', '地址', 'width:18%');
        $dataGrid->setCol('field4', '参考类型', 'width:10%');
        $dataGrid->setCol('field5', '状态', 'width:10%');
        $dataGrid->setCol('field6', '添加时间', 'width:15%');
        $dataGrid->setCol('field7', '操作', 'width:25%');

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
            $client  = Rpc::client('order.weixinOrder');
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            //构建参数
            $filters = array();
            $status  = Request::getGET('status', 0);
            $cusName = Request::getGET('customer_name', '');
            $mobile  = Request::getGET('mobile', '');
            if (!empty($status)) {
                $filters[] = array('status', '=', $status);
            }
            if (!empty($cusName)) {
                $filters[] = array('name', 'like', '%' . $cusName . '%');
            }
            if (!empty($mobile)) {
                $filters[] = array('mobile', '=', $mobile);
            }
            //如果都没有任何筛选条件的情况下、默认展示过滤掉关闭的订单
            if (empty($status)) {
                $filters[] = array('status', '=', SelfConfig::WEIXIN_ORDER_NORMAL);
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
                $row['field1'] = Util::getFromArray('mobile', $order, '');
                $row['field2'] = Util::getFromArray('name', $order, '');
                $row['field3'] = Util::getFromArray('address', $order, '');
                $row['field4'] = isset(BcEnumerateConfig::$ORDER_WEIXIN_TYPE[$order['type']]) ?
                    BcEnumerateConfig::$ORDER_WEIXIN_TYPE[$order['type']] : '';
                $row['field5'] = isset(BcEnumerateConfig::$ORDER_WEIXIN_STATUS[$order['status']]) ?
                    BcEnumerateConfig::$ORDER_WEIXIN_STATUS[$order['status']] : '';
                $row['field6'] = !empty($order['create_time']) ? date('Y-m-d H:i', $order['create_time']) : '';
                $row['field7'] = '';
                if (in_array('bc.*', $code) || in_array('bc.service', $code)) {
                    $row['field7'] .= '<a target="_blank" data-id="'.$order['id'].'" href="javascript:;" data-action-type="del">删除</a>';
                    if ($order['status'] == SelfConfig::WEIXIN_ORDER_NORMAL) {
                        $row['field7'] .= ' | <a target="_blank" href="/center/orderCommon/addOrderOne/?type=2&wx_order_id=' . $id . '">下月嫂订单</a>';
                        $row['field7'] .= ' | <a target="_blank" href="/center/orderCommon/addOrderOne/?type=1&wx_order_id=' . $id . '">下育儿嫂订单</a>';
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
     * @brief ajax 删除微信订单
     */
    public function ajaxDelAction() {
        $id = Request::getPOST('id', 0);
        if (empty($id)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '微信订单id缺失、无法删除')));
        }

        try {
            $client = Rpc::client('order.weixinOrder');
            $client->setDel(array('id' => $id));
            exit(json_encode(array('errorCode' => 0, 'msg' => '删除成功啦！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
}