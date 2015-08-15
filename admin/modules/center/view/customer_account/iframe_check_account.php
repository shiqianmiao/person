<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box box-info" style="border: none;height:100%;">
        <div class="box-body">
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>银行名称:</span>
                <select class="form-control" name="bank_name" disabled>
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
                <input name="bank_branch" disabled value='<?php echo !empty($this->info["bank_branch"]) ? $this->info["bank_branch"] : ""?>' type="text" class="form-control" placeholder="请仔细填写支行名称！">
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>银行卡号:</span>
                <input name="bank_card" disabled value='<?php echo !empty($this->info["bank_card"]) ? $this->info["bank_card"] : ""?>' type="text" class="form-control" placeholder="请仔细填写银行卡号！">
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>转出账号:</span>
                <select class="form-control" name="out_card">
                    <option value=''>请选择要转出的卡号</option>
                    <?php if (!empty(GlobalConfig::$OUT_CARD)) {?>
                    <?php foreach (GlobalConfig::$OUT_CARD as $card) {?>
                    <option value="<?php echo isset($card['num']) ? $card['num'] : '';?>"><?php echo isset($card['brief']) ? $card['brief'] : '';?> <?php echo isset($card['num']) ? $card['num'] : '';?></option>
                    <?php }?>
                    <?php }?>
                </select>
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;"> </font>转出金额:</span>
                <input name="check_amount" value='<?php echo !empty($this->info["balance"]) ? $this->info["balance"] : ""?>' type="text" class="form-control" placeholder="请填写您要结算的金额！">
            </div>
            <br/>
            
            <br/>
            <div class="input-group" style="margin-left:50px;">
                <input type="hidden" name="balance" value="<?php echo $this->info["balance"];?>"/>
                <span class="btn btn-success" data-action-type="submit">确定结算</span>
            </div>
            
            <input type="hidden" name="id" value="<?php echo !empty($this->info['id']) ? $this->info['id'] : 0;?>"/>
        </div><!-- /.box-body -->
    </div>
    
<script>
G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
    Backend.on('submit', function (e) {
        var $this = $(this);
        var outCard = $('select[name="out_card"]').val();
        var balance = $('input[name="balance"]').val();
        var checkCn = $.trim($('input[name="check_amount"]').val());
        if (checkCn - balance > 0) {
            Backend.trigger('alert-error', '结账的金额不能大于账户余额');
            return;
        }
        if (outCard == '') {
            Backend.trigger('alert-error', '请选择要转出的账户');
            return;
        }
        $this.addClass('disabled');
        $.ajax({
            url : "/center/accountCus/ajaxCheckAccount",
            type : "POST",
            dataType : "json",
            data : {
                "id"   : $('input[name="id"]').val(),
                "bank_name" : $('select[name="bank_name"]').val(),
                "bank_branch" : $.trim($('input[name="bank_branch"]').val()),
                "bank_card" : $.trim($('input[name="bank_card"]').val()),
                "check_amount" : checkCn,
                "out_card" : outCard
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