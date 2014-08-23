<?php

require_once dirname(__FILE__) . '/include/SsoBasePage.class.php';

/**
 * @brief 应用管理
 * @author aozhongxu
 */

class AppPage extends SsoBasePage {
    
    public function listAction() {

        $this->render(array(
            'datagrid' => $this->getDataGrid(),
        ), 'app_list.php');
    }
    
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/sso/app/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('id', '编号', 'width: 10%');
        $dataGrid->setCol('app', '应用代号', 'width: 15%');
        $dataGrid->setCol('name', '应用名称', 'width: 15%');
        $dataGrid->setCol('brief', '应用描述', 'width: 40%');
        $dataGrid->setCol('op', '操作', 'width: 20%');
        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }
    
    public function fieldFormat($name, $row) {
        if ($name == 'op') {
            $str = '<a href="/sso/app/iframeEdit/?id=' . $row['id'] . '" data-action-type="edit"">编辑</a>';
            return $str;
        }
        return empty($row[$name]) ? '' : $row[$name];
    }
    
    public function ajaxGetDataAction() {
        // 定义datagrid每页显示条数
        $pageSize = 20;
        $app = RequestUtil::getGET('app', '');
        $name = RequestUtil::getGET('name', '');
        $dataGrid = $this->getDataGrid();
        $page = (int)RequestUtil::getGET('page', 1);
        // 获取记录，总数
        $appTable = new SsoAppModel();
        $filter = array();
        if (!empty($app)) {
            $filter[] = array('app', 'like', '%' . $app . '%');
        }
        if (!empty($name)) {
            $filter[] = array('name', 'like', '%' . $name . '%');
        }
        $all = $appTable->getAll('*', $filter, '', $pageSize, ($page-1)*$pageSize);
        $count = $appTable->getOne('count(*)');
        
        $dataGrid->setData($all);
        $dataGrid->setPager($count, $page, $pageSize);
        $data = $dataGrid->getData();
        $this->render(array(
            'data' => $data,
        ));
    }

    public function addAction() {
        $this->render(array(), 'app_add.php');
    }
    
    public function ajaxAddAction() {
        $app = RequestUtil::getPOST('app');
        $name = RequestUtil::getPOST('name');
        $brief = RequestUtil::getPOST('brief');
        $ret = SsoData::addApp($app, $name, $brief);
        $this ->render(array(
            'errorCode' => empty($ret) ? '1' : '0',
        ));
    }

    public function iframeEditAction() {
        $appTable = new SsoAppModel();
        $row = $appTable->getRow('*', array(
            array('id', '=', RequestUtil::getGET('id')),
        ));
        $this->render(array(
            'row' => $row,
        ), 'iframe/app_list_edit.php');
    }
    
    public function ajaxEditAction() {
        $appTable = new SsoAppModel();
        $ret = $appTable->update(array(
            'brief' => RequestUtil::getPOST('brief'),
        ), array(
            array('id', '=', RequestUtil::getPOST('id')),
        ));
        
        if ($ret === false) {
            $this->render(array('errorCode' => 1, 'msg' => '编辑失败！'));
        }
        
        $this->render(array('errorCode' => 0, 'msg' => ''));
    }
}
