<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box box-info" style="border: none;height:100%;">
        <div class="box-body">
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>新服务开始时间:</span>
                <input name="new_start_date" type="text" class="form-control" style="width:250px;" placeholder="请选择要终止订单的日期！" data-widget="datepicker">
                <select class="form-control" style="width:100px;" name="new_start_hour">
                    <option value="0">请选小时</option>
                    <?php for ($i = 1; $i <= 24; $i++){?>
                        <option value="<?= $i?>"><?= $i?> 点</option>
                    <?php }?>
                </select>
            </div>
            <br/>
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>新服务结束时间:</span>
                <input name="new_end_date" type="text" class="form-control" style="width:250px;" placeholder="请选择要终止订单的日期！" data-widget="datepicker">
                <select class="form-control" style="width:100px;" name="new_end_hour">
                    <option value="0">请选小时</option>
                    <?php for ($i = 1; $i <= 24; $i++){?>
                        <option value="<?= $i?>"><?= $i?> 点</option>
                    <?php }?>
                </select>
            </div>
            <br/>
            <div class="input-group" style="margin-left:50px;">
                <span class="btn btn-success" data-action-type="submit">确定修改档期</span>
            </div>
            
            <input type="hidden" name="order_id" value="<?php echo !empty($this->orderId) ? $this->orderId : 0;?>"/>
        </div><!-- /.box-body -->
    </div>
    
<script>
G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
    Backend.on('submit', function (e) {
        var $this = $(this);
        $this.addClass('disabled');
        $.ajax({
            url : "/center/orderYuesaoList/ajaxChgSchedule",
            type : "POST",
            dataType : "json",
            data : {
                "order_id"  : $('input[name="order_id"]').val(),
                "new_start_hour" : $('select[name="new_start_hour"]').val(),
                "new_start_date" : $.trim($('input[name="new_start_date"]').val()),
                "new_end_hour" : $('select[name="new_end_hour"]').val(),
                "new_end_date" : $.trim($('input[name="new_end_date"]').val())
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
                        parent.dialog.close();
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