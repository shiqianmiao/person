<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>更换手机号码_安全验证</title>
<?php echo $this->fetch('common/header_code.php')?>
</head>

<body>
<div id="head">
   	<h1><strong><a href="#"><img src="http://static.273.cn/app/v3/css/images/logo.gif" /></a></strong><span>更换绑定手机号码</span></h1>
</div>
    
	<div id="main">
    	<div id="change_phone1">
        	<div class="step1 s1">
            	<ol class="clearfix">
                	<li>安全验证</li>
                	<li>输入新手机号</li>
                	<li>完成</li>
                </ol>
            </div>
            <form method="post" class="form1" name="changeBindMobileForm" action="/profile/changeBindMobile/" autocomplete="off">
            <ul class="u1">
            	<li class="clearfix">
                	<label>已绑定手机：</label><div class="input_content1"><?php echo $this->userInfo['enc_bind_mobile']; ?></div><div class="msg m5"><i class="i1"></i>如果当前号码已不可用/丢失，或无法收到验证码？<strong>请联系各区域运营经理</strong></div>
                    <div class="num"><a href="#" id="sms_btn" class="link_code">免费获取短信验证码</a></div>
                </li>
                <li class="clearfix">
                	<label>短信验证码：</label><input name="sms_code" maxlength="6" class="input1 input_short" />
                </li>
            </ul>
            <input type="hidden" name="opCode" value="<?php echo $this->opCode; ?>" />
            <div class="btn1 clearfix"><input type="submit" class="button1" name="submit" value="确　定" />
            <?php echo $this->fetch('common/result_msg.php');
            if (!$this->resultMsg) { ?>
            <span>下一步将输入新手机号</span></div>
            <?php }?>
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
        //profile更换绑定手机验证页面js
        var $changeBindMobileForm = $('form[name=changeBindMobileForm]');
        if ($changeBindMobileForm.length == 1) {
            var $smsBtn = $('#sms_btn');
            $smsBtn.bind('click',{form:$changeBindMobileForm, btn:$smsBtn, url:'/profile/ajaxSendSMS/'}, smsModule.sendSMS);
            
            var a = new FormValidator($changeBindMobileForm);
            a.addErrorMsgBox('#errorMsg',{afterInput:true});
            a.addCheckProject(['bind_mobile', 'sms_code'], 'ajax', ['验证码错误'], {select:[1], url:'/profile/ajaxCheckCode/'});
            a.addCheckProject('sms_code', 'empty', '请您输入短信验证码', {isStop:false});
            a.checkSubmit();
        }
    });
</script>
</body>
</html>
