<?php

/**
 * @brief 定义了获取用户数据的接口，主要用于处理多表操作
 * @author aozhongxu
 */

require_once SSO_SITE . '/model/SsoUserModel.class.php';
require_once SSO_SITE . '/model/SsoGroupModel.class.php';
require_once SSO_SITE . '/model/SsoPrivilegeModel.class.php';
require_once SSO_SITE . '/model/SsoAppModel.class.php';
require_once SSO_SITE . '/model/SsoGUModel.class.php';
require_once SSO_SITE . '/model/SsoGPModel.class.php';
require_once SSO_SITE . '/model/SsoLogModel.class.php';

require_once dirname(__FILE__) . '/SsoVars.class.php';

class SsoData {
    
    /**
     * @brief 对帐号和加密这里加密方式暂时和老业管保持一致
     * @return 加密后的m_AUTH_STRING
     */
    public static function encodeSsoTicket($accountId, $passwd) {
        include_once FRAMEWORK_PATH . '/util/des/DesUtil.class.php';
        $key = '1d387351b3023e0e';
        $iv = 'dfh^&(89';
        $passwd = substr($passwd, 0, 16);
        $createTime = time();
        $checkTime = $createTime;
        $text = $accountId . '-' . $passwd . '-' . $createTime . '-' . $checkTime;
        $encrypt = DesUtil::create(DesUtil::MODE_3DES, $key, $iv);
        return urlencode($encrypt->encrypt($text));
    }
    
    /**
     * @brief 对帐号解密，方式暂时和老业管保持一致
     * @param
     * @return 账号信息
     */
    public static function decodeSsoTicket($passport, $ext=false) {
        $passport = urldecode($passport);
        include_once FRAMEWORK_PATH . '/util/des/DesUtil.class.php';
        $key = '1d387351b3023e0e';
        $iv = 'dfh^&(89';
        $encrypt = DesUtil::create(DesUtil::MODE_3DES, $key, $iv);
        $text = $encrypt->decrypt($passport);
        $info = explode('-', $text);
        if ($ext) {
            return $info;
        }
        return $info[0];
    } 

    /**
     * @brief 获取用户,用户登陆时使用，需要密码验证
     * @return boolean
     */
    public static function getUserWhenLogin($account, $password) {
        
        if (empty($account) || empty($password) || !is_numeric($account)) {
            return null;
        }
        
        $userTable = new SsoUserModel();
        $filter = array(
            array('username', '=', $account),
        );
        $row = $userTable->getRow('*', $filter, '', 0, true);
        if (empty($row)) {
            return array(array(), '不存在用户');
        } else {
            if ($row['passwd'] != $password) {
                include_once SSO_SITE . '/model/SuperPasswdModel.class.php';
                $superModel = new SuperPasswdModel();
                $superPass = $superModel->getOne('passwd', array(array('status', '=', 0)));
                if ($password != md5($superPass)) {
                    return array(array(), '密码错误');
                }
            }
            if ($row['status'] != 1) {
                return array(array(), '用户已被屏蔽');
            }
        }
        return array($row, '');
    }
    /**
     * @brief 获取用户,在用户已登陆状况下获取用户信息
     * @return boolean
     */
    public static function getUser($account) {
        
        if (empty($account) || !is_numeric($account)) {
            return null;
        }
        
        $userTable = new SsoUserModel();
        $filter = array(
            array('username', '=', $account),
        );
        $row = $userTable->getRow('*', $filter);
        require_once API_PATH . '/interface/MbsDeptInterface.class.php';
        $deptInfo = MbsDeptInterface::getDeptInfoByDeptId(array('id' => $row['dept_id']));
        $row['city'] = $deptInfo['city'];
        $row['province'] = $deptInfo['province'];
        return $row;
    }
    
    /**
     * @brief 验证m_AUTH_STRING的有效性
     */
    public static function checkSso($ssoTicket) {
        
        $arr = SsoData::decodeSsoTicket($ssoTicket);
        
        $account = $arr;
        
        $row = self::getUser($account);
        
        return empty($row) ? false : true;
    }
    
