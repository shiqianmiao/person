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
        <div style="width:950px;margin-left:50px;">

            <div class="box box-info">
                <div class="box-body">
                    <form id="form" method="post" enctype="multipart/form-data" action="/center/banner/subBanner/">
                        <table class="table" style="border:none;">
                            <tbody>
                            <tr>
                                <td style="border: none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>Banner标题：</span>
                                        <input type="text" class="form-control" placeholder="填写banner大标题" style="width:300px;" <?php echo $this->form->getField('title1');?>>
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="title1_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>图片简介：</span>
                                        <input type="text" style="width:400px;" class="form-control" <?php echo $this->form->getField('brief');?> placeholder="填写banner简介">
                                        <span class="checkinfo"><i class="i-infom"></i>
                                            <span id="brief_tip"></span>
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none;">
                                    <div class="input-group ui-field">
                                        <span class="input-group-addon"><font style="color:red;"> * </font>Banner图片：</span>
                                        <div id="banner_img" style="margin:5px 0px 0px 5px;"></div>
                                    </div>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                        <input type="hidden" <?php echo $this->form->getField('id');?>/>
                    </form>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button data-action-type="submit" style="margin-left:120px;" class="btn btn-success"><?php echo $this->isEdit ? '修改' : '添加';?>Banner</button>
                </div><!-- /.box-footer-->
            </div>
        </div>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">

    G.use(['validator', 'jquery', 'widget/imageUploader/image_uploader.js'], function (v, $, Uploader) {
        G.use(['widget/form/form.js'], function (Form) {
            var form = new Form({
                el: '#form'
            });

            window.form = form;
        });
        //上传头像
        var uploaderPhoto = new Uploader({
            el: '#banner_img',
            name: 'banner',
            width: 106,
            height: 36,
            maxNum: 1,
            type : 'jpg,jpeg,png,gif',
            uploadedFiles : <?php echo !empty($this->info['image']) ? $this->info['image'] : '[]';?>,
            button_image_url : "http://sta.miaosq.com/app/backend/img/up_btn/default.png",
            editAble : false,
            postParams : {
                thumbWidth:120,
                thumbHeight:90
            },
            url: 'http://upload.miaosq.com/uploader.php?pre=banner'
        });
    });

    G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
        Backend.run();
        Backend.on("submit", function(e) {
            e.preventDefault();
            var $this = $(this);
            form.validate().done(function() {
                //banner照片
                var $photo = $('input[name="banner"]').val();
                if ($photo == '[]' || $photo == '' || $('input[name="banner"]').length == 0) {
                    Backend.trigger('alert-error', 'banner图片必须上传');
                    return;
                }
                $this.addClass('disabled');
                $('#form').submit();
            }).fail(function() {
                //错误定位
                var offset = $('#form').find('.ui-field-fail').offset();
                if (offset) {
                    $('html, body').scrollTop(offset.top);
                }
                return;
            });
        });
    });
</script>
</body>
</html>