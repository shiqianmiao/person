<?PHP

require_once dirname(__FILE__) . '/../config/config.inc.php';

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
    您要查看的信息不存在或已删除
</div>
<script type="text/javascript">
GJ.use("js/util/iframe/talk_to_parent.js", function () {
    GJ.talkToParent.callParentHandler.apply(GJ.talkToParent, ["resize", 300, 150]);
});
</script>
</body>
</html>
';

ResponseUtil::output($content);
