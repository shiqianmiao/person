<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box box-info" style="border: none;height:100%;">
        <div class="box-body">
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;"> * </font>客户姓名:</span>
                <input name="real_name" value='<?php echo !empty($this->info["real_name"]) ? $this->info["real_name"] : ""?>' type="text" class="form-control" placeholder="请填写客户真实姓名！">
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>客户手机:</span>
                <input disabled name="mobile" value='<?php echo !empty($this->info["mobile"]) ? $this->info["mobile"] : ""?>' type="text" class="form-control">
            </div>
            <br/>
            
            <br/>
            <div class="input-group" style="margin-left:50px;">
                <span class="btn btn-success" data-action-type="submit">提交修改</span>
            </div>
            
            <input type="hidden" name="id" value="<?php echo !empty($this->info['id']) ? $this->info['id'] : 0;?>"/>
        </div><!-- /.box-body -->
    </div>
    
<script>
G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
    Backend.on('submit', function (e) {
        var $this = $(this);
        $this.addClass('disabled');
        $.ajax({
            url : "/center/customer/subEdit",
            type : "POST",
            dataType : "json",
            data : {
                "id"   : $('input[name="id"]').val(),
                "real_name" : $.trim($('input[name="real_name"]').val()),
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