<?PHP

// 菜单管理类

class Menu {
    protected $_data = array();
    public $defaultMenuIcon = "icon-play-circle";

    // $permissions array 权限数据
    public function __construct($permissions = array(), $channels=array()) {
        $params = Bootstrap::getRouteParams();
        $rquestUri = rtrim($_SERVER['REQUEST_URI'], '/');
        $menuData = array();
        foreach ($channels as $channel) {
            $menuDataRow = array();
            /*if ($channel['code'] == 'default') {
                continue;
            }*/
            $file = $channel['dir'] . '/config/menu.inc.php';
            if (!is_file($file)) {
                trigger_error('菜单文件不存在', E_USER_ERROR);
            }
            $menuDataRow['text'] = $channel['text'];
            $menuDataRow['menu'] = include $file;
            $menuDataRow['old'] = !empty($channel['old']) ? true : false;
            $menuData[] = $menuDataRow;
            if (!is_array($menuData) || count($menuData) == 0) {
                trigger_error('菜单数据不正常', E_USER_ERROR);
            }
        }

        $activeData = array();
        //$codeData = array();
        $newData = array();

        foreach($menuData as $key => $val) {
            if (!is_array($val['menu'])) {
                continue;
            }
            if (!empty($val['code']) && !isset($permissions[$val['code']])) {
                continue;
            }
            if (!isset($val['icon'])) {
                $val['icon'] = $this->defaultMenuIcon;
            }
            foreach ($val['menu'] as $k => $v) {
                if ($val['old']) {
                    $v['url'] = 'http://bc.corp.273.cn' . $v['url'];
                    $v['target'] = '_blank';
                }
                if (!empty($v['code']) && !isset($permissions[$v['code']])) {
                    unset($val['menu'][$k]);
                    continue;
                }
                if (!$activeData && rtrim(preg_replace('/\?.*$/', "", $rquestUri), '/') == rtrim($v['url'], '/')) {
                    $activeData = array($key, $k);
                    $val['active'] = true;
                    $v['active']   = true;
                }

                //$v['parent'] = $key;
                //$codeData[$k]     = $v;
                $val['menu'][$k]  = $v;
            }
            if (count($val['menu']) > 0) {
                //$val['parent'] = '';
                $newData[$key]  = $val;
                //$codeData[$key] = $val;
            }
        }
        
        $this->_data = array(
            'menu'   => $newData,
            'active' => $activeData,
            //'code'   => $codeData,
        );
    }

    public function getData() {
        return $this->_data;
    }

    public function setActiveName($name) {
        $activeData = array();
        $newData = array();

        foreach($this->_data['menu'] as $key => $val) {
            if (!is_array($val['menu'])) {
                continue;
            }
            foreach ($val['menu'] as $k => $v) {
                if (!$activeData && !empty($v['name']) && $name == $v['name']) {
                    $activeData = array($key, $k);
                    $val['active'] = true;
                    $v['active']   = true;
                }

                $val['menu'][$k]  = $v;
            }
            if (count($val['menu']) > 0) {
                $newData[$key]  = $val;
            }
        }
        
        $this->_data = array(
            'menu'   => $newData,
            'active' => $activeData,
        );
    }

}
