<?PHP

require_once dirname(__FILE__) . '/../config/config.inc.php';

$msg = RequestUtil::getGET('errorMessage', '您要查看的信息不存在或已删除');
if ($msg == 'low_agent') {
    $content = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>浏览器版本太低</title>
<style type="text/css">
    html,body{height:100%;overflow:hidden;margin:0;}
	.content{margin:auto;max-width: 700px;padding: 0 20px;position: relative;}
	.pic{background:url(http://static.273.cn/app/mbs/img/pageno.jpg) no-repeat;max-width:640px;height:260px;margin: auto;-webkit-background-size: contain;
        -moz-background-size: contain;-o-background-size: contain;background-size: contain;}
	.info h3{color:#4a4a4a;font:24px "Microsoft Yahei",Tahoma,Helvetica;margin:0 0 10px;}
	.info p{color:#4a4a4a;margin:0;padding: 5px 0;font-size: 14px;}
	.info a{display:inline-block;height:30px;line-height:30px;margin-right:30px;padding-left:30px;color:#194fb0;text-decoration:none;background:url(img/browser.png) no-repeat 0 0;cursor: pointer;}
	.info a:hover{text-decoration:underline;}
	.info a.a2{background-position:0 -30px;}
	.info a.a3{background-position:0 -60px;}
</style>
</head>
<body>
	<div class="content" style="top:15%">
		<div class="pic"></div>
		<div class="info">
			<h3>Hi~您的浏览器版本太低啦~ 业务管理系统已经不支持IE6/IE7了！</h3>
			<p>请升级浏览器：<a href="http://chrome.360.cn" class="a2">360浏览器</a><a href="http://www.google.cn/intl/zh-CN/chrome/" class="a3">Chrome浏览器</a></p>
		</div>
	</div>
</body>
</html>';
} else {
    $content = '
<!DOCTYPE html>
<html lang="zh_CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>错误提示 - 273后台管理系统</title>
<script type="text/javascript" charset="utf-8" src="http://sta.273.cn/cgi/ganji_sta.php?file=ganji,js/util/jquery/jquery-1.7.2.js"></script>
</head>
<body>
<div style="height:150px;line-height:150px;text-align:center">
    '.$msg.'<br>
<a href="javascript:if(window.opener){window.close()}else {history.go(-1)};">返回</a>
<a href="http://sso.corp.273.cn/account/logout/?refer=' . urlencode('http://' . $_SERVER['HTTP_HOST']) . '">退出</a>
</div>
</body>
</html>
    ';
} //javascript:history.go(-1);
ResponseUtil::output($content);
