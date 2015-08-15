<html>
<?php $this->view->load('widget/head_tpl.php');?>
<body>
    <div class="box" style="margin-bottom: 5px;border-top-width:1px; height: 250px;">
        <table class="table" style="margin-bottom: 5px;">
            <tbody>
                <tr>
                    <td style="width:100px;">头像：</td>
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
                    <td><button data-order-id="<?php echo $this->orderId;?>" data-id="<?php echo $this->id;?>" class="btn btn-success" data-action-type="real_match">确定要匹配</button></td>
                </tr>
            </tbody>
        </table>
    </div><!-- /.box -->
    
<script>
G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
    Backend.run();
    Backend.on('real_match', function (e) {
        var $this = $(this);
        var $id = $this.data('id');
        $this.addClass('disabled');
        $.ajax({
            url : "/center/order/subMatch",
            type : "POST",
            dataType : "json",
            data : {
                "id" : $id,
                "order_id" : $this.data('order-id'),
                "name" : "<?php echo $this->info['full_name'];?>",
                "manager_id" : <?php echo $this->info['manager'];?>
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