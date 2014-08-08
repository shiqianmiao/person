<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>找回密码_安全验证</title>
<?php echo $this->fetch('common/header_code.php')?>
</head>

<body>
<div id="head">
   	<h1><strong><a href="#"><img src="http://static.273.cn/app/v3/css/images/logo.gif" /></a></strong><span>找回密码</span></h1>
</div>
    
	<div id="main">
    	<div id="password_modify2">
        	<div class="step2 s2_2">
            	<ol class="clearfix">
                	<li>输入用户名</li>
                	<li>安全验证</li>
                	<li>输入新密码</li>
                	<li>完成</li>
                </ol>
            </div>
            <form method="post" class="form1" name="backSaveCheckForm" action="/BackPwd/backSaveCheck/" autocomplete="off">
                <ul class="u1">
                	<li class="clearfix">
                    	<label>已绑定手机：</label> <div class="input_content1"><?php echo $this->userInfo['enc_bind_mobile']; ?></div><div class="msg"><i class="i1"></i>如果当前号码已不可用/丢失，或无法收到验证码？<strong>请联系各区域运营经理</strong></div>
                        <div class="num"><a href="#" id="sms_btn" class="link_code">免费获取短信验证码</a></div>
                    </li>
                    <li class="clearfix">
                    	<label>短信验证码：</label><input name="sms_code" maxlength="6" class="input1 input_short" />
                        <?php echo $this->fetch('common/result_msg.php'); ?>
                    </li>
                </ul>
                <input type="hidden" name="username" value="<?php echo $this->userInfo['username']; ?>" />
                <div class="btn1 clearfix"><input type="submit" class="button1" name="submit" value="确定" />
                    <span>下一步输入新密码</span>
                </div>
            </form>
        </div>
    </div>
    
	<div id="foot">
    	<p>
        	Copyright &copy; 1998 - 2013 273.CN. All Rights Reserved <br />
            273二手车交易网 版权所有
        </p>
    </div>
<script>
    G.use(['jquery', 'app/v3/js/profile/profile.js', 'app/v3/js/profile/sms.js'], function($, FormValidator, smsModule){
        //backPwd 2找回密码安全验证js
       var $backSaveCheckForm = $('form[name=backSaveCheckForm]');
        if ($backSaveCheckForm.length == 1) {
            var $smsBtn = $('#sms_btn');
            $smsBtn.bind('click',{form:$backSaveCheckForm, btn:$smsBtn, url:'/BackPwd/ajaxSendSMS/'}, smsModule.sendSMS);
            
            var a = new FormValidator($backSaveCheckForm);
            a.addErrorMsgBox('#errorMsg',{afterInput:true});
            a.addCheckProject(['username', 'sms_code'], 'ajax', ['验证码错误'], {select:[1], url:'/BackPwd/ajaxCheckCode/'});
            a.addCheckProject('sms_code', 'empty', '请您输入短信验证码', {isStop:false});
            a.checkSubmit();
        }
    });
</script>
</body>
</html>
