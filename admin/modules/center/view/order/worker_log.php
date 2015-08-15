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
                        <input type="hidden" name="order_id" value="<?php echo Request::getGET('order_id', '');?>"/>
                        <input type="hidden" name="order_type" value="<?php echo Request::getGET('order_type', '');?>"/>
                        开始时间：
                        <input type="text" value="<?php echo Request::getGET('start_time', ''); ?>" name="start_time" class="form-control" placeholder="记录日志开始时间" style="width:150px;display:inline-block" data-widget="datepicker">
                        结束时间：
                        <input type="text" value="<?php echo Request::getGET('end_time', ''); ?>" name="end_time" class="form-control" placeholder="记录日志结束时间" style="width:150px;display:inline-block" data-widget="datepicker">
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
    G.use(['app/backend/js/backend.js'], function (Backend) {
        Backend.run();
    });
</script>
</body>
</html>