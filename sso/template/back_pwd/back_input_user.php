<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>找回密码_输入用户名</title>
<?php echo $this->fetch('common/header_code.php')?>
</head>

<body>
<div id="head">
   	<h1><strong><a href="#"><img src="http://static.273.cn/app/v3/css/images/logo.gif" /></a></strong><span>找回密码</span></h1>
</div>
    
	<div id="main">
    	<div id="password_modify1">
        	<div class="step2 s1">
            	<ol class="clearfix">
                	<li>输入用户名</li>
                	<li>安全验证</li>
                	<li>输入新密码</li>
                	<li>完成</li>
                </ol>
            </div>
            <form method="post" class="form1" name="inputUserForm" action="/BackPwd/inputUser/" autocomplete="off">
            <ul class="u1">
            	<li class="clearfix">
                	<label>账号名：</label><input name="username" class="input1" maxlength="12" placeholder="请填写您要找回的账号" />
                	<?php if ($this->resultMsg['username']) {
                	    echo $this->fetch('common/result_msg.php');
                    } ?>
            	</li>
            	<li id="validate_code" class="clearfix">
                	<label>验证码：</label><input name="code" maxlength="4" class="input1 input_short" />
                	<div class="img1">
                    	<a href="#"><img src="/BackPwd/showImg/" /></a>
                    </div>
                	<?php if ($this->resultMsg['code']) {
                	    echo $this->fetch('common/result_msg.php');
                	} ?>
                </li>
            </ul>
            
            <div class="btn1 clearfix"><input type="submit" class="button1" name="submit" value="下一步" />
                <span>下一步将进行安全验证</span>
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
    G.use(['jquery', 'app/v3/js/profile/profile.js', 'app/v3/js/profile/md5.js'], function($, FormValidator){
         //backPwd 1.找回密码输入用户名页面js
        var $inputUserForm = $('form[name=inputUserForm]');
         if ($inputUserForm.length == 1) {
            var a = new FormValidator($inputUserForm);
            a.addErrorMsgBox('#errorMsg',{afterInput:true});
            a.addCheckProject('username', 'empty', '请您输入用户名', {isStop:false});
            a.addCheckProject('code', 'empty', '请您输入验证码', {isStop:false});
            a.addCheckProject('code', 'equal', '验证码错误，请重新输入验证码', {compareNum:'back_code',isNeedMd5:true,isformCookie:true});
            a.checkSubmit();
        }
        
        var $validateCode = $('#validate_code');
        var $img = $validateCode.find('img');
        var $a = $validateCode.find('a');
        $a.on('click', function() {
            $img[0].src = '/BackPwd/showImg/'+Math.random();
        });
    })
</script>
</body>
</html>