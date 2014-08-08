<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改密码</title>
<?php echo $this->fetch('common/header_code.php')?>
</head>

<body>
<div id="head">
   	<h1><strong><a href="#"><img src="http://static.273.cn/app/v3/css/images/logo.gif" /></a></strong><span>修改密码</span></h1>
</div>
    
	<div id="main">
    	<div id="password_modify5_2">
        	<form class="form1" method="post" name="resetPwdForm" action="/profile/changePwd/" autocomplete="off">
            <ul class="u1">
                <li class="clearfix">
                	<label>现在的密码：</label><input type="password" name="oldPasswd" class="input1" /><div class="msg m4"><i class="i1"></i>请输入现在的密码</div>
                </li>
            	<li class="clearfix">
                	<label>输入新密码：</label><input type="password" name="newPasswd" class="input1" /><div class="msg m4" style="display:none;"><i class="i1"></i>密码必须包含字母和数字的组合，长度8～16位，字母区分大小写</div>
                </li>
            	<li class="clearfix">
                	<label>再次输入新密码：</label><input type="password" name="notPasswd" class="input1" />
                </li>
            </ul>
            <div class="btn1 clearfix"><input name="submit" type="submit" class="button1" value="修　改" />
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
        //profile修改密码js
        var $resetPwdForm = $('form[name=resetPwdForm]');
         if ($resetPwdForm.length == 1) {
             var a = new FormValidator($resetPwdForm);
             a.addErrorMsgBox('#errorMsg',{afterInput:true});
             a.addCheckProject('oldPasswd', 'empty', '请您输入现在的密码', {isStop:false, bind:'blur'});
             a.addCheckProject('newPasswd', 'empty', '请您填写密码', {isStop:false, bind:'blur'});
             a.addCheckProject('newPasswd', 'grep', '密码中不能含有空格', {isStop:false, grep:'isNotBlank', bind:'blur'});
             a.addCheckProject('newPasswd', 'length', '正确的密码长度为8～16位', {isStop:false, minLength:8, maxLength:16, bind:'blur'});
             a.addCheckProject('newPasswd', 'match', '密码必须包含字母和数字的组合', {isStop:false, charMode:[3, 5], bind:'blur'});
             a.addCheckProject('newPasswd', 'equal', '新密码不能跟现在密码相同，请重新填写新密码', {isStop:false, compareNum:'oldPasswd', notEqual:true, bind:'blur'});
             a.addCheckProject('notPasswd', 'equal', '两次密码输入不一致', {isStop:false, compareNum:'newPasswd', bind:'blur'});
             a.addCheckProject('notPasswd', 'empty', '请您再次输入新密码', {isStop:false, bind:'blur'});
             a.addCheckProject(['oldPasswd'], 'ajax', ['密码输入有误,请重新输入'], {isStop:false, url:'/profile/ajaxcheckpwd/', preTrue:true});
             a.checkSubmit();
         }
    });
</script>
</body>
</html>
