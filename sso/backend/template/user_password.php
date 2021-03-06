<table class="table table-bordered" style="width: 600px;">
    <tbody>
        <tr>
            <td>ID：</td>
            <td>
            <input readonly="true" type="text" name="id" />
            </td>
        </tr>
        <tr>
            <td><font color="red">*</font>帐号：</td>
            <td>
            <input onkeypress="return false;" type="text" name="account" class="pull-left"/>
            <div name="account" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td>姓名：</td>
            <td>
            <input readonly="true" type="text" name="name" />
            </td>
        </tr>
        <tr>
            <td>部门：</td>
            <td>
            <input readonly="true" type="text" name="department" />
            </td>
        </tr>
        <tr>
            <td>职位：</td>
            <td>
            <input readonly="true" type="text" name="role" />
            </td>
        </tr>
        <tr>
            <td>
            <input name="submit" class="btn btn-success" type="button" value="恢复初始密码" />
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
    GJ.use("jquery", function() {

        var div_account = $("div[name=account]");
        var input_id = $("input[name=id]");
        var input_account = $("input[name=account]");
        var input_name = $("input[name=name]");
        var input_department = $("input[name=department]");
        var input_role = $("input[name=role]");
        var btn_submit = $("input[name=submit]");

        input_account.change(function() {
            $.ajax({
                url : "/sso/user/ajaxGetInfo",
                type : "POST",
                dataType : "json",
                data : {
                    "account" : input_account.val()
                },
                success : function(result) {
                    if (result.errorCode == 0) {
                        div_account.html("");
                        input_id.val(result.row.id);
                        input_name.val(result.row.name);
                        input_department.val(result.row.department);
                        input_role.val(result.row.role);
                    } else {
                        div_account.html("&nbsp;帐号不存在");
                        input_id.val("");
                        input_name.val("");
                        input_department.val("");
                        input_role.val("");
                    }
                },
                error : function() {
                    input_name.val("error");
                }
            });
        });

        btn_submit.click(function() {
            var notSubmit = false;
            div_account.html("");
            if (input_role.val() == "") {
                div_account.html("&nbsp;帐号不存在");
                notSubmit = true;
            }
            if (notSubmit) {
                return false;
            }
            $.ajax({
                url : "/sso/user/ajaxPassword",
                type : "POST",
                dataType : "json",
                data : {
                    "id" : input_id.val()
                },
                success : function(result) {
                    if (result.errorCode == 0) {
                        GJ.app.backend.trigger("alert-success", "恢复初始密码成功");
                    } else {
                        GJ.app.backend.trigger("alert-error", "恢复初始密码失败");
                    }
                },
                error : function() {
                    GJ.app.backend.trigger("alert-error", "表单提交失败");
                }
            });
        })
    }); 
</script>