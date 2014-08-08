<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提高帐户安全_绑定手机号</title>
<?php echo $this->fetch('common/header_code.php')?>
</head>

<body>
<div id="head">
   	<h1><strong><a href="#"><img src="http://static.273.cn/app/v3/css/images/logo.gif" /></a></strong><span>帐户安全设置</span></h1>
</div>
    
	<div id="main">
    	<div id="improve_safe2">
        	<div class="step3 s3_2">
            	<ol class="clearfix">
                	<li>重置密码</li>
                	<li>绑定手机号</li>
                	<li>完成</li>
                </ol>
            </div>
            <form method="post" class="form1" name="bindMobileForm" action="/ImproveSave/bindMobile/" autocomplete="off">
                <ul class="u1">
                	<li class="clear-margin">
                    	<label>绑定的手机号：</label>
                    	<input class="input1"  name="bind_mobile" maxlength="12" placeholder="绑定手机号码，用于找回密码" value="<?php echo $this->userInfo['mobile']?>" />
                    </li>
                    <li class="clearfix">
                        <div class="num"><a href="#" id="sms_btn" class="link_code">免费获取短信验证码</a></div>
                    </li>
                    <li class="clearfix">
                    	<label>短信验证码：</label><input class="input1 input_short" maxlength="6" name="sms_code" />
                    </li>
                </ul>
                <input type="hidden" name="type" value="<?php echo $this->type; ?>" />
                <div class="btn1 clearfix">
                    <input type="submit" class="button1" name="submit" value="确　定" />
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
    G.use(['jquery', 'app/v3/js/profile/profile.js', 'app/v3/js/profile/sms.js'], function($, FormValidator, smsModule){
        //2.ImproveSave绑定手机页面js
        var $bindMobileForm = $('form[name=bindMobileForm]');
         if ($bindMobileForm.length == 1) {
            //发送短信按钮
           var $smsBtn = $('#sms_btn');
            $smsBtn.bind('click',{form:$bindMobileForm, btn:$smsBtn, url:'/ImproveSave/ajaxSendSMS/'}, smsModule.sendSMS);
            //submit事件
           var a = new FormValidator($bindMobileForm);
            a.addErrorMsgBox('#errorMsg',{afterInput:true});
            a.addCheckProject('bind_mobile', 'empty', '请您输入需要绑定的手机号码', {isStop:false, bind:'blur'});
            a.addCheckProject('bind_mobile', 'grep', '您输入手机号码格式有误', {isStop:false, grep:'isMobile', bind:'blur'});
            a.addCheckProject('sms_code', 'empty', '请您输入短信验证码', {isStop:false});
            a.addCheckProject(['bind_mobile', 'sms_code'], 'ajax', ['验证码错误'], {select:[1], url:'/profile/ajaxCheckCode/'});
            a.checkSubmit();
        }
    });
</script>
</body>
</html>
