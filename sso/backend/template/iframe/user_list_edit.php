<form method="POST" action="/sso/user/add">
<table class="table table-bordered" style="width: 400px; margin: 10px;">
    <tbody>
        <tr>
            <td>帐号：</td>
            <td><input readonly="true" type="text" name="account" value="<?php echo $this->row['account']; ?>"/></td>
        </tr>
        <tr>
            <td>出生年月：</td>
            <td><input data-widget="datepicker" type="text" name="birthday" value="<?php echo date('Y-m-d', $this->row['birthday']); ?>"/></td>
        </tr>
        <tr>
            <td>邮箱：</td>
            <td><input type="text" name="email" value="<?php echo $this->row['email']; ?>"/></td>
        </tr>
        <tr>
            <td>手机号：</td>
            <td><input type="text" name="phone" value="<?php echo $this->row['phone']; ?>"/></td>
        </tr>
        <tr>
            <td>入职时间：</td>
            <td><input data-widget="datepicker" type="text" name="entry_time" value="<?php echo date('Y-m-d', $this->row['entry_time']); ?>"/></td>
        </tr>
        <tr>
            <td>直接上司：</td>
            <td><input type="text" name="leader" value="<?php echo $this->row['leader']; ?>"/></td>
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
        var input_birthday = $("input[name=birthday]");
        var input_email = $("input[name=email]");
        var input_phone = $("input[name=phone]");
        var input_entry_time = $("input[name=entry_time]");
        var input_leader = $("input[name=leader]");
        GJ.app.backend.on("submit", function($el, e) {
            e.preventDefault();
            $.ajax({
                url : "/sso/user/ajaxEdit",
                type : "POST",
                dataType : "json",
                data : {
                    "id" : input_id.val(),
                    'birthday' : input_birthday.val(),
                    'email' : input_email.val(),
                    'phone' : input_phone.val(),
                    'entry_time' : input_entry_time.val(),
                    'leader' : input_leader.val()
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