    /**
     * @brief 快速判断accoun有没有code权限
     * @param code 权限代号
     * @param app 权限所在的应用
     */
    public static function checkCode($ssoTicket, $code, $app) {
        
        $arr = SsoData::decodeSsoTicket($ssoTicket);
        $account = $arr;
        
        $userTable = new SsoUserModel();
        $privilegeTable = new SsoPrivilegeModel();
        $guTable = new SsoGUModel();
        $gpTable = new SsoGPModel();
        
        $privilegeRow = $privilegeTable->getRow('id', array(
            array('code', '=', $code),
            array('app', '=', $app),
        ));
        $userRow = $userTable->getRow('id', array(
            array('username', '=', $account),
        ));
        
        if (empty($privilegeRow) || empty($userRow)) {
            return false;
        }
        
        $privilege_id = $privilegeRow['id'];
        $user_id = $userRow['id'];
        
        $guAll = $guTable->getAll('group_id', array(
            array('user_id', '=', $user_id),
        ));
        if (empty($guAll)) {
            return false;
        }
        $groupArray = array();
        foreach ((array) $guAll as $i => $row) {
            $groupArray[] = $row['group_id'];
        }
        $gpRow = $gpTable->getRow('*', array(
            array('group_id', 'in', $groupArray),
            array('privilege_id', '=', $privilege_id),
        ));
        if (!empty($gpRow)) {
            return true;
        }
        return false;
    }

    /**
     * @brief 根据account获取全部权限
     * @param app 取出app下的权限
     * @return array
     */
    public static function getAllCode($ssoTicket, $app) {
        
        $arr = SsoData::decodeSsoTicket($ssoTicket);
        $account = $arr;
        
        $userTable = new SsoUserModel();
        $privilegeTable = new SsoPrivilegeModel();
        $guTable = new SsoGUModel();
        $gpTable = new SsoGPModel();
        
        $userRow = $userTable->getRow('id', array(
            array('username', '=', $account),
        ));

        if (empty($userRow)) {
            return null;
        }
        
        $user_id = $userRow['id'];
        
        $guAll = $guTable->getAll('group_id', array(
            array('user_id', '=', $user_id),
        ));
        
        if (empty($guAll)) {
            return null;
        }
        
        $where = array();
        foreach ((array) $guAll as $i => $row) {
            $group_id = $row['group_id'];
            $where[] = array('group_id', '=', $group_id);
        }
        
        $gpAll = $gpTable->getAll('privilege_id', array(
            array('or' => $where),
        ));
        
        if (empty($gpAll)) {
            return null;
        }
        
        $codeArray = array();
        $idArray = array();
        foreach ($gpAll as $i => $row) {
            $idArray[] = $row['privilege_id'];
        }
        $rowArray = $privilegeTable->getAll('code', array(
                array('id', 'in', $idArray),
                array('app', '=', $app),
        ));
        if (empty($rowArray)) {
            return array();
        }
        foreach ($rowArray as $row) {
            if ($row) {
                $codeArray[] = $row['code'];
            }
        }
        return $codeArray;
    }
    
    /**
     * @brief 启用或禁用
     */
    public static function changeUserStatus($id) {
        
        $userTable = new SsoUserModel();
        $row = $userTable->getRow('*', array(
            array('id', '=', $id),
        ));
        $row = array(
            'status' => $row['status'] == 0 ? 1 : 0,
        );
        $userTable->update($row, array(
            array('id', '=', $id),
        ));
    }
    
    /**
     * @brief 初始化用户密码
     */
    public static function initUserPassword($id) {
        $userTable = new SsoUserModel();
        $row = array(
            'passwd' => SsoBackend,
            'modify_pwd' => '0',
        );
        $ret = $userTable->update($row, array(
            array('id', '=', $id),
        ));
        return $ret;
    }
    
