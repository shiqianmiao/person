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
        <section class="content">
            <div class="box" style="width:400px; padding:10px;">
                <div class="box-header">
                    <h3 class="box-title" style="font-weight: bold;">标题：<?php echo $this->artInfo['title'];?></h3>
                </div><!-- /.box-header -->
                <div>
                    <?php echo $this->artInfo['cont'];?>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->
</body>
</html>
