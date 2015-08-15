<!DOCTYPE html>
<html>

<?php $this->view->load('widget/head_tpl.php');?>
<style>
    h1{
        font-size:18px;
    }
</style>
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
        <div style="margin-left:15px;">
        <h1><font style="color:orange"><?php echo $this->userInfo['user']['real_name'];?></font> 您好！欢迎来到安心客后台系统</h1>
        <h1>1.请于左侧菜单选择您要的操作事项。</h1>
        <h1>2.如发现左侧事项没有您想要的，请联系管理员添加.</h1>
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->
</body>
</html>
