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
                        状态：
                        <select class="form-control" style="width:150px;display:inline;" name="status">
                            <option value="0">选择筛选状态</option>
                            <?php if (!empty($this->statusMap)) {?>
                                <?php foreach ($this->statusMap as $value => $text) {?>
                                    <option <?php if(Request::getGET('status', 0) == $value) { ?>selected<?php }?> value="<?= $value?>"><?= $text?></option>
                                <?php }?>
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
    G.use(['app/backend/js/backend.js'], function (Backend) {
        Backend.run();
    });
</script>
</body>
</html>