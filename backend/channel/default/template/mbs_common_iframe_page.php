<!DOCTYPE html>
<html lang="zh_CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>273 BACKEND</title>
    <?php echo $this->commonStaHtml;?>
    <link href="http://static.273.cn/app/mbs/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="http://static.273.cn/app/mbs/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="http://static.273.cn/app/mbs/css/operationmanage.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <?php $this->load($this->fileBody); ?>
<script type="text/javascript">
G.use(['app/backend/js/backend.js'], function (Backend) {
    Backend.run();
});
</script>
</body>
</html>
