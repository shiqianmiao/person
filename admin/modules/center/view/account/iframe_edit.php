<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box box-info" style="border: none;height:100%;">
        <div class="box-body">
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>银行名称:</span>
                <select class="form-control" name="bank_name">
                    <option value="0">请选择银行</option>
                    <?php if (!empty(GlobalConfig::$BANK_CONF)) {?>
                    <?php foreach (GlobalConfig::$BANK_CONF as $value => $bankText) {?>
                    <option <?php if (!empty($this->info['bank_name']) && $value == $this->info['bank_name']){?>selected<?php }?> value="<?php echo $value;?>"><?php echo $bankText;?></option>
                    <?php }?>
                    <?php }?>
                </select>
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;"> </font>支行名称:</span>
                <input name="bank_branch" value='<?php echo !empty($this->info["bank_branch"]) ? $this->info["bank_branch"] : ""?>' type="text" class="form-control" placeholder="请仔细填写支行名称！">
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>银行卡号:</span>
                <input name="bank_card" value='<?php echo !empty($this->info["bank_card"]) ? $this->info["bank_card"] : ""?>' type="text" class="form-control" placeholder="请仔细填写银行卡号！">
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
            url : "/center/account/subEdit",
            type : "POST",
            dataType : "json",
            data : {
                "id"   : $('input[name="id"]').val(),
                "bank_name" : $('select[name="bank_name"]').val(),
                "bank_branch" : $.trim($('input[name="bank_branch"]').val()),
                "bank_card" : $.trim($('input[name="bank_card"]').val())
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