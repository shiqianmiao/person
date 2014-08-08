<form method="POST" action="/sso/user/add">
<table class="table table-bordered" style="width: 600px;">
    <tbody>
        <tr>
            <td><font color="red">*</font>帐号：</td>
            <td><input type="text" name="account" class="pull-left"/><div name="account" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td><font color="red">*</font>姓名：</td>
            <td><input type="text" name="name" class="pull-left"/><div name="name" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td><font color="red">*</font>性别：</td>
            <td>
                <select name="sex" class="pull-left"/>
                    <option value="-1">--请选择--</option>
                    <option>男</option>
                    <option>女</option>
                </select>
                <div name="sex" class="pull-left" style="width: 100px; color: red;"></div>
            </td>
        </tr>
        <tr>
            <td>出生年月：</td>
            <td><input data-widget="datepicker" type="text" name="birthday" /></td>
        </tr>
        <tr>
            <td>邮箱：</td>
            <td><input type="text" name="email" /></td>
        </tr>
        <tr>
            <td>手机号：</td>
            <td><input type="text" name="phone" /></td>
        </tr>
        <tr>
            <td>入职时间：</td>
            <td><input data-widget="datepicker" type="text" name="entry_time" /></td>
        </tr>
        <tr>
            <td><font color="red">*</font>部门：</td>
            <td>
                <select name="department" class="pull-left">
                    <option value="-1">--请选择--</option>
                    <?php
                        foreach ($this->dpt as $k => $v) {
                            echo '<option value="' . $k . '">' . $v . '</option>';
                        }
                    ?>
                </select>
                <div name="department" class="pull-left" style="width: 100px; color: red;"></div>
            </td>
        </tr>
        <tr>
            <td><font color="red">*</font>职位：</td>
            <td><input type="text" name="role" class="pull-left"/><div name="role" class="pull-left" style="width: 100px; color: red;"></div></td>
        </tr>
        <tr>
            <td>直接上司：</td>
            <td><input type="text" name="leader" /></td>
        </tr>
        <tr>
            <td><input data-action-type="submit" class="btn btn-success" type="submit" value="添加" /></td>
            <td><font color="<?php echo $this -> color; ?>"><?php echo $this -> result; ?></font></td>
        </tr>
    </tbody>
</table>
</form>
<script type="text/javascript" charset="utf-8">
    GJ.use("app/backend/js/backend.js", function() {
        
        var div_account = $("div[name=account]");
        var div_name = $("div[name=name]");
        var div_sex = $("div[name=sex]");
        var div_department = $("div[name=department]");
        var div_role = $("div[name=role]");
        var input_account = $("input[name=account]");
        var input_name = $("input[name=name]");
        var select_sex = $("select[name=sex]");
        var select_department = $("select[name=department]");
        var input_role = $("input[name=role]");
        
        function hintInit() {
            div_account.html("");
            div_name.html("");
            div_sex.html("");
            div_department.html("");
            div_role.html("");
        }
        
        GJ.app.backend.on("submit", function($el, e) {
            hintInit();
            if (input_account.val() == "") {
                div_account.html("&nbsp请输入帐号");
                e.preventDefault();
            }
            if (input_name.val() == "") {
                div_name.html("&nbsp请输入姓名");
                e.preventDefault();
            }
            if (select_sex.val() == "-1") {
                div_sex.html("&nbsp请选择性别");
                e.preventDefault();
            }
            if (select_department.val() == "-1") {
                div_department.html("&nbsp请选择部门");
                e.preventDefault();
            }
            if (input_role.val() == "") {
                div_role.html("&nbsp请输入职位");
                e.preventDefault();
            }
        });
    });
</script>
