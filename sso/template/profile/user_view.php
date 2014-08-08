<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>个人设置</title>
<?php echo $this->fetch('common/header_code.php')?>
</head>

<body>
<div id="head">
   	<h1><strong><a href="#"><img src="http://static.273.cn/app/v3/css/images/logo.gif" /></a></strong><span>个人设置</span></h1>
</div>
    
	<div id="main">
    	<div id="setting"><!--个人设置-->
        	<div class="form1">
            	<div class="title1"><h3>个人资料编辑</h3></div>
				    <form method="post" name="userViewForm" action="/profile/userView/" autocomplete="off">
                        <ul class="u1">
                        	<li class="clearfix">
                            	<label><i class="m2"></i> 真实姓名：</label><input class="input1" disabled maxlength="20" value="<?php echo $this->userInfo['real_name'];?>" />
                                <input type="hidden" name="real_name" value="<?php echo $this->userInfo['real_name']; ?>" />
                            </li>
                        	<li class="clearfix">
                            	<label>E-mail：</label><input class="input1" name="email" maxlength="100" value="<?php echo $this->userInfo['email'];?>" />
                            </li>
                        	<li class="clearfix">
                            	<label><i class="m2">*</i> 移动电话：</label><input class="input1" name="mobile" maxlength="12" value="<?php echo $this->userInfo['mobile'];?>" />
                            </li>
                        	<li class="clearfix">
                            	<label>家庭电话：</label><input class="input1" name="telephone" maxlength="13" value="<?php echo $this->userInfo['telephone'];?>" />
                            </li>
                        </ul>
                        <input type="hidden" name="old_mobile" value="<?php echo $this->userInfo['mobile']; ?>" />
                        <div class="btn1 clearfix"><input type="submit" class="button1" name="submit" value="保　存" />
                            <?php echo $this->fetch('common/result_msg.php'); ?>
                        </div>
                </form>
            </div>
            
            <div></div>
            
            <div class="form1">
            	<div class="title1"><h3>信息安全设置</h3></div>
                <ul class="u1">
                	<li class="clearfix">
                    	<label>修改密码：</label><div class="input_content1">************ <a href="/profile/changePwd/" target="_blank" class="link1">[修改]</a></div>
                    </li>
                	<li class="clearfix">
                    	<label>验证手机：</label>
                    	<div class="input_content1">
                        	<?php if (isset($this->userInfo['bind_mobile'])) {
                                echo $this->userInfo['enc_bind_mobile'];
                            ?>
                            <a href="/profile/changeBindMobile/" target="_blank" class="link1">[修改]</a>
                            <?php } else {?>
                            未绑定 <a href="/ImproveSave/onlyBindMobile/?type=fromview" target="_blank" class="link1">[绑定手机]</a>
                            <?php }?>
                        </div>
                    </li>
                </ul>
            </div>
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
        //profile个人资料页面js
       var $userViewForm = $('form[name=userViewForm]');
        if ($userViewForm.length == 1) {
            var a = new FormValidator($userViewForm);
            a.addErrorMsgBox('#errorMsg',{afterInput:true});
            a.addCheckProject('mobile', 'empty', '请您输入移动电话', {isStop:false});
            a.addCheckProject('email', 'grep', '您输入邮箱格式有误', {isStop:false, grep:'isEmail', isValidateNull:false});
            a.addCheckProject('mobile', 'grep', '您输入手机号码格式有误', {isStop:false, grep:'isMobile', isValidateNull:false});
            a.checkSubmit();
        }
        //5秒后消除提示
        $tip = $('div.btn1 div.msg');
        if ($tip.length != 0) {
            var timer;
            clearInterval(timer);
            var fn = function(){
                $tip.remove();
            };
            timer = setInterval(fn, 3000);
        }
    });
</script>
</body>
</html>