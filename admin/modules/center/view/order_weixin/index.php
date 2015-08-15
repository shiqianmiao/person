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
                            <option value="0">选择订单状态</option>
                            <?php if (!empty($this->statusMap)) {?>
                                <?php foreach ($this->statusMap as $value => $text) {?>
                                    <option <?php if(Request::getGET('status', 0) == $value) { ?>selected<?php }?> value="<?= $value?>"><?= $text?></option>
                                <?php }?>
                            <?php }?>
                        </select>
                        客户姓名：
                        <input type="text" value="<?php echo Request::getGET('customer_name', ''); ?>" name="customer_name" class="form-control" placeholder="筛选客户姓名" style="width:150px;display:inline-block">
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
    G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
        Backend.run();

        //删除微信订单
        Backend.on('del', function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.addClass('disabled');
            $.ajax({
                url : "/center/orderWeixin/ajaxDel/",
                type : "POST",
                dataType : "json",
                data : {
                    "id" : $this.data('id')
                },
                success : function(result) {
                    if (result.errorCode) {
                        //失败
                        Backend.trigger('alert-error', result.msg);
                        $this.removeClass('disabled');
                    } else {
                        //成功
                        Backend.trigger('alert-success', result.msg);
                        window.setTimeout(function() {
                            Backend.widget('#datagrid-1').ready(function () {
                                this.load();
                            });
                        }, 2000);
                    }
                },
                error : function() {
                    Backend.trigger('alert-error', "内容提交失败!");
                    $this.removeClass('disabled');
                }
            });
        });
    });
</script>
</body>
</html>