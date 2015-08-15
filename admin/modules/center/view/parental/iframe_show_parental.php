<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box" style="margin-bottom: 5px;border-top-width:1px;">
        <table class="table" style="margin-bottom: 5px;">
            <tbody>
                <tr>
                    <td>头像：</td>
                    <td>
                        <img src="<?php echo $this->info['face_url'];?>" alt="<?php echo $this->info['full_name'];?>">
                    </td>
                </tr>
                
                <tr>
                    <td>姓名：</td>
                    <td><?php echo $this->info['full_name'];?></td>
                </tr>
                
                <tr>
                    <td>详情：</td>
                    <td><?php echo $this->info['age'];?>岁 &nbsp;
                    地址：<?php echo $this->info['address'];?> &nbsp; &nbsp;
                    <?php echo $this->info['id_check']?> &nbsp; &nbsp;
                    <?php echo $this->info['reg_time'];?>注册</td>
                </tr>
                
                <tr>
                    <td>操作：</td>
                    <td><button data-id="<?php echo $this->id;?>" class="btn btn-success" data-action-type="real_sign">确定签定合约</button></td>
                </tr>
            </tbody>
        </table>
    </div><!-- /.box -->
    
<script>
G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
    Backend.run();
    Backend.on('real_sign', function (e) {
        var $this = $(this);
        var $id = $this.data('id');
        $this.addClass('disabled');
        $.ajax({
            url : "/center/parental/signParental",
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