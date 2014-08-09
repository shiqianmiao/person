<?php

require_once dirname(__FILE__) . '/include/SsoBasePage.class.php';

/**
 * @brief 组管理
 * @author aozhongxu
 */

class GroupPage extends SsoBasePage {
    
    public function listAction() {
        $this->render(array(
            'datagrid' => $this->getDataGrid(),
        ), 'group_list.php');
    }
    
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/sso/group/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('id', '编号', 'width: 10%');
        $dataGrid->setCol('code', '唯一码', 'width: 10%');
        $dataGrid->setCol('name', '组名', 'width: 20%');
        $dataGrid->setCol('brief', '描述', 'width: 40%');
        $dataGrid->setCol('op', '操作', 'width: 20%');
        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }
    
    public function fieldFormat($name, $row) {
        if ($name == 'op') {
            $str = '<a href="/sso/group/iframeEdit/?id=' . $row['id'] . '" data-action-type="edit"">编辑</a>&nbsp;&nbsp;';
            $str .= '<a href="/sso/group/userManager/?id=' . $row['id'] . '">用户管理</a>&nbsp;&nbsp;';
            $str .= '<a href="/sso/group/privilegeManager/?id=' . $row['id'] . '">权限管理</a>&nbsp;&nbsp;';
            $str .= '<a href="/sso/group/ajaxDelete/?id=' . $row['id'] . '" data-action-type="delete">删除</a>';
            return $str;
        }
        return empty($row[$name]) ? '' : $row[$name];
    }
    
    public function ajaxGetDataAction() {
        
        // 定义datagrid每页显示条数
        $pageSize = 20;
        
        $dataGrid = $this->getDataGrid();
        $page = (int)RequestUtil::getGET('page', 1);
        
        // 获取记录，总数
        
        $groupTable = new SsoGroupModel();
        $filter = array(
            array('name', 'like', '%' . urldecode(RequestUtil::getGET('name', '')) . '%')
        );
        $all = $groupTable->getAll('*', $filter, '', $pageSize, ($page-1)*$pageSize);
        $count = $groupTable->getOne('count(1)');
        
        $dataGrid->setData($all);
        $dataGrid->setPager($count, $page, $pageSize);
        $data = $dataGrid->getData();
        $this->render(array(
            'data' => $data,
        ));
    }

    public function addAction() {
        $this->render(array(), 'group_add.php');
    }
    
    public function ajaxAddAction() {
        
        $groupTable = new SsoGroupModel();
        
        $ret = $groupTable->insert(array(
            'name' => RequestUtil::getPOST('name'),
            'brief' => RequestUtil::getPOST('brief'),
            'code' => RequestUtil::getPOST('code'),
        ));
        
        $this->render(array(
            'errorCode' => $ret ? '0' : '1',
        ));
    }
    
    public function ajaxDeleteAction() {
        $id = $_GET['id'];
        SsoData::deleteGroupById($id);
    }
    
    public function iframeEditAction() {
        
        $groupTable = new SsoGroupModel();
        $row = $groupTable->getRow('*',array(
            array('id', '=', RequestUtil::getGET('id')),
        ));
        
        $this->render(array(
            'row' => $row,
        ), 'iframe/group_list_edit.php');
    }
    
    public function ajaxEditAction() {
        
        $groupTable = new SsoGroupModel();
        $groupTable->update(array(
            'brief' => RequestUtil::getPOST('brief'),
            'name' => RequestUtil::getPOST('name'),
            'code' => RequestUtil::getPOST('code'),
        ), array(
            array('id', '=', RequestUtil::getPOST('id'))
        ));
    }
    
    public function userManagerAction() {
        
        $groupTable = new SsoGroupModel();
        $group_id = RequestUtil::getGET('id');
        
        $groupRow = $groupTable->getRow('*', array(
            array('id', '=', $group_id),
        ));
        
        $userAll = SsoData::getUserByGroupId($group_id);
        $number = count($userAll);
        $this->render(array(
            'all' => $userAll,
            'number' => $number,
            'name' => $groupRow['name'],
        ), 'group_user_manager.php');
    }
    
    public function ajaxAddUserToGroupAction() {
        $user_ids = json_decode(RequestUtil::getPOST('user_ids'));
        $group_id = RequestUtil::getPOST('group_id');
        SsoData::assignUserToGroup($user_ids, $group_id);
    }
    
    public function privilegeManagerAction() {
        
        $id = RequestUtil::getGET('id');
        $groupTable = new SsoGroupModel();
        $appTable = new SsoAppModel();
        
        $row = $groupTable->getRow('*', array(
            array('id', '=', $id),
        ));
        $allApp = $appTable->getAll('*', '', array(
            'app' => 'ASC',
        ));
        $this->render(array(
            'allApp' => $allApp,
            'name' => $row['name'],
        ), 'group_privilege_manager.php');
    }
    
    public function ajaxAddUserToGroupByIdsAction(){
    	$groupId = RequestUtil::getGET('groupId');
    	$roleId = RequestUtil::getGET('roleId');
    	
    	$users = new SsoUserModel();
    	$where = array();
    	$where[] = array('status','=',1);
    	if($roleId){
    		$where[] = array('role_id','=',$roleId);
    	}
    	$users = $users->getAll('*',$where);
    	if(!empty($users)){
    		$ids = array();
    		foreach ($users as $user){
    			$ids[] = $user['id'];
    		}
    		SsoData::assignUserToGroup($ids, $groupId);
    	}
    	echo '111';
    }
}
