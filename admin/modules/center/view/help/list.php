<!DOCTYPE html>
<html>
<?php $this->view->load('widget/head_tpl.php');?>

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
        <section class="content">
            <a class="btn btn-block btn-success" href="/center/help/addArticle/?cat_id=<?php echo $this->catId;?>">
                <i class="fa fa-plus"></i> 添加该类文章
            </a>
            
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title" style="font-weight: bold;">文章列表</h3>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>文章标题</th>
                                <th>文章简介</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php if (!empty($this->info) && is_array($this->info)) {?>
                        <?php foreach ($this->info as $article) {?>
                            <tr>
                                <td style="width:300px;">
                                    <a href="/center/help/articleDetail/?id=<?php echo $article['id'];?>">
                                        <?php if (!empty($article['cover'])) {?>
                                            <img style="border-radius: 50px;-moz-border-radius: 50px;-webkit-border-radius: 50px;" src="<?php echo $article['cover'];?>" />
                                        <?php }?>
                                        <?php echo $article['title'];?>
                                    </a>
                                </td>
                                
                                <td style="width: 400px;"><?php echo $article['brief'];?></td>
                                <td>
                                    <a class="btn btn-info" href="/center/help/editArticle/?id=<?php echo $article['id'];?>">编辑</a>
                                    <!-- 
                                    <button class="btn btn-danger btn-flat" data-action-type="del_cat" data-name="<?php echo $article['title'];?>" data-id="<?php echo $cat['id'];?>">删除</button>
                                    -->
                                </td>
                            </tr>
                        <?php }?>
                    <?php }?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div>
        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<script type="text/javascript">
G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
    Backend.run();
    Backend.on('add_cat', function (e) {
        e.preventDefault();
        var url = "/center/help/iframeAddCat";
        var dialog = new Dialog({
            title : '添加帮助信息大类',
            width : 500,
            height: 250,
            close : function() {
                location.reload();
            }
        });
        dialog.ready(function () {
            this.open(url);
        });
        window.dialog = dialog;
    });

    Backend.on('edit_cat', function (e) {
        e.preventDefault();
        var url = "/center/help/iframeEditCat?id="+$(this).data('id');
        var dialog = new Dialog({
            title : '编辑帮助信息大类',
            width : 500,
            height: 250,
            close : function() {
                location.reload();
            }
        });
        dialog.ready(function () {
            this.open(url);
        });
        window.dialog = dialog;
    });

    Backend.on('del_cat', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $id = $this.data('id');

        var dialog = new Dialog ({
            title : '请仔细核对大类信息',
            width : 300,
            height: 50,
            content : '您确定要删除 "'+$this.data('name')+'" 大类?',
            skin : 'blue',
            ok   : function(e) {
                $this.addClass('disabled');
                $.ajax({
                    url : "/center/help/ajaxDelCat",
                    type : "POST",
                    dataType : "json",
                    data : {
                        "id" : $id
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
                                location.reload();
                            }, 2000);
                        }
                    },
                    error : function() {
                        Backend.trigger('alert-error', "提交失败");
                        $this.removeClass('disabled');
                    }
                });
            },
            cancel : function(e) {
            }
        });
    });
});
</script>
</body>
</html>
