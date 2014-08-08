<?php

require_once dirname(__FILE__) . '/include/SsoBasePage.class.php';

/**
 * @brief 权限管理
 * @author aozhongxu
 */

class PrivilegePage extends SsoBasePage {
    
    public function listAction() {
        
        $appTable = new SsoAppModel();
        $allApp = $appTable->getAll('*', '', array(
            'app' => 'ASC',
        ));
        $this->render(array(
            'allApp' => $allApp,
        ), 'privilege_list.php');
    }
    
    public function ajaxAddPrivilegeToGroupAction() {
        
        $group_id = RequestUtil::getPOST('group_id');
        $privilege_ids = json_decode(RequestUtil::getPOST('privilege_ids'));
        
        SsoData::addPrivilegeToGroup($privilege_ids, $group_id);
        
        $this->render(array());
        
    }
    
    public function ajaxRemovePrivilegeToGroupAction() {
        
        $group_id = RequestUtil::getPOST('group_id');
        $privilege_ids = json_decode(RequestUtil::getPOST('privilege_ids'));
        
        SsoData::removePrivilegeToGroup($privilege_ids, $group_id);
        
        $this->render(array());
    }
    
    public function ajaxGetPrivilegeByGroupAction() {
        
        $privilegeTable = new SsoPrivilegeModel();
        $gpTable = new SsoGPModel();
        
        $group_id = RequestUtil::getPOST('id');
        $app = RequestUtil::getPOST('app');
        
        // 获取全部权限
        $all = $privilegeTable->getAll('*', array(
            array('app', '=', $app),
        ), array(
            'code' => 'ASC',
        ));
        $count = count($all);
        
        // 构造所有权限的trie
        $this->trie = array();
        foreach ($all as $i => $row) {
            $this->insertNode($row['code'], $row['id'], $row['name']);
        }
        $trieHtml = $this->view->fetch('widget/privilege_trie.php', array(
            'app' => $app,
            'trieArray' => $this->trie,
        ));
        
        // 获取属于group_id的所有权限
        $privilege_ids = $gpTable->getAll('privilege_id', array(
            array('group_id', '=', $group_id),
        ), array(
            'privilege_id' => 'ASC',
        ));
        
        $where = array();
        for ($i = 0; isset($privilege_ids[$i]); $i ++) {
            $id = $privilege_ids[$i]['privilege_id'];
            $where[] = array('id', '=', $id);
        }
        $all = array();
        if (!empty($where)) {
            $all = $privilegeTable->getAll('*', array(
                array('app', '=', $app),
                array('OR' => $where),
            ));
        }
        $count2 = count($all);
        
        // 构造属于group_id的权限
        $this->trie = array();
        foreach ((array) $all as $i => $row) {
            $this->insertNode($row['code'], $row['id'], $row['name']);
        }
        $trieHtml2 = $this->view->fetch('widget/privilege_trie.php', array(
            'app' => $app,
            'trieArray' => $this->trie,
        ));
        
        $this->render(array(
            'trieHtml' => $trieHtml,
            'count' => $count,
            'trieHtml2' => $trieHtml2,
            'count2' => $count2,
        ));
    }
    
    public function ajaxListAction() {
        
        $privilegeTable = new SsoPrivilegeModel();
        
        $app = RequestUtil::getPOST('app');
        
        $all = $privilegeTable->getAll('*', array(
            array('app', '=', $app),
        ), array(
            'code' => 'ASC',
        ));
        $count = count($all);
        $this->trie = array();
        foreach ((array) $all as $i => $row) {
            $this->insertNode($row['code'], $row['id'], $row['name']);
        }
        $trieHtml = $this->view->fetch('widget/privilege_trie.php', array(
            'app' => $app,
            'trieArray' => $this->trie,
        ));
        $this->render(array(
            'trieHtml' => $trieHtml,
            'count' => $count,
        ));
    }
    
    private $trie = array();

    private function insertNode($code, $id, $name) {
        
        $arr = explode('.', $code);
        $n = count($arr);
        
        // 初始化
        $prr = & $this->trie;
        $key = $arr[0];
        
        // 生成路径
        for ($i = 0; $i < $n; $i ++) {
            if ($i > 1) {
                break;
            }
            $k = $key . '.*';
            $prr = & $prr[$k];
            $key = $key . '.' . $arr[$i + 1];
        }
        
        // 生成权限
        $prr[$code]['id'] = $id;
        $prr[$code]['name'] = $name;
    }
    
    public function addAction() {
        
        $appTable = new SsoAppModel();
        $allApp = $appTable->getAll('*', '', array(
            'app' => 'ASC',
        ));
        $this->render(array(
            'allApp' => $allApp,
        ), 'privilege_add.php');
    }
    
    public function ajaxAddAction() {
        
        $privilegeTable = new SsoPrivilegeModel();
        $ret = $privilegeTable->insert(array(
            'code' => RequestUtil::getPOST('code'),
            'app' => RequestUtil::getPOST('app'),
            'name' => RequestUtil::getPOST('name'),
            'brief' => RequestUtil::getPOST('brief'),
            'url' => RequestUtil::getPOST('url'),
            'in_time' => time(),
        ));
        $this->render(array(
            'errorCode' => empty($ret) ? '1' : '0',
        ));
    }
    
    public function ajaxParseUrlAction() {
        
        $url = RequestUtil::getPOST('url', '');
        
        // 加上http://
        if (strpos($url, 'http://') === false) {
            $url = 'http://' . $url;
        }
        
        // 判断url合法，以及是否在corp.273.cn域下
        if ($url == 'http://'
            || !preg_match('/^(http):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)
            || strpos($url, '.corp.273.cn/') === false) {
            $this->render(array(
                'errorCode' => '1',
                'msg' => '&nbsp;url不合法',
            ));
        }
        
        // 分割url，提取关键词，并删除无用的关键词
        $arr = split('\.|\/', $url);
        foreach ($arr as $i => $str) {
            if (in_array($str, array('', 'http:', 'www', 'corp', '273'))) {
                unset($arr[$i]);
            }
        }
        
        // 获取app
        $arr = implode('.', $arr);
        $arr = explode('.cn.', $arr);
        $app = $arr[0];
        $code = $arr[1];
        
        // render
        if (empty($code)) {
            $this->render(array(
                'errorCode' => '1',
                'msg' => '&nbsp;根权限已添加',
            ));
        }
        $field = array(
            'code' => $code,
            'app' => $app,
        );
        $this->render(array(
            'errorCode' => '0',
            'field' => $field,
        ));
    }

    public function ajaxDeleteAction() {
        
        // 获取一组id
        $ids = json_decode(RequestUtil::getPOST('ids'));
        $app = RequestUtil::getPOST('app');
        
        // 构造where条件
        $where1 = $where2 = array();
        foreach ($ids as $id) {
            $where1[] = array('id', '=', $id);
            $where2[] = array('privilege_id', '=', $id);
        }
        
        $privilegeTable = new SsoPrivilegeModel();
        $gpTable = new SsoGPModel();
        
        // 删除权限
        $ret = $privilegeTable->delete(array(
            array('OR' => $where),
        ));
        
        // 如果权限删除成功，那么删除GP关系
        if ($ret) {
            $gpTable->delete(array(
                array('OR' => $where),
            ));
        }
        
        $this->render(array());
    }

}