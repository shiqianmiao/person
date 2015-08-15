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
                <div class="alert alert-info " style="padding:8px;margin:5px;">
                    <form method="get" action="#">
                        客户姓名：
                        <input type="text" value="<?php echo Request::getGET('real_name', ''); ?>" name="real_name" class="form-control" placeholder="筛选客户姓名" style="width:150px;display:inline-block">
                        客户手机：
                        <input type="text" value="<?php echo Request::getGET('mobile', ''); ?>" name="mobile" class="form-control" placeholder="筛选客户手机" style="width:150px;display:inline-block">
                        <input type="submit" class="btn btn-success" value="查询" />
                    </form>
                </div>

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
        Backend.on('edit', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = "/center/customer/iframeEdit/?id="+id;
            var dialog = new Dialog({
                title : '编辑客户信息',
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