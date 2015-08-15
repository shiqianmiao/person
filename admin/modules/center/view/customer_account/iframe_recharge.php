<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box box-info" style="border: none;height:100%;">
        <div class="box-body">
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;"> </font>客户姓名:</span>
                <input name="name" value="<?php echo $this->name;?>" disabled type="text" class="form-control">
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;"> </font>客户手机:</span>
                <input name="mobile" value="<?php echo $this->mobile;?>" disabled type="text" class="form-control">
            </div>
            <br/>
            
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;"> </font>充值金额:</span>
                <input name="amount" type="text" class="form-control" placeholder="请填写您要充值的金额！">
            </div>
            <br/>

            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;"> </font>充值图片:</span>
                <div style="margin-left:15px;margin-top:6px;" id="uploader"></div>
            </div>
            <br/>
            
            <br/>
            <div class="input-group" style="margin-left:50px;">
                <span class="btn btn-success" data-action-type="submit">确定充值</span>
            </div>
            
            <input type="hidden" name="id" value="<?php echo !empty($this->accountId) ? $this->accountId : 0;?>"/>

            <div style="color:red;font-size:17px;margin:10px;">充值记录如下：</div>
            <?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>
        </div><!-- /.box-body -->
    </div>
    
<script>
G.use(['uploader', 'jquery'], function (u,$) {
    G.use(['widget/imageUploader/image_uploader.js'], function (Uploader) {
        var uploaderCarNumber = new Uploader({
            el: '#uploader',
            name: 'images',
            width: 100,
            height: 30,
            maxNum: 5,
            type : 'jpg,jpeg,png,gif',
            uploadedFiles : [],
            editAble : false,
            postParams : {
                thumbWidth:120,
                thumbHeight:90
            },
            url: 'http://upload.anxin365.com/uploader.php?pre=cus_recharge'
        });
    });
});

G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
    Backend.on('submit', function (e) {
        var $this = $(this);
        var images = $('input[name="images"]').val();
        if (images == '' || images == '[]' || $('input[name="images"]').length == 0) {
            Backend.trigger('alert-error', '充值图片必须上传！');
            return;
        }
        $this.addClass('disabled');
        $.ajax({
            url : "/center/accountCus/ajaxSubRecharge",
            type : "POST",
            dataType : "json",
            data : {
                "id"   : $('input[name="id"]').val(),
                "amount" : $.trim($('input[name="amount"]').val()),
                "images" : images
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