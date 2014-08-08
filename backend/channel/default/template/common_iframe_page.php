<!DOCTYPE html>
<html lang="zh_CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>273 BACKEND</title>
    <script type="text/javascript" charset="utf-8" src="http://sta.273.cn/cgi/ganji_sta.php?file=ganji,js/util/jquery/jquery-1.7.2.js,app/backend/js/backend.js,app/backend/css/bootstrap.css&configDir=<?php echo $this->channelInfo['sta_config_dir']; ?>"></script>
</head>
<body>
    <?php $this->load($this->fileBody); ?>
<script type="text/javascript">
GJ.use('app/backend/js/backend.js', function () {
    var backendObj = GJ.app.backend();
    var cache = [], talkToParentLoaded = false;
    GJ.remote = function (method) {
        var args = Array.prototype.slice.call(arguments);
        if (talkToParentLoaded) {
            GJ.talkToParent.callParentHandler.apply(GJ.talkToParent, args);
        } else {
            cache.push(args);
        }
    }
    GJ.use('js/util/iframe/talk_to_parent.js', function () {
        talkToParentLoaded = true;
        GJ.each(cache, function (action) {
            GJ.talkToParent.callParentHandler.apply(GJ.talkToParent, action);
        });
    });
    backendObj.run();
});
</script>
</body>
</html>