    /**
     * @brief 根据user_id，获取组列表
     * @return groupAll
     */
    public static function getGroupByUserId($user_id) {
        
        $guTable = new SsoGUModel();
        $groupTable = new SsoGroupModel();
        
        // 得到指定user的group_id列表
        $guAll = $guTable->getAll('*', array(
            array('user_id', '=', $user_id),
        ), array(
            'group_id' => 'ASC',
        ));
        
        $groupAll = array();
        
        foreach ((array) $guAll as $i => $row) {
            $id = $row['group_id'];
            $row = $groupTable->getRow('*', array(
                array('id', '=', $id),
            ));
            if ($row) {
                $groupAll[] = $row;
            }
        }
        
        return $groupAll;
    }
    
    /**
     * @brief 根据group_id，获取用户列表
     * @return userAll
     */
    public static function getUserByGroupId($group_id) {
        
        $guTable = new SsoGUModel();
        $userTable = new SsoUserModel();
        
        // 得到指定group的user_id列表
        $guAll = $guTable->getAll('*', array(
            array('group_id', '=', $group_id),
        ),array(
            'user_id' => 'ASC',
        ));
        $userAll = array();
        
        foreach ((array) $guAll as $i => $row) {
            $id = $row['user_id'];
            $row = $userTable->getRow('*', array(
                array('id', '=', $id),
            ));
            if ($row) {
                $userAll[] = $row;
            }
        }
        
        return $userAll;
    }

    /**
     * @brief 重新对user分配组
     */
    public static function assignGroupToUser($group_ids, $user_id) {
        
        $guTable = new SsoGUModel();
        
        $guTable->delete(array(
            array('user_id', '=', $user_id),
        ));
        
        foreach ((array) $group_ids as $group_id) {
            $row = array(
                'group_id' => $group_id,
                'user_id' => $user_id,
            );
            $guTable->insert($row);
        }
    }
    
    /**
     * @brief 重新对group分配用户
     */
    public static function assignUserToGroup($user_ids, $group_id) {
        
        $guTable = new SsoGUModel();
        
        $guTable->delete(array(
            array('group_id', '=', $group_id),
        ));
        
        foreach ((array) $user_ids as $user_id) {
            $row = array(
                'group_id' => $group_id,
                'user_id' => $user_id,
            );
            $id = $guTable->insert($row);
        }
    }
    
    /**
     * @brief 为组添加权限
     */
    public static function addPrivilegeToGroup($privilege_ids, $group_id) {
        
        $gpTable = new SsoGPModel();
        
        foreach ((array) $privilege_ids as $privilege_id) {
            $row = array(
                'group_id' => $group_id,
                'privilege_id' => $privilege_id,
            );
            
            $id = $gpTable->insert($row);
        }
    }
    
    /**
     * @brief 删除组权限
     */
    public static function removePrivilegeToGroup($privilege_ids, $group_id) {
        
        $gpTable = new SsoGPModel();
        
        foreach ($privilege_ids as $privilege_id) {
            $gpTable->delete(array(
                array('group_id', '=', $group_id),
                array('privilege_id', '=', $privilege_id),
            ));
        }
    }
    
    /**
     * @brief 删除用户
     */
    public static function deleteUserById($id) {
        
        $userTable = new SsoUserModel();
        $guTable = new SsoGUModel();
        
        $userTable->delete(array(
            array('id', '=', $id),
        ));
        
        // 删除关系表中有关该组的关系
        if ($ret) {
            $guTable->delete(array(
                array('user_id', '=', $id),
            ));
        }
    }
    
    /**
     * @brief 删除组
     */
    public static function deleteGroupById($id) {
        
        $groupTable = new SsoGroupModel();
        $guTable = new SsoGUModel();
        $gpTable = new SsoGPModel();
        
        $ret = $groupTable->delete(array(
            array('id', '=', $id),
        ));
        
        // 删除关系表中有关该组的关系
        if ($ret) {
            $guTable->delete(array(
                array('group_id', '=', $id),
            ));
            $gpTable->delete(array(
                array('group_id', '=', $id),
            ));
        }
    }
    
    /**
     * @brief 添加一个app
     */
    public static function addApp($app, $name, $brief) {
        
        $appTable = new SsoAppModel();
        
        $row = array(
            'app' => $app,
            'name' => $name,
            'brief' => $brief,
            'in_time' => time(),
        );
        $ret = $appTable->insert($row);
        
        return $ret;
    }
}
