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
        <div style="width:900px;margin-left:50px;">
        
            <?php if (!empty($this->map)) {?>
                <div class="stepsMain">
                    <ul>
                    <?php foreach ($this->map['map'] as $key => $m) {?>
                        <li <?php if ($m == 1){?>class="active"<?php } ?>><span><em><?php echo $key + 1;?></em></span><p><?php echo $this->map['text'][$key];?></p></li>
                    <?php }?>
                    </ul>
                    <span class="stepsDown" style="width: 632px;"></span>
                </div>
            <?php }?>
            
            <div class="callout callout-info">
                <h4><?php echo $this->order['customer_name'];?> &nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i>  &nbsp;&nbsp;&nbsp;订单号：<?php echo $this->order['order_num'];?></h4>
                <p>服务类型：<?php echo $this->order['service_type'];?></p>
                <p>服务时间：<?php echo $this->order['service_time_b'];?> <i class="fa fa-fw fa-arrows-h"></i> <?php echo $this->order['service_time_e'];?></p>
            </div>
            
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">匹配育儿嫂</h3>
                </div>
                <div class="box-body">
                    <div class="input-group input-group-sm" style="width:300px;">
                        <input name="parental_id" type="text" placeholder="输入要匹配的育儿嫂ID" class="form-control">
                        <span class="input-group-btn">
                            <input type="hidden" name="order_id" value="<?php echo $this->order['id'];?>"/>
                            <button data-action-type="match" class="btn btn-success btn-flat" type="button">添加匹配</button>
                        </span>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    每个订单总共最多只能匹配<font style="color:red;"> <?php echo GlobalConfig::MATCH_AMOUNT_BABY;?> 
                    </font>个育儿嫂、当前已经匹配了<font style="color:red;"> <?php echo !empty($this->parental) ? count($this->parental) : 0;?> </font>个！
                </div><!-- /.box-footer-->
            </div>
            
            <?php if (!empty($this->parental) && is_array($this->parental)) {?>
            <?php foreach ($this->parental as $info) {?>
            <div class="box box-success">
                <div class="box-header">
                    <table class="table" style="margin-bottom: 5px;">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="width:150px;text-align:center;">
                                    <img class="img-circle" width="80" src="<?php echo $info['face_url'];?>" alt="<?php echo $info['full_name'];?>">
                                </td>
                                <td style="width:170px;"><?php echo $info['full_name']?> (id: <?php echo $info['id'];?>)</td>
                                <td style="width:50px;"><?php echo $info['age'];?>岁</td>
                                <td style="width:200px;"><?php echo $info['address'];?></td>
                                <td><?php echo $info['mobile'];?></td>
                            </tr>
                            <tr>
                                <td colspan="4">经济人：<?php echo isset($this->manager[$info['manager']]['Fullname']) ? $this->manager[$info['manager']]['Fullname'] : '无'?>
                                &nbsp;&nbsp;&nbsp;
                                注册时间：<?php echo $info['reg_time'];?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php }?>
            <?php }?>
            
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">
G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
    Backend.run();
    Backend.on('match', function (e) {
        e.preventDefault();
        var orderId = $('input[name="order_id"]').val();
        var url = "/center/order/iframeMatch?order_id="+orderId+"&id="+$.trim($('input[name="parental_id"]').val());
        var dialog = new Dialog({
            title : '仔细核对育儿嫂信息',
            width : 500,
            height: 250,
            close : function() {
                location.reload();
            }
        });
        dialog.ready(function () {
            this.open(url);
        });
        window.dialog = dialog;
    });

    /*Backend.on('edit_cat', function (e) {
        e.preventDefault();
        var url = "/center/help/iframeEditCat?id="+$(this).data('id');
        var dialog = new Dialog({
            title : '编辑帮助信息大类',
            width : 500,
            height: 250,
            close : function() {
                location.reload();
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
    });*/
});
</script>
</body>
</html>
