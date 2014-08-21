<form method="POST" action="/sso/privilege/add" class="pull-left">
<table class="table table-bordered" style="width: 600px;">
    <tbody>
        <tr>
            <td><font color="red">*</font>代号：</td>
            <td><input type="text" name="code" class="pull-left"/><div name="code" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td><font color="red">*</font>名称：</td>
            <td><input type="text" name="name" class="pull-left"/><div name="name" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td><font color="red">*</font>应用：</td>
            <td>
                <select name="app" class="pull-left">
                    <option value="-1">--请选择--</option>
                    <?php
                        foreach ($this->allApp as $i => $row) {
                            echo '<option value="' . $row['app'] . '">' . $row['app'] . ' - ' . $row['name'] . '</option>';
                        }
                    ?>
                </select>
                <div name="app" class="pull-left" style="width: 100px; color: red;"></div>
            </td>
        </tr>
        <tr>
            <td>url：</td>
            <td><input type="text" name="url" class="pull-left"/><div name="url" class="pull-left" style="width: 100px; color: red;"></div></td>
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
    G.use(["app/backend/js/backend.js", "jquery"], function(Backend, $) {
        var div_code = $("div[name=code]");
        var div_name = $("div[name=name]");
        var div_app = $("div[name=app]");
        var div_url = $("div[name=url]");
        var input_code = $("input[name=code]");
        var input_name = $("input[name=name]");
        var select_app = $("select[name=app]");
        var input_url = $("input[name=url]");
        var input_brief =  $("input[name=brief]");
        
        function hintInit() {
            div_code.html("");
            div_name.html("");
            div_app.html("");
            div_url.html("");
        }
        
        input_url.change(function() {
            hintInit();
            if (input_url.val() == "") {
                input_code.val("");
                input_name.val("");
                select_app.val("");
                return false;
            }
            $.ajax({
                url : "/sso/privilege/ajaxParseUrl",
                type : "POST",
                dataType : "json",
                data : {
                    "url" : input_url.val()
                },
                success : function(result) {
                    if (result.errorCode == 0) {
                        input_code.val(result.field.code);
                        select_app.val(result.field.app);
                    } else {
                        div_url.html(result.msg);
                    }
                },
                error : function() {
                    name_input.val("error");
                }
            });
        });
        
        Backend.on("submit", function(e) {
            e.preventDefault();
            hintInit();
            var notSubmit = false;
            if ($.trim(input_code.val()) == "") {
                div_code.html("&nbsp;请输入代号");
                notSubmit = true;
            }
            if ($.trim(input_name.val()) == "") {
                div_name.html("&nbsp;请输入名称");
                notSubmit = true;
            }
            if (select_app.val() == "-1") {
                div_app.html("&nbsp;请选择应用");
                notSubmit = true;
            }
            if (notSubmit) {
                return false;
            }
            $.ajax({
                url : "/sso/privilege/ajaxAdd",
                type : "POST",
                dataType : "json",
                data : {
                    "name" : input_name.val(),
                    "url" : input_url.val(),
                    "app" : select_app.val(),
                    "code" : input_code.val(),
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