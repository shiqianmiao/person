<form method="POST" action="/sso/group/ajaxEdit">
<table class="table table-bordered" style="width: 400px; margin: 10px;">
    <tbody>
        <tr>
            <td><font color="red">*</font>组名：</td>
            <td><input type="text" name="name" value="<?php echo $this->row['name']; ?>"/></td>
        </tr>
        <tr>
            <td><font color="red">*</font>唯一码：</td>
            <td><input type="text" name="code" value="<?php echo $this->row['code']; ?>"/></td>
        </tr>
        <tr>
            <td>描述：</td>
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
    GJ.use("app/backend/js/backend.js", function() {
        var input_id = $("input[name=id]");
        var input_brief = $("input[name=brief]");
        var input_name = $("input[name=name]");
        var input_code = $("input[name=code]");
        GJ.app.backend.on("submit", function($el, e) {
            e.preventDefault();
            $.ajax({
                url : "/sso/group/ajaxEdit",
                type : "POST",
                dataType : "json",
                data : {
                    "id" : input_id.val(),
                    "brief" : input_brief.val(),
                    "name" : input_name.val(),
                    "code" : input_code.val()
                },
                success : function(result) {
                    setTimeout(function () {
                        GJ.remote("close");
                    }, 100);
                },
                error : function() {
                    GJ.app.backend.trigger("alert-error", "表单提交失败");
                }
            });
        });
    });
</script>
