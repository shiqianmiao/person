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
            
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title" style="font-weight: bold;">添加儿歌类文章</h3>
                </div>
                <div class="box-body">
                    <table class="table" style="width:500px;border:none;">
                        <tbody>
                            <tr>
                                <td style="border: none;">
                                    <div class="input-group">
                                        <span class="input-group-addon">儿歌名称：</span>
                                        <input name="title" value="<?php echo !empty($this->artInfo['title']) ? $this->artInfo['title'] : '';?>" type="text" style="width: 200px;" class="form-control" placeholder="填写儿歌名称">
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="border: none;">
                                    <div class="input-group">
                                        <span class="input-group-addon">关键字：</span>
                                        <input name="keyword" value="<?php echo !empty($this->artInfo['keyword']) ? $this->artInfo['keyword'] : '';?>" type="text" style="width: 380px;" class="form-control" placeholder="填写本文关键字、多个用|隔开。例如:食材|美食">
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="border: none;">
                                    <div class="input-group">
                                        <span class="input-group-addon">上传儿歌：</span>
                                        &nbsp;&nbsp;&nbsp;
                                        <button data-action-type="up_song" class="btn btn-info">上传儿歌</button>
                                        <a href="<?php echo !empty($this->artInfo['audio']) ? $this->artInfo['audio'] : 'javascript:;';?>" target="_blank" style="color:red;" id="audio_url"><?php echo !empty($this->artInfo['audio']) ? $this->artInfo['audio'] : '';?></a>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="border: none;">
                                    <div class="input-group">
                                        <span class="input-group-addon">上传封面图：</span>
                                        <div style="margin-left:15px;margin-top:6px;" id="uploader"></div>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="border:none;">
                                    <div class="input-group">
                                        <span class="input-group-addon">儿歌简介：</span>
                                        <textarea id="brief" style="width:380px;" class="form-control" rows="3" placeholder="填写该儿歌的简单介绍"><?php echo !empty($this->artInfo['brief']) ? $this->artInfo['brief'] : '';?></textarea>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="border: none;">
                                    <div class="input-group">
                                        <span class="input-group-addon">适合月份：</span>
                                        &nbsp;&nbsp;&nbsp;
                                        <input name="month_b" value="<?php echo isset($this->artInfo['month_b']) ? $this->artInfo['month_b'] : '';?>" type="text" style="width: 80px;"/>
                                        ~
                                        <input name="month_e" value="<?php echo !empty($this->artInfo['month_e']) ? $this->artInfo['month_e'] : '';?>" type="text" style="width: 80px;"/>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="border:none;">
                                    歌词：
                                    <script type="text/plain" id="myEditor" style="width:480px;height:200px;"><?php if(!empty($this->artInfo['cont'])){ echo htmlspecialchars_decode($this->artInfo['cont']); }?></script>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <input type="hidden" name="cat_id" value="<?php echo $this->catId;?>"/>
                    <input type="hidden" name="art_id" value="<?php echo !empty($this->artInfo['id']) ? $this->artInfo['id'] : 0;?>"/>
                    <input type="hidden" name="is_edit" value="<?php echo !empty($this->edit) ? 1 : 0;?>"/>
                    <input type="hidden" name="audio_url" value="<?php echo !empty($this->artInfo['audio']) ? $this->artInfo['audio'] : 0;?>"/>
                    <button data-action-type="submit" style="margin-left:120px;" class="btn btn-success">发布文章</button>
                </div><!-- /.box-footer-->
            </div>
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">
G.use(['uploader', 'jquery'], function (u,$) {
    G.use(['widget/imageUploader/image_uploader.js'], function (Uploader) {
        var uploaderCarNumber = new Uploader({
            el: '#uploader',
            name: 'cover',
            width: 100,
            height: 30,
            maxNum: 1,
            type : 'jpg,jpeg,png,gif',
            uploadedFiles : <?php echo !empty($this->artInfo['cover']) ? $this->artInfo['cover'] : '[]';?>,
            //button_image_url : "http://sta.anxin365.com/app/mbs/img/btn_car_number_upload.png",
            editAble : false,
            postParams : {
                thumbWidth:120,
                thumbHeight:90,
            },
            url: 'http://upload.anxin365.com/uploader.php?pre=help'
        });
    });
});

G.use(['lib/umeditor/umeditor.js', 'app/backend/js/backend.js', 'jquery', 'widget/dialog/dialog.js'], function (UM, Backend, $, Dialog) {
    Backend.run();
    var ue;
    //实例化编辑器
    var ue = UM.getEditor('myEditor');
    ue.ready(function () {
        Backend.on("submit", function(e) {
            e.preventDefault();
            var catId = $('input[name="cat_id"]').val();
            
            $.ajax({
                url : "/center/help/ajaxSubmitArticle/",
                type : "POST",
                dataType : "json",
                data : {
                    "is_edit" : $('input[name="is_edit"]').val(),
                    "art_id"  : $('input[name="art_id"]').val(),
                    "cont"    : ue.getContent(),
                    "cat_id"  : catId,
                    "title"   : $.trim($('input[name="title"]').val()),
                    "keyword" : $.trim($('input[name="keyword"]').val()),
                    "brief"   : $('#brief').val(),
                    "month_b" : $('input[name="month_b"]').val(),
                    "month_e" : $('input[name="month_e"]').val(),
                    "audio_url" : $('input[name="audio_url"]').val(),
                    "cover"   : $('input[name="cover"]').val()
                },
                success : function(result) {
                    if (result.errorCode) {
                        //失败
                        Backend.trigger('alert-error', result.msg);
                    } else {
                        //成功
                        Backend.trigger('alert-success', result.msg);
                        window.setTimeout(function() {
                            location.href="/center/help/articleList/?cat_id="+catId;
                        }, 3000);
                    }
                },
                error : function() {
                    Backend.trigger('alert-error', "内容提交失败!");
                }
            });
        });
    });

    //上传儿歌
    Backend.on('up_song', function (e) {
        e.preventDefault();
        var url = "/center/help/iframeUpSong";
        var dialog = new Dialog({
            title : '上传儿歌',
            width : 500,
            height: 150,
            close : function() {
            }
        });
        dialog.ready(function () {
            this.open(url, function() {
                this.register('setAudio', function(url) {
                    $('#audio_url').html(url);
                    $('#audio_url').attr('href', url);
                    $('input[name="audio_url"]').val(url);
                });
            });
        });
        window.dialog = dialog;
    });
});
</script>
</body>
</html>