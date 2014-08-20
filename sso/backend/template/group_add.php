<form method="POST" action="/sso/group/add">
    <table class="table table-bordered" style="width: 600px;">
        <tbody>
            <tr>
                <td><font color="red">*</font>组名：</td>
                <td>
                    <input type="text" name="name" class="pull-left"/>
                    <div name="name" class="pull-left" style="width: 100px; color: red;"></div>
                </td>
            </tr>
            <tr>
                <td><font color="red">*</font>唯一码：</td>
                <td><input type="text" name="code" /></td>
            </tr>
            <tr>
                <td>描述：</td>
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
    G.use(["app/backend/js/backend.js", "jquery"], function (Backend, $) {
        var div_name    = $("div[name=name]");
        var input_name  = $("input[name=name]");
        var input_brief = $("input[name=brief]");
        var input_code  = $("input[name=code]");
        
        Backend.on("submit", function (e) {
            e.preventDefault();
            var notSubmit   = false;
            
            div_name.html("");
            if (input_name.val() == "") {
                div_name.html("&nbsp请输入组名");
                notSubmit = true;
            }
            if (input_code.val() == "") {
                div_name.html("&nbsp请输入唯一码");
                notSubmit = true;
            }
            if (notSubmit) {
                return;
            }
            $.ajax({
                url : "/sso/group/ajaxAdd",
                type : "POST",
                dataType : "json",
                data : {
                    "name" : input_name.val(),
                    "brief" : input_brief.val(),
                    "code" : input_code.val()
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
