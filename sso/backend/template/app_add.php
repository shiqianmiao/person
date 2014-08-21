<form method="POST" action="/sso/app/add">
<table class="table table-bordered" style="width: 600px;">
    <tbody>
        <tr>
            <td><font color="red">*</font>应用代号：</td>
            <td><input type="text" name="app" class="pull-left"/><div name="app" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td><font color="red">*</font>应用名称：</td>
            <td><input type="text" name="name" class="pull-left"/><div name="name" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td>应用描述：</td>
            <td><input type="text" name="brief" /></td>
        </tr>
        <tr>
            <td><input data-action-type="submit" class="btn btn-success" type="submit" value="添加" /></td>
            <td></td>
        </tr>
    </tbody>
</table>
</form>
<script type="text/javascript" charset="utf-8">
    G.use(["app/backend/js/backend.js", "jquery"], function(Backend, $) {
        
        var div_app = $("div[name=app]");
        var div_name = $("div[name=name]");
        var input_app = $("input[name=app]");
        var input_name = $("input[name=name]");
        var input_brief = $("input[name=brief]");
        
        Backend.on("submit", function(e) {
            e.preventDefault();
            var notSubmit = false;
            div_app.html("");
            div_name.html("");
            if (input_app.val() == "") {
                div_app.html("&nbsp请输入应用代号");
                notSubmit = true;
            }
            if (input_name.val() == "") {
                div_name.html("&nbsp请输入应用名称");
                notSubmit = true;
            }
            if (notSubmit) {
                return false;
            }
            $.ajax({
                url : "/sso/app/ajaxAdd",
                type : "POST",
                dataType : "json",
                data : {
                    "name" : input_name.val(),
                    "app" : input_app.val(),
                    "brief" : input_brief.val()
                },
                success : function(result) {
                    if (result.errorCode == 0) {
                        Backend.trigger("alert-success", "添加成功");
                    } else {
                        Backend.trigger("alert-error", "已经添加");
                    }
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
        });
    });
</script>
