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

            <form id="form" method="POST" action="/center/orderCommon/addOrderTwo/">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title" style="font-weight: bold;">添加订单第一步</h3>
                </div>
                <div class="box-body">
                        <table class="table" style="border:none;">
                            <tbody>
                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>客户手机：</span>
                                        <input type="text" style="width:300px;" class="form-control" <?php echo $this->form->getField('mobile');?> placeholder="务必填写客户的真实手机号码">
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="mobile_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <input type="hidden" name="wx_order_id" value="<?php echo Request::getGET('wx_order_id', 0);?>"/>
                    <input type="hidden" name="order_type" value="<?php echo $this->orderType;?>"/>
                    <button data-action-type="submit" style="margin-left:120px;" class="btn btn-success">进入第二步</button>
                </div><!-- /.box-footer-->
            </div>
            </form>
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
                $('#form').submit();
            }).fail(function() {
                //错误定位
                var offset = $('#form').find('.ui-field-fail').offset();
                if (offset) {
                    $('html, body').scrollTop(offset.top);
                }
                return;
            });
        });
    });
</script>
</body>
</html>