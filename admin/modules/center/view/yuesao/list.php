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
<style>
    .box .table tr td{
        border:none;
        vertical-align:center;
        padding: 4px;
    }
</style>
        <!-- Main content -->
        <section class="content">
            <?php if (in_array('bc.*', $this->code) || in_array('bc.manager', $this->code)) {?>
            <div class="box" style="margin-bottom: 15px;border-top-width:1px;background-color:#a3cde9;text-indent:3em;padding:5px 0px;">
                输入要签约的月嫂ID：<input type="text" name="parental_id" style="width:200px;"/>
                 &nbsp; &nbsp;
                <button class="btn btn-success" data-action-type="signing">签约</button>
            </div>
            <?php }?>
            
        <?php if (!empty($this->infoList) && is_array($this->infoList)) {?>
        <?php foreach ($this->infoList as $info) {?>
            <div class="box" style="margin-bottom: 5px;border-top-width:1px;">
                <table class="table" style="margin-bottom: 5px;">
                    <tbody>
                        <tr>
                            <td rowspan="3" style="width:150px;text-align:center;">
                                <img class="img-circle" width="80" src="<?php echo $info['face_url'];?>" alt="<?php echo $info['full_name'];?>">
                            </td>
                            <td style="width:600px;">
                                <?php echo $info['full_name'];?> (ID: <font style="color:red;font-weight:bold;"><?php echo $info['id'];?></font>)
                            </td>
                            <td>
                                <!-- <font style="color:red;font-weight:bold;"> &nbsp; &nbsp;8000 / 天</font> -->
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $info['age'];?>岁 &nbsp;家乡<?php echo $info['address'];?> &nbsp; &nbsp;<?php echo $info['id_check'];?> &nbsp; &nbsp;<?php echo $info['reg_time'];?>注册</td>
                            <td rowspan="2">
                                <?php if (in_array('bc.manager', $this->code) || in_array('bc.*', $this->code)) {?>
                                <button class="btn btn-default" data-name="<?php echo $info['full_name'];?>" data-action-type="unsign" data-id="<?php echo $info['id'];?>">解约</button>
                                <button class="btn btn-default" data-action-type="edit" data-id="<?php echo $info['id'];?>">编辑信息</button>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td>经纪人：<?php echo isset($this->manager[$info['manager']]) ? $this->manager[$info['manager']] : '无';?> &nbsp; &nbsp; &nbsp;<?php echo $info['work_age'];?>年从业经验</td>
                        </tr>
                    </tbody>
                </table>
            </div><!-- /.box -->
            <?php }?>
        <?php }?>
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
        var url = "/center/yuesao/iframeShowYuesao?id="+$.trim($('input[name="parental_id"]').val());
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
                    url : "/center/yuesao/ajaxUnsignYuesao",
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
