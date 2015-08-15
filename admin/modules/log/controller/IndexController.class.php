<?php

/**
 * Class IndexController
 * @author chenchaoyang@iyuesao.com
 * @since  2015-01-11
 */
class IndexController extends BcController {
    public function defaultAction() {
        $this->logListAction();
    }

    public function logListAction() {
        try {

            $data = array(
                'datagrid' => $this->getDataGrid(),
            );
            $content = $this->view->fetch('index.php', $data);
            echo $content;
        } catch (Yar_Server_Exception $e){
            var_dump($e->getMessage());
        }

    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/log/Index/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('field1', 'ID', 'width:10%');
        $dataGrid->setCol('field2', '详情', 'width: 10%');
        $dataGrid->setCol('field3', 'tag', 'width:10%');
        $dataGrid->setCol('field4', 'url', 'width:15%');
        $dataGrid->setCol('field5', '文件', 'width:25%');
        $dataGrid->setCol('field6', '时间', 'width:30%');
        $dataGrid->setCol('field7', '操作', 'width:10%');
        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }

    public function ajaxGetDataAction() {
        $dataGrid = $this->getDataGrid();
        $this->currentPage = (int) Request::getGET('page', 1);
        $allCount = 0;
        $this->pageSize = 10;

        try {
            $logClient = Rpc::client('log.log');

            $offset = ($this->currentPage - 1) * $this->pageSize;
            $params = array(
                'tag'    => Request::getREQUEST('tag', ''),
                'url'    => Request::getREQUEST('url', ''),
                'level'    => Request::getREQUEST('level', ''),
                'limit'  => $this->pageSize,
                'offset' => $offset,
            );
            $logList = $logClient->getList($params);
            $allCount = $logClient->getCount($params);

        } catch (Exception $e) {
            Log::logFatal(['tag' => 'log.logList','data' => $e->getMessage()]);
        }

        $list = [];
        foreach((array)$logList as $key => $log) {
            $trace = json_decode($log['trace'], true);
            $row['field1'] = $log['id'];
            $row['field2'] = json_decode($log['data'], true);
            $row['field3'] = $log['tag'];
            $row['field4'] = $log['url'];
            $row['field5'] = Util::getFromArray('file', $trace, var_export($trace, true));
            $row['field6'] = date('Y-m-d H:i:s', $log['create_time']);
            $row['field7'] = '<a href="/log/Index/getDetail/?id='. $log['id'] . '">详情<a>';
            $list[] = $row;

        }

        $dataGrid->setData($list);

        //设置分页信息
        $maxSize = $allCount > $this->maxSize ? $this->maxSize : $allCount;
        $dataGrid->setPager($maxSize, $this->currentPage, $this->pageSize);

        $data = $dataGrid->getData();
        echo json_encode(array('data' => $data));
    }

    /**
     * @brief 过滤数据处理
     */
    public function fieldFormat($name, $row) {
        return empty($row[$name]) && $row[$name] != 0 ? '' : $row[$name];
    }

    public function getDetailAction() {
        $id = Request::getGET('id', 0);
        if(empty($id)) {
            $logParams = [
                'tag' => 'log.getDetail',
                'data' => 'ID不能为空'
            ];
            Log::logWarn($logParams);
            throw new Exception('ID不能为空');
        }

        $logClient = Rpc::client('log.log');
        $log = $logClient->getById(['id' => (int)$id]);
        print_r($log);exit;
    }
}