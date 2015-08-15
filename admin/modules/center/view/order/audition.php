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
            
            <?php if (!empty($this->parental) && is_array($this->parental)) {?>
            <?php foreach ($this->parental as $key => $info) {?>
            <div class="box box-success">
                <div class="box-header">
                    <table class="table" style="margin-bottom: 5px;">
                        <tbody>
                            <tr>
                                <td rowspan="3" style="width:150px;text-align:center;">
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
                <div class="box-body">
                    <table style="margin-left:50px;">
                        <tr>
                            <td style="width:500px;padding:8px 10px 8px 0px;">
                                <div class="input-group">
                                    <span class="input-group-addon">面试时间：</span>
                                    <input name="audition_time_<?php echo $key;?>" value="<?php echo isset($info['audition_time']) ? $info['audition_time'] : '';?>" type="text" data-start-date="<?php echo date('Y-m-d'); ?>" class="form-control" style="width:280px;" data-widget="datepicker" placeholder="请选择面试时间">
                                    <select class="form-control" style="width:100px;" name="audition_hour_<?php echo $key;?>">
                                        <option value="0">请选</option>
                                        <?php for ($i = 1; $i <= 24; $i++){?>
                                            <option <?php if($i == $info['audition_hour']){?>selected<?php }?> value="<?= $i?>"><?= $i?> 点</option>
                                        <?php }?>
                                    </select>
                                    <input type="hidden"/>
                                    <span class="checkinfo"><i class="i-infom"></i>
                                        <span id="service_time_b_tip"></span>
                                    </span>
                                </div>
                            </td>
                            <td rowspan="2">
                                <input type="hidden" name="audition_id_<?php echo $key;?>" value="<?php echo $info['audition_id'];?>"/>
                                <?php if ($info['audition_status'] < BcEnumerateConfig::AUDITION_STATUS_UNPASS) {?>
                                    <button class="btn btn-success btn-flat" data-id="audition_id_<?php echo $key;?>" data-hour="audition_hour_<?php echo $key;?>" data-time="audition_time_<?php echo $key;?>" data-address="audition_address_<?php echo $key;?>" data-action-type="submit">
                                        <?php echo $info['audition_status'] == BcEnumerateConfig::AUDITION_STATUS_DEFAULT ? '提交' : '修改'?>面试信息
                                    </button>
                                <?php }?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style="width:500px;padding:8px 10px 8px 0px;">
                                <div class="input-group">
                                    <span class="input-group-addon">面试地点：</span>
                                    <input name="audition_address_<?php echo $key;?>" value="<?php echo isset($info['audition_address']) ? $info['audition_address'] : '';?>" type="text" class="form-control" placeholder="填写面试的具体地点">
                                </div>
                            </td>
                        </tr>
                        
                        <?php if ($info['audition_status'] >= BcEnumerateConfig::AUDITION_STATUS_DOING) {?>
                        <tr>
                            <td style="width:500px;padding:8px 10px 8px 0px;">
                                <div class="input-group">
                                    <span class="input-group-addon">面试结果：</span>
                                    <select name="result_<?php echo $key;?>" class="form-control">
                                        <option>选择面试结果</option>
                                        <?php if (!empty(BcEnumerateConfig::$AUDITION_RESULT) && is_array(BcEnumerateConfig::$AUDITION_RESULT)) {?>
                                        <?php foreach (BcEnumerateConfig::$AUDITION_RESULT as $value => $text) {?>
                                        <option <?php if ($value == $info['audition_status']){?>selected<?php }?> value="<?php echo $value;?>"><?php echo $text;?></option>
                                        <?php }?>
                                        <?php }?>
                                    </select>
                                </div>
                            </td>

                            <td rowspan="2">
                                <button class="btn btn-info btn-flat" data-id="audition_id_<?php echo $key;?>" data-result="result_<?php echo $key;?>" data-quote="quote_<?php echo $key;?>" data-action-type="sub_result">
                                <?php echo $info['audition_status'] == BcEnumerateConfig::AUDITION_STATUS_DOING ? '提交' : '修改';?>面试结果
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:8px 10px 8px 0px;">
                                <div class="input-group">
                                    <span class="input-group-addon">订单单价：</span>
                                    <input name="quote_<?php echo $key;?>" value="<?php echo isset($info['quote']) ? $info['quote'] : '';?>" type="text" class="form-control" style="width:250px;" placeholder="填写该订单每月的服务价格">
                                    <span style="line-height: 35px;">&nbsp;&nbsp;元 / 26天&nbsp;&nbsp;</span>
                                    <div style="color:orangered;"><i class="fa fa-fw fa-warning"></i>&nbsp;<font style="color:red;">注意这里勿必填写该订单每月的报价!</font></div>
                                </div>
                            </td>
                        </tr>
                        <?php }?>
                    </table>
                </div><!-- /.box-body -->
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
G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
    Backend.on('sub_result', function (e) {
        e.preventDefault();
        var $this = $(this);
        var resultName = $this.data('result');
        var quoteName = $this.data('quote');
        var id = $this.data('id');
        $this.addClass('disabled');
        $.ajax({
            url : "/center/order/ajaxSubAuditionResult/",
            type : "POST",
            dataType : "json",
            data : {
                "result" : $('select[name="'+resultName+'"]').val(),
                "audition_id" : $('input[name="'+id+'"]').val(),
                "quote" : $('input[name="'+quoteName+'"]').val()
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
                        if (result.is_pass) {
                            location.href="/center/orderList/detail/?id=<?php echo $_GET['order_id'];?>";
                        } else {
                            location.reload();
                        }
                    }, 2000);
                }
            },
            error : function() {
                Backend.trigger('alert-error', "内容提交失败!");
                $this.removeClass('disabled');
            }
        });
    });

    Backend.on('submit', function (e) {
        e.preventDefault();
        var $this = $(this);
        var timeName = $this.data('time');
        var hourName = $this.data('hour');
        var address  = $this.data('address');
        var id = $this.data('id');
        $this.addClass('disabled');
        $.ajax({
            url : "/center/order/ajaxSubmitAudition/",
            type : "POST",
            dataType : "json",
            data : {
                "audition_time" : $('input[name="'+timeName+'"]').val(),
                "audition_hour" : $('select[name="'+hourName+'"]').val(),
                "audition_address" : $('input[name="'+address+'"]').val(),
                "audition_id" : $('input[name="'+id+'"]').val()
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
                        location.reload();
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
