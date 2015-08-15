<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box" style="margin-bottom: 5px;border-top-width:1px;">
        <table class="table" style="margin-bottom: 5px;">
            <tbody>
                <tr>
                    <div class="input-group">
                        <span class="input-group-addon"><font style="color: red;"> * </font>上传头像：</span>
                        <div style="margin-left:15px;margin-top:6px;" id="uploader"></div>
                    </div>
                </tr>
                
                <tr>
                    <div class="input-group">
                        <span class="input-group-addon"><font style="color: red;"> * </font>阿姨简介：</span>
                        <textarea id="desc" style="width:380px;" class="form-control" rows="3" placeholder="仔细填写阿姨简介"><?php echo !empty($this->workerInfo['description']) ? $this->workerInfo['description'] : '';?></textarea>
                    </div>
                </tr>
                
                <tr>
                    <td><button data-id="<?php echo $this->workerId;?>" class="btn btn-success" data-action-type="sub">确定修改</button></td>
                </tr>
            </tbody>
        </table>
    </div><!-- /.box -->
    
<script>
G.use(['uploader', 'jquery'], function (u,$) {
    G.use(['widget/imageUploader/image_uploader.js'], function (Uploader) {
        var uploaderCarNumber = new Uploader({
            el: '#uploader',
            name: 'face_url',
            width: 100,
            height: 30,
            maxNum: 1,
            type : 'jpg,jpeg,png,gif',
            uploadedFiles : <?php echo !empty($this->artInfo['cover']) ? $this->artInfo['cover'] : '[]';?>,
            //button_image_url : "http://sta.anxin365.com/app/mbs/img/btn_car_number_upload.png",
            editAble : false,
            postParams : {
                thumbWidth:120,
                thumbHeight:90
            },
            url: 'http://upload.anxin365.com/uploader.php?pre=face'
        });
    });
});

G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
    Backend.run();
    Backend.on('sub', function (e) {
        var $this = $(this);
        var $id = $this.data('id');
        $this.addClass('disabled');
        $.ajax({
            url : "/center/parental/ajaxSubEdit",
            type : "POST",
            dataType : "json",
            data : {
                "id" : $id,
                "face_url" : $('input[name="face_url"]').val(),
                "desc" : $('#desc').val()
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