<?php

require_once dirname(__FILE__) . '/include/SsoBasePage.class.php';

/**
 * @brief 用户管理
 * @author aozhongxu
 */

class UserPage extends SsoBasePage {
    
    public function listAction() {
        $this->render(array(
            'dpt' => SsoVars::$DEPARTMENT,
            'datagrid' => $this->getDataGrid(),
        ), 'user_list.php');
    }
    
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/sso/user/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('id', '编号', 'width: 5%');
        $dataGrid->setCol('account', '帐号', 'width: 15%');
        $dataGrid->setCol('name', '姓名', 'width: 10%');
        $dataGrid->setCol('department', '部门', 'width: 15%');
        $dataGrid->setCol('role', '职位', 'width: 10%');
        $dataGrid->setCol('last_login', '上次登录', 'width: 15%');
        $dataGrid->setCOl('status', '状态', 'width: 10%');
        $dataGrid->setCol('op', '操作', 'width: 20%');
        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }
    
    public function fieldFormat($name, $row) {
        if ($name == 'status') {
            return $row['forbidden'] == 1 ? '禁用' : '正常';
        }
        if ($name == 'department') {
            return SsoVars::$DEPARTMENT[$row['department']];
        }
        if ($name == 'last_login' && !empty($row[$name])) {
            return empty($row[$name]) ? '' : date('Y-m-d H:i:s', $row['last_login']);
        }
        if ($name == 'op') {
            $str = '<a href="/sso/user/iframeEdit/?id=' . $row['id'] . '" data-action-type="edit">查看|编辑</a>&nbsp;&nbsp;';
            $str .= '<a href="/sso/user/groupManager/?id=' . $row['id'] . '">组管理</a>&nbsp;&nbsp;';
            $str .= '<a href="/sso/user/ajaxChangeStatus/?id=' . $row['id'] . '" data-action-type="change-status" >' . ($row['forbidden'] == 1 ? '启用' : '禁用') . '</a>';
            return $str;
        }
        return empty($row[$name]) ? '' : $row[$name];
    }
    
    public function ajaxGetDataAction() {
        // 定义datagrid每页显示条数
        $pageSize = 20;
        
        $dataGrid = $this->getDataGrid();
        $page = RequestUtil::getGET('page', 1);
        
        // 获取记录，总数
        $userTable = new SsoUserModel();
        $where = $this->getWhere();
        $all = $userTable->getAll('*', $where, '', $pageSize, ($page-1)*$pageSize);
        $count = $userTable->getOne('count(1)');
        
        $dataGrid->setData($all);
        $dataGrid->setPager($count, $page, $pageSize);
        $data = $dataGrid->getData();
        $this->render(array(
            'data' => $data,
        ));
    }

    public function getWhere() {
        $account = RequestUtil::getGET('account', '');
        $name = RequestUtil::getGET('name', '');
        $forbidden = RequestUtil::getGET('forbidden', '-1');
        $department = RequestUtil::getGET('department', '-1');
        $where = array();
        if ($account) {
            $where[] = array('username', 'like', '%' . $account . '%');
        }
        if ($name) {
            $where[] = array('real_name', 'like', '%' . $name . '%');
        }
        if ($forbidden != -1) {
            $where[] = array('status', '=', $forbidden ? 0 : 1);
        }
        if ($department != -1) {
            $where[] = array('dept_id', '=', $department);
        }
        return $where;
    }

    public function addAction() {
        if (RequestUtil::isPOST()) {
            
            $userTable = new SsoUserModel();
            $row = array(
                'username'    => RequestUtil::getPOST('account'),
                'passwd'   => SsoVars::getInitPassword(),
                'real_name'       => RequestUtil::getPOST('name'),
                'sex'        => RequestUtil::getPOST('sex'),
                'email'      => RequestUtil::getPOST('email'),
                'mobile'      => RequestUtil::getPOST('phone'),
                'role_id'       => RequestUtil::getPOST('role'),
                'dept_id' => RequestUtil::getPOST('department'),
                //'leader'     => RequestUtil::getPOST('leader'),
                //'entry_time' => strtotime(RequestUtil::getPOST('entry_time')),
                'insert_time'    => time(),
                'birth_day'   => strtotime(RequestUtil::getPOST('birthday')),
            );
            
            $ret = $userTable->insert($row);
            if ($ret) {
                $this->render(array(
                    'dpt' => SsoVars::$DEPARTMENT,
                    'color' => 'green',
                    'result' => '添加成功',
                ), 'user_add.php');
            } else {
                $this->render(array(
                    'dpt' => SsoVars::$DEPARTMENT,
                    'color' => 'red',
                    'result' => '添加失败',
                ), 'user_add.php');
            }
        } else {
            $this->render(array(
                'dpt' => SsoVars::$DEPARTMENT,
            ), 'user_add.php');
        }
    }

    public function passwordAction() {
        $this->render(array(), 'user_password.php');
    }
    
    public function deleteAction() {
        $this->render(array(), 'user_delete.php');
    }
    
    public function ajaxGetInfoAction() {
        
        $userTable = new SsoUserModel();
        
        $account = RequestUtil::getPOST('account');
        $row = $userTable->getRow('*', array(
            array('username', '=', $account),
        ));
        if (!empty($row)) {
            $dpt = SsoVars::$DEPARTMENT[$row['department']];
            $row['department'] = $dpt;
            $this->render(array(
                'errorCode' => '0',
                'row' => $row,
            ));
        } else {
            $this->render(array(
                'errorCode' => '1',
            ));
        }
    }
    
    public function ajaxChangeStatusAction() {
        $id = RequestUtil::getGET('id');
        SsoData::changeUserStatus($id);
    }
    
    public function ajaxPasswordAction() {
        $id = RequestUtil::getPOST('id');
        $ret = SsoData::initUserPassword($id);
        $this->render(array(
            'errorCode' => $ret ? '0' : '1',
        ));
    }
    
    public function ajaxDeleteAction() {
        $id = RequestUtil::getPOST('id');
        SsoData::deleteUserById($id);
    }
    
    public function iframeEditAction() {
        $id = RequestUtil::getGET('id');
        $userTable = new SsoUserModel();
        $row = $userTable->getRow('*', array(
            array('id', '=', $id),
        ));
        $this->render(array(
            'row' => $row,
        ), 'iframe/user_list_edit.php');
    }
    
    public function ajaxEditAction() {
        $row = array(
            'birthday' => strtotime(RequestUtil::getPOST('birthday')),
            'entry_time' => strtotime(RequestUtil::getPOST('entry_time')),
            'email' => RequestUtil::getPOST('email'),
            'phone' => RequestUtil::getPOST('phone'),
            'leader' => RequestUtil::getPOST('leader'),
        );
        $id = RequestUtil::getPOST('id');
        $userTable = new SsoUserModel();
        $userTable->update($row, array(
            array('id', '=', $id),
        ));
    }
    
    public function groupManagerAction() {
        
        $userTable = new SsoUserModel();
        
        $user_id = RequestUtil::getGET('id');
        $userRow = $userTable->getRow('*', array(
            array('id', '=', $user_id),
        ));
        $groupAll = SsoData::getGroupByUserId($user_id);
        $number = count($groupAll);
        $this->render(array(
            'all' => $groupAll,
            'number' => $number,
            'account' => $userRow['account'],
        ), 'user_group_manager.php');
    }
    
    public function ajaxGetOtherGroupAction() {
        $name = RequestUtil::getPOST('name');
        $id = (int) $name;
        $groupTable = new SsoGroupModel();
        $all = $groupTable->getAll('*', array(
            array('OR' => array(
                array('id', '=', $id),
                array('name', 'like', '%' . $name . '%'),
            ))
        ));
        $this->render(array(
            'all' => $all,
        ));
    }
    
    public function ajaxAddGroupToUserAction() {
        $user_id = RequestUtil::getPOST('user_id');
        $group_ids = json_decode(RequestUtil::getPOST('group_ids'));
        SsoData::assignGroupToUser($group_ids, $user_id);
    }

}
