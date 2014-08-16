<!DOCTYPE html>
<html lang="zh_CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>后台管理系统</title>
<script type="text/javascript" charset="utf-8" src="http://sta.273.cn/cgi/ganji_sta.php?file=ganji,js/util/jquery/jquery-1.7.2.js,app/backend/js/backend.js,app/backend/css/bootstrap.css&configDir=<?php echo $this->channelInfo['sta_config_dir']; ?>"></script>
</head>
<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="/<?php echo $this->channelInfo['url']; ?>" data-original-title=""><img src="http://sta.273.cn/src/app/backend/img/logo.png" alt="logo" id="logo"></a>
            <div class="group-menu nav-collapse" id="channel-info">
                <ul class="nav">
                    <li><?php echo $this->channelInfo['text'];?></li>
                    <li class="dropdown">
                        <div class="btn-group">
                            <a class="btn dropdown-toggle" data-widget="dropdown" data-toggle="dropdown" href="#">
                                 切换管理后台<i class="icon-chevron-down"></i>
                            </a>

                            <ul class="dropdown-menu">
                                <?php foreach ($this->channels as $channel => $channelInfo) {?>
                                <li><a href="/<?php echo $channel; ?>/"><?php echo $channelInfo['text']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
          
            <div class="group-menu nav-collapse" id="user-info"> 
                <ul class="nav pull-right">
                    <li class="divider-vertical"></li>
                    <li class="dropdown">
                        <div class="btn-group">
                            <a class="btn dropdown-toggle" data-widget="dropdown" data-toggle="dropdown" href="#">
                                 <i class="splashy-contact_blue"></i> <?php echo $this->userInfo['user']['account']; ?> <i class="icon-chevron-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <!--li><a href="#">1</a></li-->
                                <!--li class="divider"></li-->
                                <li><a href="<?php echo $this->changePwdUrl; ?>"><i class="icon-off"></i>修改密码</a></li>
                                <li><a href="<?php echo $this->logoutUrl; ?>"><i class="icon-off"></i>Logout</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div data-widget="sidebar" data-source="<?php echo htmlspecialchars(json_encode($this->menu)); ?>" id="sidebar">
    <ul id="menu"></ul>
</div>
<!-- 是否显示菜单判断 -->
<?php 
    $showNoMueu = false;
    if($this->showNoMenu) {
        $showNoMueu = true;
       } 
?>
<div class="container-fluid">
    <div class='row-fluid' id="main">
    <?php if(!$showNoMueu) {?>
        <ul class="breadcrumb" id="breadcrumb">
            <li>
                <a href="/<?php echo $this->channelInfo['url']; ?>/"><i class="icon-home"></i>首页</a>
            </li>
            <?php if (count($this->menu['active'])) { ?>
                <span class="divider">/</span>
                <li><span><?php echo $this->menu['menu'][$this->menu['active'][0]]['text']; ?></span>
                    <span class="divider">/</span></li>
                <?php if (count($this->menu['active']) == 2 ) { ?>
                    <li><?php echo $this->menu['menu'][$this->menu['active'][0]]['menu'][$this->menu['active'][1]]['text']; ?>
            <?php }
            } ?>
            <?php if($this->showAble) {?>
            <li style="float: right" >
            <span>通话音质不佳，<a data-action-type="explan">查看原因</a>；</span>
            </li>
            <li style="float: right" >
            <span>接到莫名异地电话，<a data-action-type="unknow">查看原因</a>。</span>
            </li>
            <?php }?>
        </ul>
        <?php }?>
        <?php $this->load($this->fileBody); ?>
    </div>
    <div id='alerts-container'></div>
</div>

<!-- Templates -->
<script type="text/template" charset="utf-8" id="menu-tpl">
    <li <% if (typeof active !== 'undefined') { %>class='active'<% } %> >
        <h2><a href='#'><i class="<%= icon %>"></i><%= text %></a></h2>
        <% if(menu) { %>
            <ul class="nav nav-list">
                <% _.each(menu, function (m) { %>
                    <li <% if (m.active) {%>class="active"<% } %>><a href="<%= m.url %>"><%= m.text %></a></li>
                <% }); %>
            </ul>
        <% } %>
    </li>
</script>

<script type="text/javascript" charset="utf-8">
GJ.use('app/backend/js/backend.js', function () {
    window.backendObj = GJ.app.backend();
    window.backendObj.run();
    //$(function(){if($.browser.msie&&parseInt($.browser.version,10)===6){$('.row div[class^="span"]:last-child').addClass("last-child");$('[class*="span"]').addClass("margin-left-20");$(':button[class="btn"], :reset[class="btn"], :submit[class="btn"], input[type="button"]').addClass("button-reset");$(":checkbox").addClass("input-checkbox");$('[class^="icon-"], [class*=" icon-"]').addClass("icon-sprite");$(".pagination li:first-child a").addClass("pagination-first-child")}})
});
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "http://hm.baidu.com/hm.js?51ff5d7556127e8d36b5555a3a8a1f8a";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</body>
</html>
