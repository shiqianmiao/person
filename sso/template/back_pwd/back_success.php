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
    	<div id="password_modify4">
        	<div class="step2 s2_4">
            	<ol class="clearfix">
                	<li>输入用户名</li>
                	<li>安全验证</li>
                	<li>输入新密码</li>
                	<li>完成</li>
                </ol>
            </div>
        <div class="tip_box">
        	<div class="tip_box_font t1">
        		<h5><i></i><?php echo $this->resultMsg; ?></h5>
                <div  id="success_result"><a href="http://mbs.corp.273.cn/index.php?model=Main" class="link1 a1"><i>5</i>秒后自动进入业管系统</a></div>
            </div>
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
    G.use(['jquery'], function($){
        //成功页面js
        var $successBox = $('#success_result');
        if ($successBox.length == 1) {
            var timer;
            clearInterval(timer);
            var num = 5;
            var fn = function(){
                --num;
                num = num >= 0 ? num : 0;
                $successBox.find('i').html(num);
                if (num <= 0) {
                    //跳转到业管页面
                    window.location.href="http://mbs.corp.273.cn/index.php?model=Main"; 
                    clearInterval(timer);
                }
            };
            timer = setInterval(fn, 1000);
        }
    });
</script>
</body>
</html>
