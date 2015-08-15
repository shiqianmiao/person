<!DOCTYPE html>
<html>
<?php $this->view->load('widget/head_tpl.php');?>

<style>
    .input-group-addon{
        width:120px;
        text-align:right;
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
        <div style="width:900px;margin-left:50px;">
            <div class="stepsMain">
                <ul>
                    <li class="active"><span><em>1</em></span><p>需求提交</p></li>
                    <li><span><em>2</em></span><p>匹配服务者</p></li>
                    <li><span><em>3</em></span><p>预约面试</p></li>
                    <li><span><em>4</em></span><p>签合同&amp;余款</p></li>
                    <li><span><em>5</em></span><p>服务中</p></li>
                    <li><span><em>6</em></span><p>订单完成</p></li>
                </ul>
                <span class="stepsDown" style="width: 632px;"></span>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title" style="font-weight: bold;"><?php echo $this->isEdit ? '修改' : '添加';?>订单</h3>
                </div>
                <div class="box-body">
                    <form id="form">
                        <table class="table" style="border:none;">
                            <tbody>
                            <tr>
                                <td style="border: none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>订单联系人：</span>
                                        <input type="text" class="form-control" placeholder="务必填写客户真实姓名" style="width:300px;" <?php echo $this->form->getField('customer_name');?>>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="customer_name_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>联系方式：</span>
                                        <input type="text" style="width:300px;" class="form-control" <?php echo $this->form->getField('mobile');?> placeholder="务必填写客户的真实手机号码">
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="mobile_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>宝宝生日：</span>
                                        <input <?php echo $this->form->getField('birthday');?> style="width:300px;" type="text" class="form-control" data-widget="datepicker" >
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="birthday_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field" id="baby_type">
                                        <span class="input-group-addon" style="border-right:solid 1px #ccc;"><font style="color:red;"> * </font>宝宝类型：</span>
                                        &nbsp;&nbsp;
                                        <?php if (!empty(SelfConfig::$BABY_TYPE) && is_array(SelfConfig::$BABY_TYPE)) {?>
                                            <?php foreach (SelfConfig::$BABY_TYPE as $value => $text) {?>
                                                <input type="radio" name="baby_type_radio"  value="<?php echo $value;?>" <?php if($value == Util::getFromArray('baby_type', $this->info, '')) {?>checked<?php }?>> <?php echo $text?>
                                            <?php }?>
                                        <?php }?>
                                        <input type="hidden" <?php echo $this->form->getField('baby_type');?>/>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="baby_type_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>服务始于：</span>
                                        <?php $sDate = !empty($this->info['service_time_b']) ? date("Y-m-d", $this->info['service_time_b']) : '';?>
                                        <?php $sHour = !empty($this->info['service_time_b']) ? date("G", $this->info['service_time_b']) : '';?>
                                        <input <?php if ($this->isEdit){?>disabled<?php }?> style="width:200px;" type="text" class="form-control" <?php echo $this->form->getField('service_time_b');?> data-widget="datepicker" >
                                        <select <?php if ($this->isEdit){?>disabled<?php }?> class="form-control" style="width:100px;" name="hour_b_validate">
                                            <option value="0">请选</option>
                                            <?php for ($i = 1; $i <= 24; $i++){?>
                                                <option <?php if($i == $sHour){?>selected<?php }?> value="<?= $i?>"><?= $i?> 点</option>
                                            <?php }?>
                                        </select>
                                        <input type="hidden" <?php echo $this->form->getField('hour_b');?>/>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="service_time_b_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>服务终于：</span>
                                        <?php $eDate = !empty($this->info['service_time_e']) ? date("Y-m-d", $this->info['service_time_e']) : '';?>
                                        <?php $eHour = !empty($this->info['service_time_e']) ? date("G", $this->info['service_time_e']) : '';?>
                                        <input <?php if ($this->isEdit){?>disabled<?php }?> style="width:200px;" type="text" class="form-control" <?php echo $this->form->getField('service_time_e');?> data-widget="datepicker" >
                                        <select <?php if ($this->isEdit){?>disabled<?php }?> class="form-control" style="width:100px;" name="hour_e_validate">
                                            <option value="0">请选</option>
                                            <?php for ($i = 1; $i <= 24; $i++){?>
                                                <option <?php if($i == $eHour){?>selected<?php }?> value="<?= $i?>"><?= $i?> 点</option>
                                            <?php }?>
                                        </select>
                                        <input type="hidden" <?php echo $this->form->getField('hour_e');?>/>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="service_time_e_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon">客户籍贯：</span>
                                        <input <?php echo $this->form->getField('customer_native');?> type="text" style="width:300px;" class="form-control">
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="customer_native_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field" id="customer_nation">
                                        <span class="input-group-addon" style="border-right: solid 1px #ccc;">客户民族：</span>
                                        &nbsp;&nbsp;
                                        <?php if (!empty(SelfConfig::$CUSTOMER_NATION) && is_array(SelfConfig::$CUSTOMER_NATION)) {?>
                                            <?php foreach (SelfConfig::$CUSTOMER_NATION as $value => $text) {?>
                                                <input type="radio" name="customer_nation_radio"  value="<?php echo $value;?>" <?php if($value == Util::getFromArray('customer_nation', $this->info, '')) {?>checked<?php }?>> <?php echo $text?>
                                            <?php }?>
                                        <?php }?>
                                        <input type="hidden" <?php echo $this->form->getField('customer_nation');?>/>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="customer_nation_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon">上户地址：</span>
                                        <input <?php echo $this->form->getField('hold_address');?> style="width:300px;" type="text" class="form-control">
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="hold_address_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field" id="salary">
                                        <span class="input-group-addon" style="border-right:solid 1px #ccc;">月嫂薪资范围：</span>
                                        <?php $salary = !empty($this->info['salary']) ? explode(',', $this->info['salary']) : array();?>
                                        <?php if (!empty(SelfConfig::$PARENTAL_SALARY) && is_array(SelfConfig::$PARENTAL_SALARY)) {?>
                                            <?php foreach (SelfConfig::$PARENTAL_SALARY as $value => $text) {?>
                                                &nbsp;&nbsp;
                                                <input type="checkbox" <?php if(in_array($value, $salary)){?>checked<?php }?> name="salary_check" value="<?php echo $value;?>"> <?php echo $text?>
                                            <?php }?>
                                        <?php }?>
                                        <input type="hidden" <?php echo $this->form->getField('salary');?>/>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="salary_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field" id="work_age">
                                        <span class="input-group-addon" style="border-right:solid 1px #ccc;">月嫂工作年限：</span>
                                        <?php $workAge = !empty($this->info['work_age']) ? explode(',', $this->info['work_age']) : array();?>
                                        <?php if (!empty(SelfConfig::$PARENTAL_WORK_AGE) && is_array(SelfConfig::$PARENTAL_WORK_AGE)) {?>
                                            <?php foreach (SelfConfig::$PARENTAL_WORK_AGE as $value => $text) {?>
                                                &nbsp;&nbsp;
                                                <input type="checkbox" <?php if(in_array($value, $workAge)){?>checked<?php }?> name="work_age_check" value="<?php echo $value;?>"> <?php echo $text?>
                                            <?php }?>
                                        <?php }?>
                                        <input type="hidden" <?php echo $this->form->getField('work_age');?>/>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="work_age_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon">特殊需求：</span>
                                        <textarea id="require" style="width:300px;" <?php echo $this->form->getField('require');?> class="form-control" rows="3" placeholder="希望是哪里人？不要哪里人？..."><?php echo Util::getFromArray('require', $this->info, '');?></textarea>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="require_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon">订单备注：</span>
                                        <textarea id="mark" style="width:300px;" <?php echo $this->form->getField('mark');?> class="form-control" rows="3" placeholder="在这里填写订单备注"><?php echo Util::getFromArray('mark', $this->info, '');?></textarea>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="mark_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <input type="hidden" name="customer_id" value="<?php echo $this->customerId;?>"/>
                    <input type="hidden" name="is_edit" value="<?php echo $this->isEdit;?>"/>
                    <input type="hidden" name="order_id" value="<?php echo Util::getFromArray('id', $this->info, 0);?>"/>
                    <input type="hidden" name="wx_order_id" value="<?php echo $this->wxOrderId;?>"/>
                    <button data-action-type="submit" style="margin-left:120px;" class="btn btn-success"><?php echo $this->isEdit ? '修改' : '创建';?>订单</button>
                </div><!-- /.box-footer-->
            </div>
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">

    G.use(['validator', 'jquery'], function (v, $) {
        G.use(['widget/form/form.js'], function (Form) {
            var form = new Form({
                el: '#form'
            });

            window.form = form;
        });
    });

    G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
        Backend.run();
        Backend.on("submit", function(e) {
            e.preventDefault();
            var $this = $(this);
            form.validate().done(function() {
                $this.addClass('disabled');
                $.ajax({
                    url: "/center/order/ajaxAddParentalOrder/",
                    type: "POST",
                    dataType: "json",
                    data: {
                        "customer_name":   $('input[name="customer_name"]').val(),
                        "mobile":          $('input[name="mobile"]').val(),
                        "birthday":        $('input[name="birthday"]').val(),
                        "baby_type":       $('input[name="baby_type"]').val(),
                        "service_time_b":  $.trim($('input[name="service_time_b"]').val()),
                        "service_time_e":  $.trim($('input[name="service_time_e"]').val()),
                        "customer_native": $('input[name="customer_native"]').val(),
                        "customer_nation": $('input[name="customer_nation"]').val(),
                        "hold_address":    $('input[name="hold_address"]').val(),
                        "salary":          $('input[name="salary"]').val(),
                        "work_age":        $('input[name="work_age"]').val(),
                        "require":         $('#require').val(),
                        "mark":            $('#mark').val(),
                        "hour_b":          $('input[name="hour_b"]').val(),
                        "hour_e":          $('input[name="hour_e"]').val(),
                        "is_edit":         $('input[name="is_edit"]').val(),
                        "id":              $('input[name="order_id"]').val(),
                        "wx_order_id" :    $('input[name="wx_order_id"]').val(),
                        "customer_id" :    $('input[name="customer_id"]').val()
                    },
                    success: function (result) {
                        if (result.errorCode) {
                            //失败
                            Backend.trigger('alert-error', result.msg);
                            $this.removeClass('disabled');
                        } else {
                            //成功
                            Backend.trigger('alert-success', result.msg);
                            window.setTimeout(function () {
                                location.href = "/center/orderList/default/";
                            }, 3000);
                        }
                    },
                    error: function () {
                        Backend.trigger('alert-error', "内容提交失败!");
                        $this.removeClass('disabled');
                    }
                });
            }).fail(function() {
                //错误定位
                var offset = $('#form').find('.ui-field-fail').offset();
                if (offset) {
                    $('html, body').scrollTop(offset.top);
                }
                return;
            });
        });

        //验证宝宝类型表单域
        $('#baby_type .iCheck-helper').click(function() {
            $('input[name="baby_type"]').val($('input:radio[name="baby_type_radio"]:checked').val());
            form.getField('baby_type').validate();
        });
        //服务开始时间验证
        $('select[name="hour_b_validate"]').change(function() {
            var $val = $(this).val();
            $val = $val == 0 ? '' : $val;
            $('input[name="hour_b"]').val($val);
            form.getField('hour_b').validate();
            form.getField('service_time_b').validate();
        });
        //服务结束时间验证
        $('select[name="hour_e_validate"]').change(function() {
            var $val = $(this).val();
            $val = $val == 0 ? '' : $val;
            $('input[name="hour_e"]').val($val);
            form.getField('hour_e').validate();
            form.getField('service_time_e').validate();
        });
        //验证客户民族表单域
        $('#customer_nation .iCheck-helper').click(function() {
            $('input[name="customer_nation"]').val($('input:radio[name="customer_nation_radio"]:checked').val());
            form.getField('customer_nation').validate();
        });
        //验证月嫂薪资范围表单域
        $('#salary .iCheck-helper').click(function() {
            var salary = '';
            $('input[name="salary_check"]:checked').each(function() {
                salary = salary + $(this).val() + ',';
            });
            $('input[name="salary"]').val(salary);
            form.getField('salary').validate();
        });
        //验证月嫂工作年限表单域
        $('#work_age .iCheck-helper').click(function() {
            var workAge = '';
            $('input[name="work_age_check"]:checked').each(function() {
                workAge = workAge + $(this).val() + ',';
            });
            $('input[name="work_age"]').val(workAge);
            form.getField('work_age').validate();
        });
    });
</script>
</body>
</html>