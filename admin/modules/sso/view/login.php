<!DOCTYPE html>
<html class="bg-black">
<?php $this->load('widget/head_tpl.php');?>
<body class="bg-black">
<div class="form-box" id="login-box">
    <div class="header">登录</div>
    <form action="/sso/user/login" method="post">
        <div class="body bg-gray">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="用户名"/>
            </div>
            <div class="form-group">
                <input type="password" name="passwd" class="form-control" placeholder="密码"/>
            </div>
            <div class="form-group">
                <input type="checkbox" name="remember_me"/> 记住密码
                <span name="result" style="margin: 10px 0 0 50px; color: red;"></span>
            </div>

            <input type="hidden" name="refer" value="<?php echo $this->referUrl;?>">

        </div>
        <div class="footer">
            <input type="submit" name="submit" class="btn bg-olive btn-block" value="登录"/>

            <p><a href="#">忘记密码</a></p>

            <a href="register.html" class="text-center">注册</a>
        </div>
    </form>
</div>

<script src="/bootstrap.min.js" type="text/javascript"></script>
<script>
    G.use(['jquery'], function ($) {
        var input_username = $("input[name=username]");
        var input_passwd = $("input[name=passwd]");
        var div_result = $("span[name=result]");
        var btn_submit = $("input[name=submit]");

        btn_submit.click(function () {
            var checkSubmit = true;

            div_result.html("");

            if ($.trim(input_username.val()) == "") {
                div_result.html("用戶名不能空");
                checkSubmit = false;
            } else if ($.trim(input_passwd.val()) == "") {
                div_result.html("密码不能为空");
                checkSubmit = false;
            }

            return checkSubmit;
        });
    });


</script>


</body>
</html>