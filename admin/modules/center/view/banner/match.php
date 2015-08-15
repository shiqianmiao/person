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

        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">匹配空闲服务者空闲档期</h3>
            </div>
            <div class="box-body">
                <div class="alert alert-info " style="padding:8px;margin:5px;">
                    <form method="get" action="#">
                        开始服务时间：
                        <input type="text" value="<?php echo Request::getGET('start_time', ''); ?>" name="start_time" class="form-control" style="width:200px;display:inline-block" data-widget="datepicker">
                        <select class="form-control" style="width:100px;display: inline;" name="hour_b">
                            <option value="0">请选</option>
                            <?php for ($i = 1; $i <= 24; $i++){?>
                                <option <?php if($i == Request::getGET('hour_b', 0)){?>selected<?php }?> value="<?= $i?>"><?= $i?> 点</option>
                            <?php }?>
                        </select>
                        结束服务时间：
                        <input type="text" value="<?php echo Request::getGET('end_time', ''); ?>" name="end_time" class="form-control" style="width:200px;display:inline-block" data-widget="datepicker">
                        <select class="form-control" style="width:100px;display: inline;" name="hour_e">
                            <option value="0">请选</option>
                            <?php for ($i = 1; $i <= 24; $i++){?>
                                <option <?php if($i == Request::getGET('hour_e', 0)){?>selected<?php }?> value="<?= $i?>"><?= $i?> 点</option>
                            <?php }?>
                        </select>
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
G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
});
</script>
</body>
</html>
