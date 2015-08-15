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
            <?php if (in_array('bc.*', $this->code) || in_array('bc.manager', $this->code)) {?>
            <div class="box" style="margin-bottom: 15px;border-top-width:1px;background-color:#a3cde9;text-indent:3em;padding:5px 0px;">
                输入要签约的育儿嫂ID：<input type="text" name="parental_id" style="width:200px;"/>
                 &nbsp; &nbsp;
                <button class="btn btn-success" data-action-type="signing">签约</button>
            </div>
            <?php }?>
            
            <div class="box">
                <div class="box-body">
                    <div class="alert alert-info " style="padding:8px;margin:5px;">
                        <form method="get" action="#">
                            状态：
                            <select class="form-control" style="width:150px;display:inline;" name="status">
                                <?php if (!empty($this->statusMap)) {?>
                                    <?php foreach ($this->statusMap as $value => $text) {?>
                                        <option <?php if(Request::getGET('status', WorkerConfig::STATUS_SIGN) == $value) { ?>selected<?php }?> value="<?= $value?>"><?= $text?></option>
                                    <?php }?>
                                <?php }?>
                            </select>
                            姓名：
                            <input type="text" value="<?php echo Request::getGET('real_name', ''); ?>" name="real_name" class="form-control" placeholder="筛选姓名" style="width:150px;display:inline-block">
                            手机号号：
                            <input type="text" value="<?php echo Request::getGET('mobile', ''); ?>" name="mobile" class="form-control" placeholder="筛选手机号" style="width:150px;display:inline-block">
                            <input type="submit" class="btn btn-success" value="查询" />
                        </form>
                    </div>

                    <?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>
                </div>
            </div><!-- /.box -->
        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script>
G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
    Backend.run();
    Backend.on('signing', function (e) {
        e.preventDefault();
        var url = "/center/parental/iframeShowParental?id="+$.trim($('input[name="parental_id"]').val());
        var dialog = new Dialog({
            title : '请认真核对签约人详情',
            width : 550,
            height: 300,
            close : function() {
                location.reload();
            }
        });
        dialog.ready(function () {
            this.open(url);
        });
        window.dialog = dialog;
    });

    Backend.on('unsign', function(e) {
        e.preventDefault();
        var $this = $(this);

        var dialog = new Dialog ({
            title : '请仔细核对是否确认解约此人!',
            width : 300,
            height: 50,
            content : '您确定要将 "'+$this.data('name')+'" 解约吗？',
            skin : 'blue',
            ok   : function(e) {
                $this.addClass('disabled');
                $.ajax({
                    url : "/center/parental/ajaxUnsignParental",
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
                            setTimeout(function() {
                                location.reload();
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

    Backend.on('edit', function (e) {
        e.preventDefault();
        var url = "/center/parental/iframeEdit?id="+$(this).data('id');
        var dialog = new Dialog({
            title : '编辑服务者头像和简介',
            width : 550,
            height: 300,
            close : function() {
                location.reload();
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
