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
    	<div id="password_modify3">
        	<div class="step2 s2_3">
            	<ol class="clearfix">
                	<li>输入用户名</li>
                	<li>安全验证</li>
                	<li>输入新密码</li>
                	<li>完成</li>
                </ol>
            </div>
            <form method="post" class="form1" name="backPwdForm" action="/BackPwd/backPwd/" autocomplete="off">
                <ul class="u1">
                	<li class="clearfix">
                    	<label>输入新密码：</label><input type="password" name="passwd" class="input1" placeholder="请输入您要设置的新密码" /><div class="msg m4"><i class="i1"></i>密码必须包含字母和数字的组合，长度8～16位，字母区分大小写</div>
                    </li>
                    <li class="clearfix">
                    	<label>再次输入新密码：</label><input type="password" name="notPasswd" class="input1" />
                    </li>
                </ul>
                <input type="hidden" name="username" value="<?php echo $this->userInfo['username']; ?>" />
                <input type="hidden" name="opCode" value="<?php echo $this->opCode; ?>" />
                <div class="btn1 clearfix"><input type="submit" class="button1" name="submit" value="下一步" />
                <?php echo $this->fetch('common/result_msg.php'); ?>
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
    G.use(['jquery', 'app/v3/js/profile/profile.js'], function($, FormValidator){
        //backPwd 3找回密码输入新密码js
       var $backPwdForm = $('form[name=backPwdForm]');
        if ($backPwdForm.length == 1) {
            var a = new FormValidator($backPwdForm);
            a.addErrorMsgBox('#errorMsg',{afterInput:true});
            a.addCheckProject('passwd', 'empty', '请输入新密码', {isStop:false, bind:'blur'});
            a.addCheckProject('newPasswd', 'grep', '密码中不能含有空格', {isStop:false, grep:'isNotBlank', bind:'blur'});
            a.addCheckProject('passwd', 'length', '正确的密码长度为8～16位', {isStop:false, minLength:8, maxLength:16, bind:'blur'});
            a.addCheckProject('passwd', 'match', '密码必须包含字母和数字的组合', {isStop:false, charMode:[3, 5], bind:'blur'});
            a.addCheckProject('notPasswd', 'empty', '请您再次输入新密码', {isStop:false, bind:'blur'});
            a.addCheckProject('notPasswd', 'equal', '两次密码输入不一致', {isStop:false, compareNum:'passwd', bind:'blur', showCorrect:true});
            a.checkSubmit();
        }
    });
</script>
</body>
</html>
