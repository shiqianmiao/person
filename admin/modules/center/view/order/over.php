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
            
            
            <?php if (!empty($this->subInfo) && is_array($this->subInfo)) {?>
            <?php foreach ($this->subInfo as $sub) {?>
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title" style="font-weight: bold;">(<?php echo $sub['start_time'] . ' ~ ' . $sub['over_time'];?>) 应该支付给阿姨的金额</h3>
                </div>
                
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title" style="font-weight: bold;">
                            基本工资：<?php echo $sub['b_wage'] / 100;?>元 &nbsp;&nbsp;
                            工资总计：<?php echo $sub['b_amount'] / 100;?>元 &nbsp;&nbsp;
                            冻结客户：<?php echo $sub['c_frozen'] / 100;?>元 &nbsp;&nbsp;
                            应退客户：<?php echo ($sub['c_frozen'] - $sub['b_wage'] - $sub['plat_amount']) / 100;?>元
                        </h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="2">正常上班</td>
                                <td colspan="2">周末加班</td>
                                <td colspan="2">法定节假日加班</td>
                                <td rowspan="3">
                                    <!--
                                    <button data-action-type="calendar" class="btn btn-info">查看考勤</button>
                                    -->
                                    <br/>
                                </td>
                            </tr>
                            <tr>
                                <td>天数</td>
                                <td>单价</td>
                                <td>天数</td>
                                <td>单价</td>
                                <td>天数</td>
                                <td>单价</td>
                            </tr>
                            <tr>
                                <td><?php echo $sub['day_normal'];?></td>
                                <td><?php echo OrderPriceConfig::BABY_DAY_PRICE;?> 元</td>
                                <td><?php echo $sub['day_week'];?></td>
                                <td><?php echo OrderPriceConfig::BABY_WEEK_DAY_PRICE;?> 元</td>
                                <td><?php echo $sub['day_legal'];?></td>
                                <td><?php echo OrderPriceConfig::BABY_LEGAL_DAY_PRICE;?> 元</td>
                            </tr>
                        </tbody></table>
                    </div><!-- /.box-body -->
                    
                    <!-- 
                    <div class="box-header" style="background-color:green;color:white;">
                        <h3 class="box-title" style="font-weight: bold;">雇主小费：100元</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td rowspan="2" style="width:100px;">雇主小费</td>
                                <td>次数</td>
                                <td rowspan="2" style="color:#66D9FF;font-size:40px;line-height:50px;">
                                    <i class="fa fa-fw fa-bars"></i>
                                </td>
                            </tr>
                            <tr>
                                <td>10</td>
                            </tr>
                        </tbody></table>
                    </div>
                    -->
                    
                    <div class="box-header">
                        <h3 class="box-title" style="font-weight: bold;">安心客奖励：<?php echo $sub['b_reward'] / 100;?>元</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td rowspan="2" style="width:100px;">安心客奖励</td>
                                <td>次数</td>
                                <td rowspan="2" style="color:#66D9FF;font-size:40px;line-height:50px;">
                                    <i class="fa fa-fw fa-bars"></i>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $sub['reward_cn'];?></td>
                            </tr>
                        </tbody></table>
                    </div><!-- /.box-body -->
                    
                    <div class="box-header">
                        <h3 class="box-title" style="font-weight: bold;">扣除罚金：<?php echo $sub['b_fine'] / 100;?>元</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td rowspan="2" style="width:100px;">罚金</td>
                                <td>次数</td>
                                <td rowspan="2" style="color:#66D9FF;font-size:40px;line-height:50px;">
                                    <i class="fa fa-fw fa-bars"></i>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $sub['fine_cn'];?></td>
                            </tr>
                        </tbody></table>
                    </div><!-- /.box-body -->
                    
                </div>
                <div class="box-footer" style="font-weight: bold;">
                    客户状态：
                    <?php if ($sub['confirm']) {?>
                    <font style="color: red;font-weight:bold;font-size:16px;">客户已经确认通过</font>
                    <?php } else {?>
                    <input type="hidden" name="sub_id" value="<?php echo $sub['sub_id'];?>"/>
                    <button data-action-type="confirm" class="btn btn-warning">等待客户确认</button>
                    <?php }?>
                </div><!-- /.box-footer-->
                <div class="box-footer" style="text-align: center;">
                </div><!-- /.box-footer-->
            </div>
            <?php }?>
            <?php } else {?>
            <h1>该订单暂无待客户确认的子订单需要操作！</h1>
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
    Backend.on('calendar', function (e) {
        e.preventDefault();
        var url = "/center/orderList/iframeShowDaily";
        var dialog = new Dialog({
            title : '查看考勤',
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

    Backend.on('confirm', function(e) {
        e.preventDefault();
        var $this = $(this);

        $.ajax({
            url : "/center/orderList/ajaxSetConfirm",
            type : "POST",
            dataType : "json",
            data : {
                "sub_id" : $('input[name="sub_id"]').val()
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
