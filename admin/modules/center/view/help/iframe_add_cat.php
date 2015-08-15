<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box box-info" style="border: none;height:100%;">
        <div class="box-body">
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>大类类型:</span>
                <select class="form-control" name="cat_type">
                    <option value="0">请选择大类类型</option>
                    <?php if (!empty($this->catType)) {?>
                        <?php foreach ($this->catType as $value => $type) {?>
                            <option <?php if(!empty($this->info["type"]) && $this->info['type'] == $value) { ?>selected<?php }?> value="<?= $value?>"><?= $type?></option>
                        <?php }?>
                    <?php }?>
                </select>
            </div>

            <br/>
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>大类名称:</span>
                <input name="name" value='<?php echo !empty($this->info["name"]) ? $this->info["name"] : ""?>' type="text" class="form-control" placeholder="不能超过20个字！">
            </div>
            <div id="cat_validate" style="display: none;"><span style="color:red;"><i class="fa fa-times-circle-o"></i> 大类不能为空！</span></div>

            <br/>
            <div class="input-group">
                <span class="input-group-addon"><font style="color:red;">* </font>发布类型:</span>
                <select class="form-control" name="pub_type">
                    <option value="0">请选择发布页面类型</option>
                    <?php if (!empty($this->pubType)) {?>
                        <?php foreach ($this->pubType as $value => $text) {?>
                            <option <?php if(!empty($this->info["pub_type"]) && $this->info['pub_type'] == $value) { ?>selected<?php }?> value="<?= $value?>"><?= $text?></option>
                        <?php }?>
                    <?php }?>
                </select>
            </div>

            <br/>
            <div class="input-group">
                <span class="input-group-addon">&nbsp;&nbsp;大类简介:</span>
                <textarea name="desc" class="form-control" rows="3" placeholder="不能超过40个字！"><?php echo !empty($this->info["desc"]) ? $this->info["desc"] : ""?></textarea>
            </div>
            
            <br/>
            <div class="input-group" style="margin-left:50px;">
                <span class="btn btn-success" data-action-type="sub_cat"><?php echo !empty($this->edit) ? '提交修改' : '添加';?></span>
            </div>
            
            <input type="hidden" name="id" value="<?php echo !empty($this->info['id']) ? $this->info['id'] : 0;?>"/>
            <input type="hidden" name="edit" value="<?php echo !empty($this->edit) ? 1 : 0;?>" />
        </div><!-- /.box-body -->
    </div>
    
<script>
G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
    Backend.run();
    Backend.on('sub_cat', function (e) {
        var $this = $(this);
        var $name = $.trim($('input[name="name"]').val());
        //判断名称
        if ($name == '') {
            $('#cat_validate').show();
            $('input[name="name"]').addClass('has-error');
            return;
        }
        $this.addClass('disabled');
        $.ajax({
            url : "/center/help/ajaxSubCat",
            type : "POST",
            dataType : "json",
            data : {
                "id"   : $('input[name="id"]').val(),
                "edit" : $('input[name="edit"]').val(),
                "name" : $name,
                "desc" : $.trim($('textarea[name="desc"]').val()),
                "type" : $('select[name="cat_type"]').val(),
                "pub_type" : $('select[name="pub_type"]').val()
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

    //验证大类名称
    $('input[name="name"]').blur(function() {
        var $name = $.trim($(this).val());
        //判断名称
        if ($name == '') {
            $('#cat_validate').show();
            $(this).addClass('has-error');
            return;
        } else {
            $('#cat_validate').hide();
            $(this).removeClass('has-error');
        }
    });
});
</script>
</body>
</html>