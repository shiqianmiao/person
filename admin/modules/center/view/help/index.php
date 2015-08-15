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
            <a class="btn btn-block btn-success" data-action-type="add_cat">
                <i class="fa fa-plus"></i> 添加帮助中心大类
            </a>
            
            <div class="box">
                <div class="alert alert-info " style="padding:8px;margin:5px;">
                    <form method="get" action="#">
                        <select class="form-control" style="width:200px;display:inline;" name="type">
                            <?php if (!empty($this->catType)) {?>
                                <?php foreach ($this->catType as $value => $type) {?>
                                    <option <?php if(Request::getGET('type', 1) == $value) { ?>selected<?php }?> value="<?= $value?>"><?= $type?></option>
                                <?php }?>
                            <?php }?>
                        </select>
                        <input type="submit" class="btn btn-success" value="查询" />
                    </form>
                </div>

                <div class="box-body table-responsive">
                    <?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>
                </div><!-- /.box-body -->
            </div>
        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">
G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
    Backend.run();
    Backend.on('add_cat', function (e) {
        e.preventDefault();
        var url = "/center/help/iframeAddCat";
        var dialog = new Dialog({
            title : '添加帮助信息大类',
            width : 500,
            height: 350,
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

    Backend.on('edit_cat', function (e) {
        e.preventDefault();
        var url = "/center/help/iframeEditCat?id="+$(this).data('id');
        var dialog = new Dialog({
            title : '编辑帮助信息大类',
            width : 500,
            height: 350,
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

    Backend.on('del_cat', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $id = $this.data('id');

        var dialog = new Dialog ({
            title : '请仔细核对大类信息',
            width : 300,
            height: 50,
            content : '您确定要删除 "'+$this.data('name')+'" 大类?',
            skin : 'blue',
            ok   : function(e) {
                $this.addClass('disabled');
                $.ajax({
                    url : "/center/help/ajaxDelCat",
                    type : "POST",
                    dataType : "json",
                    data : {
                        "id" : $id
                    },
                    success : function(result) {
                        if (result.errorCode) {
                            //失败
                            Backend.trigger('alert-error', result.msg);
                            $this.removeClass('disabled');
                        } else {
                            //成功
                            Backend.trigger('alert-success', result.msg);
                            setTimeout(function() {
                                Backend.widget('#datagrid-1').ready(function () {
                                    this.load();
                                });
                            }, 2000);
                        }
                    },
                    error : function() {
                        Backend.trigger('alert-error', "提交失败");
                        $this.removeClass('disabled');
                    }
                });
            },
            cancel : function(e) {
            }
        });
    });
});
</script>
</body>
</html>
