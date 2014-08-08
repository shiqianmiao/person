<?php
require_once BACKEND . '/widget/DataGridWidget.class.php';

class ListPage extends BackendPage {
    private $datagrid;

    public function defaultAction() {
        $this->_initDataGrid();
        $this->render(array(
            'datagrid' => $this->dataGrid,
        ), 'list.php');
    }

    public function ajaxGetDataAction() {
        $this->_initDataGrid();
        $page = empty($_GET['page']) ? 0 : intval($_GET['page']);
        $this->dataGrid->setPager(100, $page, 10);
        $this->render(array(
            "data" => $this->dataGrid->getData()
        ));
    }

    public function ajaxDeleteAction() {
        if (!empty($_GET['ids'])) {
            $ids = json_decode(urldecode($_GET['ids']));
            $this->render(array(
                "ids" => $ids
            ));
        } else if (!empty($_GET['id'])) {
            $id = json_decode(urldecode($_GET['id']));
            $this->render(array(
                "id"  => $id
            ));
        }
    }

    private function _initDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl("/default/list/ajaxGetData");

        $dataGrid->setCol("title1", "字段1", "", 'able');
        $dataGrid->setCol("title2", "字段2", "", 'asc');
        $dataGrid->setCol("date_display", "时间", 'width:200px;', 'desc');
        $dataGrid->setCol('action', "操作", 'width:200px;');

        $dataGrid->addMultiRowAction(array(
            "text"   => "删除",
            "icon"   => "icon-trash",
            "action" => "datagrid-multi-row-delete",
            "url"    => "/default/list/ajaxDelete"
        ));

        $dataGrid->addMultiRowAction(array(
            "text"   => "审核通过",
            "icon"   => "icon-ok",
            "action" => "datagrid-multi-row-validate",
            "url"    => "/default/list/ajaxValidate"
        ));

        $dataGrid->setData(array(
            array(
                "id"    => 1,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-01'
            ),
            array(
                "id"    => 2,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-02'
            ),
            array(
                "id"    => 3,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-03'
            ),
            array(
                "id"    => 4,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-04'
            ),
            array(
                "id"    => 5  ,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-05'
            ),
            array(
                "id"    => 6  ,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-06'
            ),
            array(
                "id"    => 7  ,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-07'
            ),
            array(
                "id"    => 8  ,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-08'
            ),
            array(
                "id"    => 9  ,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-09'
            ),
            array(
                "id"    => 10  ,
                "title1"=> rand(0, 100),
                "title2"=> rand(0, 1),
                "title3"=> rand(100, 1000),
                "date"  => '2012-01-10'
            ),
        ));

        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));

        $this->dataGrid = $dataGrid;
    }

    public function fieldFormat ($name, $row) {
        if ($name == 'date_display') {
            return date('Y-m-d H:i:s', $row['date']);
        }

        if ($name == 'action') {
            return '
            <a href="/default/list/ajaxDelete" data-action-type="datagrid-row-delete">
                <i class="splashy-error_x"></i>删除
            </a>
            <a href="/default/list/ajaxEdit" data-action-type="datagrid-row-edit">
                <i class="splashy-pencil_small"></i>编辑
            </a>
            <a href="/default/list/ajaxValidate" data-action-type="datagrid-row-validate">
                <i class="splashy-check"></i>审核通过
            </a>';
        }

        if ($name == 'checkall') {
            return '<input data-action-type="datagrid-row-check" type="checkbox">';
        }

        return $row[$name];
    }
}

