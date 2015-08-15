<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box box-info" style="border: none;height:100%;">
        <div class="box-body">
            <form id="form" action="/center/help/uploadSong/" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <span class="input-group-addon">选择儿歌文件:</span>
                <input class="form-control" type="file" name="upfile" /> 
            </div>
            
            <br/>
            <div class="input-group" style="margin-left:50px;">
                <a id="sub" class="btn btn-success">上传</a>
            </div>
            </form>
        </div><!-- /.box-body -->
    </div>
    <script type="text/javascript">
    G.use(["jquery", 'widget/dialog/dialog_helper.js'], function($, DialogHelper) {
        <?php if(!empty($this->url)){?>
        var url = '<?php echo $this->url?>';
        var dh = new DialogHelper();
        dh.call('setAudio', url);
        parent.dialog.close();
        <?php } else {?>
        $('#sub').click(function() {
            $(this).addClass('disabled');
            $(this).html('上传中、请耐心等待...');
            $('#form').submit();
        });
        <?php }?>
    });
    </script>
</body>
</html>