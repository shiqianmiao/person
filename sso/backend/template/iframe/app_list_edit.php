<form method="POST" action="/sso/app/ajaxEdit">
<table class="table table-bordered" style="width: 400px; margin: 10px;">
    <tbody>
        <tr>
            <td><font color="red">*</font>应用代号：</td>
            <td><input readonly="true" type="text" name="app" value="<?php echo $this->row['app']; ?>"/></td>
        </tr>
        <tr>
            <td><font color="red">*</font>应用名称：</td>
            <td><input readonly="true" type="text" name="name" value="<?php echo $this->row['name']; ?>"/></td>
        </tr>
        <tr>
            <td>应用描述：</td>
            <td><input type="text" name="brief" value="<?php echo $this->row['brief']; ?>"/></td>
        </tr>
        <tr>
            <td><input data-action-type="submit" class="btn btn-success" type="submit" value="修改" /></td>
            <td><input type="hidden" name='id' value="<?php echo $this->row['id']; ?>" /></td>
        </tr>
    </tbody>
</table>
</form>
<script type="text/javascript" charset="utf-8">
    G.use(["app/backend/js/backend.js", "jquery"], function(Backend, $) {
        var input_id    = $("input[name=id]");
        var input_brief = $("input[name=brief]");
        Backend.on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                url : "/sso/app/ajaxEdit",
                type : "POST",
                dataType : "json",
                data : {
                    "id" : input_id.val(),
                    "brief" : input_brief.val()
                },
                success : function(result) {
                    if (result.errorCode == 1) {
                        Backend.trigger("alert-error", result.msg);
                    } else if (result.errorCode == 0) {
                        Backend.trigger("alert-success", "编辑成功");
                        setTimeout(function () {
                            parent.dialog.close();
                        }, 100);
                    }
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
        });
    });
</script>
