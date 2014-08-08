<?PHP
//数据列表组件
//抽象类，必须继承并实现fieldFormat()方法

require_once dirname(__FILE__) . '/BaseWidget.class.php';
require_once FRAMEWORK_PATH . '/util/pager/Pager.class.php';

class DataGridWidget extends BaseWidget {
	protected $_col            = array();
	protected $_data           = array();
	protected $_pager          = array();
	protected $_mutilRowAction = array(); // 多行操作
    protected $_callback       = null;
    protected $_callbaclParams = array(0 => null, 1 => null);
	public $url = '';
	public $id = '';

	public function setUrl($url) {
		$this->url = $url;
	}

    public function setFormatCallback($callback, $params = array()) {
        $this->_callback = $callback;
        $i = 2;
        foreach ($params as $params) {
            $this->_callbaclParams[$i++] = $params;
        }
    }

	public function setCol($name, $text, $style = '', $orderMode = false) {
		$this->_col[$name] = array(
			'text'      => $text,
            'style'     => $style,
            'orderMode' => $orderMode,
		);
	}

	public function setData($data) {
		$this->_data = $data ? $data : array();
	}

    /*
    array(
        "text" => "删除",
        "icon" => "icon-trash",
        "action" => "datagrid-multi-row-delete",
        "url"   => "/default/list/ajaxDelete"
    )*/
    public function addMultiRowAction($action) {
        $this->_mutilRowAction[] = $action;
    }

	public function setPager($count, $current, $size, $maxPage = null) {
		$this->_pager = Pager::getPager($count, $current, $size, $maxPage);
	}

	public function getData() {
		$ret = array(
			'cols'     => $this->_col,
			'rows'     => $this->_getFormatRowData(),
			'pager'    => $this->_pager,
			'url'      => $this->url,
			'multiRowAction' => $this->_mutilRowAction,
		);
        if (!count($this->_pager)) {
            $ret['pager'] = Null;
        }
        return $ret;
	}

    public function toHTML($id = '') {
        if ($id) {
            $this->id = $id;
        }
        $html = '
<div class="ui-datagrid" data-show-fields=\''.json_encode(array_keys($this->_col)).'\' data-widget="datagrid"'.(!empty($this->id) ? " id='".$this->id."'" : "").' data-url="'.$this->url.'">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>';
        if (count($this->_mutilRowAction)) {
            $html .= '<th><input data-action-type="datagrid-checkall" type="checkbox"></th>';
        }
        foreach($this->_col as $name => $col) {
            if (empty($col)) {
                trigger_error('this col not found:'.$name);
            }
            $html .= '<th data-field-name="'.$name.'" style="'.$col['style'].'"';
            if ($col['orderMode']) {
                $html .= ' data-action-type="datagrid-sort-'.$col['orderMode'].'"';
            }
            $html .= '>'.$col['text'];
            if ($col['orderMode']) {
                $html .= "<a href='#' class='pull-right' data-action-type='datagrid-sort-".$col['orderMode']."'><i class='icon-sort-".$col['orderMode']."'></i></a>";
            }
            $html .= '</th>';
        }
        $html .= '
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="btn-group pull-left">
        
    </div>
    <div class="pagination pull-right">
        
    </div>
</div>
';
        return $html;
    }

	protected function _getFormatRowData() {
		foreach ((array)$this->_data as $key => $value) {
			foreach ($this->_col as $name => $col) {
                $this->_callbaclParams[0] = $name;
                $this->_callbaclParams[1] = $value;
                $this->_data[$key][$name] = call_user_func_array($this->_callback, $this->_callbaclParams);
			}
		}
		return $this->_data;
	}
}
