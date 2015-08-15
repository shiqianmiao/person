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
                    <h3 class="box-title">提交合同信息</h3>
                </div>
                <div class="box-body">
                    <table class="table" style="width:500px;border:none;">
                        <tbody>
                            <tr>
                                <td style="width:100px;text-align:center;border:none;line-height:35px;">
                                    合同状态：
                                </td>
                                <td style="border:none;">
                                    <select class="form-control" name="contract">
                                        <option value="0">请选择合同状态</option>
                                        <?php if (!empty(BcEnumerateConfig::$CONTRACT_STATUS)) {?>
                                        <?php foreach (BcEnumerateConfig::$CONTRACT_STATUS as $value => $status) {?>
                                        <option <?php if ($value == $this->contractStatus){?>selected<?php }?> value="<?php echo $value;?>"><?php echo $status;?></option>
                                        <?php }?>
                                        <?php }?>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="width:100px;text-align:center;border:none;line-height:35px;">
                                    定金状态：
                                </td>
                                <td style="border:none;">
                                    <select class="form-control" name="deposit">
                                        <option value="0">请选择定金状态</option>
                                        <?php if (!empty(BcEnumerateConfig::$ORDER_DEPOSIT_STATUS)) {?>
                                        <?php foreach (BcEnumerateConfig::$ORDER_DEPOSIT_STATUS as $value => $status) {?>
                                        <option <?php if($value == $this->order['deposit']){?>selected<?php }?> value="<?php echo $value;?>"><?php echo $status;?></option>
                                        <?php }?>
                                        <?php }?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;">
                                    <input type="hidden" name="order_id" value="<?php echo $this->order['id'];?>"/>
                                    <button data-action-type="submit" class="btn btn-success">保存</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                
                <div class="box-footer">
                    <?php if (!empty($this->contractInfo) && is_array($this->contractInfo)) {?>
                    <?php foreach ($this->contractInfo as $c) {?>
                    <div style="font-weight:bold;font-size:18px;">在 <font style="color:green;"><?php echo $c['create_time'];?> </font>被修改状态为：<font style="color:green;"><?php echo $c['status_text'];?></font></div>
                    <?php }?>
                    <?php }?>
                </div><!-- /.box-footer-->
            </div>
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">
G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
    
    Backend.on('submit', function(e) {
        e.preventDefault();
        var $this = $(this);

        $this.addClass('disabled');
        $.ajax({
            url : "/center/orderList/ajaxSubContract",
            type : "POST",
            dataType : "json",
            data : {
                "order_id" : $('input[name="order_id"]').val(),
                "contract" : $('select[name="contract"]').val(),
                "deposit"  : $('select[name="deposit"]').val(),
                "quote"    : $('input[name="quote"]').val()
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
    });
});
</script>
</body>
</html>
