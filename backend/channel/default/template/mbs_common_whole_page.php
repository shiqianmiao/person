<!DOCTYPE html>
<html lang="zh_CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>后台系统</title>
    <?php echo $this->commonStaHtml;?>
</head>

<body>
    <div class="topnav clearfix">
        <div class="fl ml10">
            <span class="topnav-user"><a data-action-type="dept_name_click" href="javascript:;"><?php echo $this->userInfo['user']['real_name'];?> (<?php echo $this->deptInfo['dept_name'];?>)<i></i></a></span>
            <span class="ml10"><a href="<?php echo $this->logoutUrl; ?>?refer=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST']);?>">退出</a></span>
        </div>
        
        <div class="setting_nume" style="display:none;">
            <ul>
                <li><a target="_blank" href="http://sso.corp.273.cn/profile/userView/">个人设置</a></li>
                <li><a target="_blank" href="<?php echo $this->changePwdUrl; ?>">修改密码</a></li>
            </ul>
        </div>
        
        <div class="fr mr10">
            <span><i></i><a href="http://v.youku.com/v_show/id_XNjMzODQ3MDgw.html" target="_blank">一路阳光MV</a></span>
            <span><i class="app"></i><a href="/mbs_index/showPhone" target="_blank">手机业管</a></span>
            <span><a href="<?php echo $this->videoUrl; ?>" title="" target="_blank">培训视频</a></span>
            <span><a href="<?php echo $this->commulicationUrl; ?>" title="" target="_blank">内部论坛</a></span>
            <span><a href="http://shop107160827.taobao.com" title="" target="_blank">物资申领</a></span>
            <span><a href="http://273.21tb.com" target="_blank">在线学习</a></span>
            <span class="wtfk"><a href="/mbs_post/carListOp/addFeed/" target="_blank">投诉反馈</a></span>
        </div>
    </div>

    <div class="navbar" id="navbar" name="navbar">
        <div class="container-fluid">
            <div style="display:none">
            <button type="button" class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button> 
            </div>
            <a class="logo" href="/mbs_index/showIndex/">273业务管理系统</a>
            <span class="localname"></span>
            <div class="nav-collapse1" style="display:none"></div>
            <div class="nav-collapse1">
                <ul class="nav navigation">
                    <?php foreach ($this->channels as $channel => $channelInfo) {?>
                        <li <?php if ($this->channelInfo['code'] == $channelInfo['code']){?>class="active"<?php }?>>
                            <a href="/<?php echo $channel; ?>/"><?php echo $channelInfo['text']; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            
            <!--
            <div class="nav-collapse">
                <div class="btn-group userexit-group">
                    <button class="btn dropdown-toggle" data-toggle="dropdown"  data-widget="dropdown"><i class="i-user"></i><?php echo $this->userInfo['user']['account']; ?><span class="icon-chevron-down"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href=""><i class="icon-off"></i>修改密码</a></li>
                        <li><a href=""><i class="icon-off"></i>Logout</a></li>
                    </ul>
                </div>
                <button class="btn btn-yjfk" data-action-type="feed"><i class="i-yjfk"></i>意见反馈</button>
            </div>
            -->
        </div>
    </div>

<div class="container-fluid containers">
    <div class="row-fluid">
        <!--sidebar-->
        <div data-widget="sidebar" data-source="<?php echo htmlspecialchars(json_encode($this->menu)); ?>" id="sidebar" class="sidebar">
            <div id="caidan" class="clearfix">
                <div class="clearfix">
                    <?php if (empty($this->mbsAppModule)) {?>
                          <a href="/mbs_index/showIndex" style="margin-left:35px;font-size:12px;font-weight:bold;text-decoration:none;color:#205484;"><i></i>业管首页</a>
                    <?php }?>
                </div>
                <ul class="nav nav-list" id="menu">
                </ul>
            </div>
        </div>
        <!--/sidebar-->
        <!-- 是否显示菜单判断 -->
        <?php 
            $showNoMueu = false;
            if($this->showNoMenu) {
                $showNoMueu = true;
            } 
        ?>

        <div class="container-fluid">
            <div class='row-fluid' id="main">
                <div class="main">
                <?php $this->load($this->fileBody); ?>
                </div>
            </div>
            <div id='alerts-container'></div>
        </div>
    </div>
</div>
<style>
.tipsbox{ border:1px solid #ff7300; background:#fef6df; font-size:12px; color:#000; position:relative;}
.tipsw250{ width:250px;}
.tipsw200{ width:200px;}
.tipsclose{ background:url(http://att.273.com.cn/ms/crm_tipsico.gif) -28px 0; width:10px; height:10px; overflow:hidden; text-indent:-999em; position:absolute; right:10px; top:10px}
.tipsclose a{ display:block;width:10px; height:10px;}
.tipsclose a:hover{background:url(http://att.273.com.cn/ms/crm_tipsico.gif) -38px 0; }
.tipstxt{padding:10px; line-height:20px;}
.tipstxt h4{ font-size:18px; padding:10px 0 10px; margin:0; text-align:center;}
.tipstxt p{ margin:0; padding:0; text-align:center;}
.tipsico1,.tipsico2,.tipsico3{width:14px; height:8px; overflow:hidden; position:absolute; background-image:url(http://att.273.com.cn/ms/crm_tipsico.gif); background-position:0 0; }
.tipsico1{ right:25px; top:-8px}
.tipsico2{ left:25px; top:-8px}
.tipsico3{ background-position:-14px 0; left:25px; bottom:-8px}
.tipsbtn{ background:#FF7E53; height:24px; text-align:center; line-height:24px; color:#fff; margin:0 10px 10px;}
.tipsbtn a,.tipsbtn2 a{color:#fff; text-decoration:none; display:block; height:24px;}
.tipsbtn a:hover,.tipsbtn2 a:hover{text-decoration:underline;}
.tipsbtn2{ background:#FF7E53; height:24px; text-align:center; line-height:24px; color:#fff; margin:0 10px 10px; width:100px; float:right;}
.tipsform{ width:100px; float:left; padding:3px 0 0 10px; vertical-align:middle;}
.tipsform input{ float:left}
.tipsinfo label{ float:left;font-size:12px;}
.clear{ clear:both; height:0px; line-height:1px; font-size:0; overflow:hidden;}
</style>
<script type="text/javascript">

G.use(['app/backend/js/backend.js', 'jquery', 'util/cookie.js'], function (Backend, $, Cookie) {
    Backend.run();

    Backend.on('dept_name_click', function() {
        $('.setting_nume').toggle();
    });
});

/*G.use(['util/log_v2.js', 'jquery', 'util/uuid_v2.js'], function (Log, $, Uuid) {
    var log = new Log({eqsch:'mbs/'} || {});

    log.bindTrackEvent();

    // uuid 检测
    setTimeout(function () {
        Uuid.detect();
    }, 1000);
});*/
</script>

</body>
</html>
