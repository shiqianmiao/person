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
            <div class="box-header">
                <h3 class="box-title" style="font-weight: bold;">日志列表</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <form action="/log/" method="get">
                tag:<input name="tag" type="text" value="<?php echo Request::getREQUEST('tag', '');?>"/>
                <input value="查询" type="submit" />
                </form>
            </div>
            <div class="box-body">

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