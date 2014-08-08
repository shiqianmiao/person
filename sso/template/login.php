<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登录页面</title>
<script type="text/javascript" charset="utf-8" src="http://sta.273.cn/cgi/ganji_sta.php?file=ganji,js/util/jquery/jquery-1.7.2.js"></script>
<style type="text/css">
    html,body{height:100%;overflow:hidden;}
	body{margin:0;font-size:14px;background: -moz-linear-gradient(top, #173147, #8396a3 45%,#8396a3 70%);
		background: -webkit-gradient(linear, 0 0, 0 70%, from(#132f43), to(#8396a3));background-repeat: no-repeat;}
	.login{position:relative;margin:auto;background: url(http://static.273.cn/app/mbs/img/login_bg3.png) no-repeat;width:590px;height:590px;}
	.login .mlogo{height:197px;}
	.login .info{ position: absolute;left:84px;width:350px;height:300px;padding: 38px 35px 0 35px;}
	.input_box{margin:0 0 10px;color:#f6f6f6;position:relative;}
	.input1,.input2{margin:0;width:332px;height:20px;line-height:20px;padding:9px 0 9px 16px;border:1px solid #ddd;color:#727272;font-size:14px;outline: none;}
	.input2{margin:0 10px 0 0;width:236px;}
	.icon-true,.icon-false{background:url(http://static.273.cn/app/mbs/img/i.png) no-repeat;display:inline-block;height:21px;margin:10px 0 0 6px;width:21px;position:absolute;}
	.icon-false{background-position:0 -24px;}
	.input_box img{vertical-align:middle;margin-top:-3px;margin-bottom:1px;width:84px;height:40px;border:1px solid #ddd;cursor: pointer;}
    .button1{width: 350px;height: 40px;line-height: 40px;border:0;margin:5px 0;text-align:center;background:#3a7aa7;color:#fff;font-size:18px;font-family: "Microsoft YaHei" ! important;}
    a{display:inline-block;color:#676767;text-decoration:none;line-height: 40px;float:right;cursor: pointer;}
	a:hover{text-decoration:underline;}
    p{position:fixed;width:100%;bottom:15px;text-align:center;color:#fff;margin:0;font-size:12px;font-family: Arial,"宋体";}
</style>
</head>
<body>
    <form method="post" action="/account/login/">
    <div class="login" style="top:12%;">
        <div class="mlogo"></div>
		<div class="info">
        <div class="input_box">
            <input name="account" value="<?php echo $this->account; ?>" type="text" class="input1" placeholder="用户名"/><span class=""></span>
        </div>
        <div class="input_box">
            <input type="password" name="password" value="<?php echo $this->password; ?>" class="input1" placeholder="密码"/>
        </div>
        <div class="input_box">
            <input type="text" name="valid" class="input2" placeholder="验证码"/><img src="/account/showImage" id="code-img" align="absmiddle" /><span class="icon-false" style="display:none"></span>
        </div>
        <div class="input_box">
            <input type="submit" name="submit" class="button1" value="登陆" />
            <a class="fr" href="/BackPwd/inputUser/" target="_blank">忘记密码？</a>
            <input type="hidden" name="refer" value="<?php echo $this->refer; ?>">
            <div name="result" class="pull-left" style="margin: 5px; color: red;"><?php echo $this->resultMsg; ?></div></td>
        </div>
        </div>
    </div>
    </form>
    <p>Copyright © 1998 - 2014 273.cn All Rights Reserved</p>
</body>
</html>
<script>
    GJ.use("jquery", function() {
        var input_account = $("input[name=account]");
        var input_password = $("input[name=password]");
        var input_valid = $("input[name=valid]");
        var div_result = $("div[name=result]");
        var btn_submit = $("input[name=submit]");
        
        $('#code-img').click(function() {
            $(this).attr("src", '/account/showImage?random='+Math.random());
        });

        btn_submit.click(function() {
            var checkSubmit = true;
            div_result.html("");
            if ($.trim(input_account.val()) == "") {
                div_result.html("用戶名不能空");
                checkSubmit = false;
            } else if ($.trim(input_password.val()) == "") {
                div_result.html("密码不能空");
                checkSubmit = false;
            } else if ($.trim(input_valid.val()) == "") {
                div_result.html("验证码不能空");
                checkSubmit = false;
            }
            return checkSubmit;
        })
    })

</script>

