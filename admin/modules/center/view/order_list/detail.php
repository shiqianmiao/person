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
        <div style="padding:15px;">
            <?php echo $this->opUrl;?>
        </div>
        
        <div class="callout callout-info">
            
            <h4><i class="fa fa-hand-o-right"></i> &nbsp;订单号：<?php echo $this->info['order_num'];?></h4>
            <p>订单状态：<?php echo $this->info['status_text'];?></p>
            <p>服务时间：<?php echo $this->info['service_time_b'] . ' ~ ' . $this->info['service_time_e'];?></p>
            <p>客户姓名：<?php echo $this->info['customer_name'];?></p>
            <p>宝宝生日：<?php echo $this->info['birthday'];?></p>
            <p>服务类型：<?php echo $this->info['service_type'];?></p>
            <p>宝宝是：<?php echo $this->info['baby_type'];?></p>
            <p>客户名族：<?php echo $this->info['customer_nation'];?></p>
            <p>客户籍贯：<?php echo $this->info['customer_native'];?></p>
            <p>育儿嫂薪资范围：<?php echo $this->info['salary'];?></p>
            <p>育儿嫂工作年限：<?php echo $this->info['work_age'];?></p>
            <p>上户地址：<?php echo $this->info['hold_address'];?></p>
            <p>其他需求：<?php echo $this->info['require'];?></p>


            <?php if (!empty($this->doServe)) {?>
            <div class="box box-info">
                <dl><dt>获得好评数：</dt><dd><?php echo $this->praise;?></dd></dl>
                <dl><dt>获得差评数：</dt><dd><?php echo $this->complaint;?></dd></dl>
                <dl style="border:none;"><dt>获得小费：</dt><dd><?php echo $this->info['tip'];?>元</dd></dl>
            </div>

            <input type="hidden" name="order_id" value="<?php echo $this->info['id'];?>"/>
            <input type="hidden" name="worker_id" value="<?php echo $this->info['worker_id'];?>"/>
            <input type="hidden" name="order_type" value="<?php echo GlobalConfig::ORDER_TYPE_BABY;?>"/>
            <input type="hidden" name="quote" value="<?php echo $this->info['quote'];?>"/>
            <div class="box box-info">
                <h4><i class="fa fa-hand-o-right"></i> &nbsp;点评管理</h4>
                <?php if (!empty($this->comment) && is_array($this->comment)) {?>
                    <?php foreach ($this->comment as $com) {?>
                    <table>
                        <tr>
                            <td style="padding: 10px;width: 400px;">客户姓名：<?php echo $this->info['customer_name'];?>
                                (<?php echo $this->info['mobile'];?>)</td><td>评价时间：<?php echo date('Y-m-d H:i:s', $com['create_time']);?></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 10px;"><?php echo $com['comment'];?>
                                <a href="javascript:;" data-id="<?= $com['id'];?>" data-status="<?php echo SelfConfig::COMMENT_STATUS_DELETE;?>" data-action-type="comment_op">删除</a>&nbsp;&nbsp;
                                <a href="javascript:;" data-id="<?= $com['id'];?>" data-status="<?php echo SelfConfig::COMMENT_STATUS_SELF;?>" data-action-type="comment_op">仅客户自己可见</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 10px;">
                                <?php foreach ($this->comCat as $key => $cat) {?>
                                    <?php echo $cat;?>：<?php echo $com['parental_'.$key];?>分&nbsp;&nbsp;
                                <?php }?>
                            </td>
                        </tr>
                    </table>
                    <hr/>
                    <?php }?>
                <?php }?>

                <table style="margin-left: 20px;">
                    <?php if (!empty($this->comCat) && is_array($this->comCat)) {?>
                        <?php foreach ($this->comCat as $key => $cat) {?>
                        <tr>
                            <td style="padding: 6px;"><?php echo $cat;?>：</td>
                            <td style="padding: 6px;"><input type="radio" name="parental_<?php echo $key;?>" value="1"/> 1 </td>
                            <td style="padding: 6px;"><input type="radio" name="parental_<?php echo $key;?>" value="2"/> 2 </td>
                            <td style="padding: 6px;"><input type="radio" name="parental_<?php echo $key;?>" value="3"/> 3 </td>
                            <td style="padding: 6px;"><input type="radio" name="parental_<?php echo $key;?>" value="4"/> 4 </td>
                            <td style="padding: 6px;"><input type="radio" name="parental_<?php echo $key;?>" value="5"/> 5 </td>
                        </tr>
                        <?php }?>
                    <?php }?>
                </table>

                <textarea id="comment" style="width:500px;margin: 20px;" class="form-control" rows="3" placeholder="点评内容不超过500字"></textarea>
                <button data-action-type="sub_comment" style="margin-left: 50px;" class="btn btn-success">提交点评</button>
                <br/><br/>
            </div>
            
            <?php if (in_array('bc.*', $this->code) || in_array('bc.service', $this->code)) {?>
            <div class="box box-info">
                <h4><i class="fa fa-hand-o-right"></i> &nbsp;订单回访</h4>
                <?php if (!empty($this->visit) && is_array($this->visit)) {?>
                    <?php foreach ($this->visit as $v){?>
                    <table>
                        <tr>
                            <td style="padding: 10px;width: 400px;">回访人：<?php echo $v['visiter_name'];?>(<?php echo BcEnumerateConfig::$VISIT_TYPE[$v['type']];?>)</td>
                            <td>回访时间：<?php echo date('Y-m-d', $v['visit_time']);?></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 10px;">
                                <?php echo $v['brief'];?>
                            </td>
                        </tr>
                    </table>
                    <hr/>
                    <?php }?>
                <?php }?>

                <table class="table" style="width:400px;border:none;">
                    <tbody>
                        <tr>
                            <td style="width:100px;text-align:right;border:none;line-height:35px;">
                                客户回访：
                            </td>
                            <td style="border:none;">
                                <select name="visit_type" class="form-control">
                                    <option value="1">选择回访类型</option>
                                    <?php if (!empty(BcEnumerateConfig::$VISIT_TYPE)) {?>
                                    <?php foreach (BcEnumerateConfig::$VISIT_TYPE as $value => $type) {?>
                                    <option value="<?php echo $value;?>"><?php echo $type;?></option>
                                    <?php }?>
                                    <?php }?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style="width:100px;text-align:right;border:none;">回访纪要：</td>
                            <td style="border:none;">
                                <textarea id="visit_cont" class="form-control" rows="3" placeholder="输入回访纪要..."></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="box-footer">
                    <button data-action-type="sub_visit" class="btn btn-success">提交回访</button>
                </div><!-- /.box-footer-->
            </div>
            <?php }?>


            <!-- 红黄牌处罚-->
            <div class="box box-info">
                <h4><i class="fa fa-hand-o-right"></i> &nbsp;红黄牌记录</h4>
                <?php if (!empty($this->punish) && is_array($this->punish)) {?>
                <?php foreach ($this->punish as $pu) {?>
                <table>
                    <tr>
                        <td style="padding: 6px;width: 150px;"><?php echo $pu['create_user_name'];?>(<?php echo BcEnumerateConfig::$ORDER_PUNISH_TYPE[$pu['type']];?>)</td>
                        <td style="padding: 6px;width: 300px;">处罚说明：<?php echo $pu['brief'];?></td>
                        <td style="padding: 6px;width: 150px;">扣款<?php echo $pu['punish'] / 100;?>元</td>
                        <td style="padding: 6px;width: 300px;">记录时间：<?php echo date('Y-m-d H:i:s', $pu['create_time']);?></td>
                    </tr>
                </table>
                <hr/>
                <?php }?>
                <?php }?>

                <table class="table" style="width:400px;border:none;">
                    <tbody>
                    <tr>
                        <td style="width:100px;text-align:right;border:none;line-height:35px;">
                            处罚类型：
                        </td>
                        <td style="border:none;">
                            <select name="punish_type" class="form-control">
                                <option value="1">选择处罚类型</option>
                                <?php if (!empty(BcEnumerateConfig::$ORDER_PUNISH_TYPE)) {?>
                                    <?php foreach (BcEnumerateConfig::$ORDER_PUNISH_TYPE as $value => $type) {?>
                                        <option value="<?php echo $value;?>"><?php echo $type;?></option>
                                    <?php }?>
                                <?php }?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td style="width:100px;text-align:right;border:none;">处罚说明：</td>
                        <td style="border:none;">
                            <textarea id="punish_brief" class="form-control" rows="3" placeholder="输入处罚说明..."></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="box-footer">
                    <button data-action-type="sub_punish" class="btn btn-success">提交处罚</button>
                </div><!-- /.box-footer-->
            </div>
            <!--处罚结束-->
            <?php }?>
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">
G.use(['app/backend/js/backend.js', 'jquery', 'widget/dialog/dialog.js'], function (Backend, $, Dialog) {
    Backend.run();
    Backend.on('audition', function (e) {
        e.preventDefault();
        var id = $(this).data('block');
        $(this).hide();
        $('#'+id).show();
    });
    //提交回访
    Backend.on('sub_visit', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('disabled');
        $.ajax({
            url : "/center/orderList/ajaxSubmitVisit/",
            type : "POST",
            dataType : "json",
            data : {
                "visit_type" : $('select[name="visit_type"]').val(),
                "visit_cont" : $.trim($('#visit_cont').val()),
                "order_id" : $('input[name="order_id"]').val(),
                "order_type" : $('input[name="order_type"]').val()
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
    //终止订单
    Backend.on('stop_order', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = "/center/orderList/iframeStop/?id="+id;
        var dialog = new Dialog({
            title : '填写订单的终止时间',
            width : 500,
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
    //修改档期
    Backend.on('chg_schedule', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = "/center/orderList/iframeSchedule/?id="+id;
        var dialog = new Dialog({
            title : '填写新的档期的起止时间',
            width : 550,
            height: 350,
            close : function() {
                location.reload();
            }
        });
        dialog.ready(function () {
            this.open(url);
        });
        window.dialog = dialog;
    });
    //提交点评
    Backend.on('sub_comment', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('disabled');
        $.ajax({
            url : "/center/orderCommon/ajaxSubComment/",
            type : "POST",
            dataType : "json",
            data : {
                "order_type" : $('input[name="order_type"]').val(),
                "worker_id"  : $('input[name="worker_id"]').val(),
                "order_id"   : $('input[name="order_id"]').val(),
                "comment"    : $.trim($('#comment').val()),
                "parental_1" : $('input:radio[name="parental_1"]:checked').val(),
                "parental_2" : $('input:radio[name="parental_2"]:checked').val(),
                "parental_3" : $('input:radio[name="parental_3"]:checked').val(),
                "parental_4" : $('input:radio[name="parental_4"]:checked').val(),
                "parental_5" : $('input:radio[name="parental_5"]:checked').val()

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
    //提交处罚
    Backend.on('sub_punish', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('disabled');
        $.ajax({
            url : "/center/orderCommon/ajaxSubPunish/",
            type : "POST",
            dataType : "json",
            data : {
                "punish_type" : $('select[name="punish_type"]').val(),
                "punish_brief" : $.trim($('#punish_brief').val()),
                "order_id" : $('input[name="order_id"]').val(),
                "quote" : $('input[name="quote"]').val(),
                "worker_id"  : $('input[name="worker_id"]').val(),
                "order_type" : $('input[name="order_type"]').val()
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
    //点评的删除 和 设置仅自己可见
    Backend.on('comment_op', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('disabled');
        $.ajax({
            url : "/center/orderCommon/ajaxSetComment/",
            type : "POST",
            dataType : "json",
            data : {
                "status" : $this.data('status'),
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