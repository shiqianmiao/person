<!DOCTYPE html>
<html>
<?php $this->view->load('widget/head_tpl.php');?>

<body class="skin-blue">
<!-- header logo: style can be found in header.less -->
<?php $this->view->load('widget/header_tpl.php');?>

<div class="wrapper row-offcanvas row-offcanvas-left">
    <?php $this->view->load('widget/left_aside_tpl.php');?>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side">
        <!-- Content Header (Page header) -->
        <?php $this->view->load('widget/bread_tpl.php');?>

        <!-- Main content -->
        <div class="box">
            <div class="box-body">
                <!--
                <div class="alert alert-info " style="padding:8px;margin:5px;">
                    <form method="get" action="#">
                        状态：
                        <select class="form-control" style="width:150px;display:inline;" name="status">
                            <option value="0">选择订单状态</option>
                            <?php if (!empty($this->statusMap)) {?>
                                <?php foreach ($this->statusMap as $value => $text) {?>
                                    <option <?php if(Request::getGET('status', 0) == $value) { ?>selected<?php }?> value="<?= $value?>"><?= $text?></option>
                                <?php }?>
                            <?php }?>
                        </select>
                        客户姓名：
                        <input type="text" value="<?php echo Request::getGET('customer_name', ''); ?>" name="customer_name" class="form-control" placeholder="筛选客户姓名" style="width:150px;display:inline-block">
                        订单编号：
                        <input type="text" value="<?php echo Request::getGET('order_num', ''); ?>" name="order_num" class="form-control" placeholder="筛选订单编号" style="width:150px;display:inline-block">
                        育儿嫂姓名：
                        <input type="text" value="<?php echo Request::getGET('worker_name', ''); ?>" name="worker_name" class="form-control" placeholder="筛选育儿嫂" style="width:150px;display:inline-block">
                        <input type="submit" class="btn btn-success" value="查询" />
                    </form>
                </div>
                -->

                <?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>

            </div><!-- /.box-body -->
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">
    G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
        Backend.run();
        Backend.on('check_account', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = "/center/account/checkAccount/?id="+id;
            var dialog = new Dialog({
                title : '结算',
                width : 500,
                height: 400,
                close : function() {
                    Backend.widget('#datagrid-1').ready(function () {
                        this.load();
                    });
                }
            });
            dialog.ready(function () {
                this.open(url);
            });
            window.dialog = dialog;
        });

        Backend.on('edit', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = "/center/account/iframeEdit/?id="+id;
            var dialog = new Dialog({
                title : '编辑账户信息',
                width : 500,
                height: 250,
                close : function() {
                    Backend.widget('#datagrid-1').ready(function () {
                        this.load();
                    });
                }
            });
            dialog.ready(function () {
                this.open(url);
            });
            window.dialog = dialog;
        });

    });
</script>
</body>
</html>