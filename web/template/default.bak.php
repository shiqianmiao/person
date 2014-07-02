<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>首页-个人主页</title>
  <?php echo $this->staHtml;?>
</head>

<body>
<div style="text-align:center;clear:both;">
</div>
  <nav>
  <ul>
    <li>
      <a href="#link-001">Home</a>
<!--
      <ul>
        <li><a href="#link-001-1">Subelement 1.1</a></li>
        <li><a href="#link-001-2">Subelement 1.2</a></li>
        <li><a href="#link-001-3">Subelement 1.3</a></li>
        <li><a href="#link-001-4">Subelement 1.4</a></li>
      </ul>
-->
    </li>
    <li>
      <a href="#link-002">About Me</a>
    </li>
    <li>
      <a href="#link-003">日记</a>
      <ul>
        <li><a href="#link-003-1">公开日记</a></li>
        <li><a href="#link-003-2">私密日记</a></li>
      </ul>
    </li>
    <li>
      <a href="#link-004">相册</a>
      <ul>
        <li><a href="#link-004-1">公开相册</a></li>
        <li><a href="#link-004-2">私密相册</a></li>
      </ul>
    </li>
    <li>
      <a href="#link-005">冷故事</a>
    </li>
    <li>
      <a href="#link-005">留言板</a>
    </li>
    <li>
        <a href="#">Web艺术</a>
    </li>
  </ul>
</nav>


<div id="page">
  <div id="container" class="content clearfix">
    <div class="focusWrap">
      <div class="focusCon" id="j_Focus">
      <div class="focusL">
        <ul class="ulFocus" id="j_FocusNav">
          <li rel="0">
            <span>酷狗7正式版发布<br/>音乐魔力全线绽放</span>
          </li>

          <li rel="1">
            <span>海量免费高清MV<br/>更清晰更流畅</span>
          </li>
          <li rel="2">
            <span>酷狗7:音乐魔方<br/>带给你全新播放体验</span>
          </li>
          <li rel="3">

            <span>个性化歌曲推荐<br/>酷狗猜你喜欢</span>
          </li>
          <li rel="4">
            <span>酷狗收音机<br/>重新认识电台魅力</span>
          </li>
          <li rel="5">
            <span>手机酷狗3.0<br/>无线音乐新序章</span>

          </li>
        </ul>
        <div class="back" id="j_FocusBack"></div>
      </div>
      <div class="focusM">
        <ul class="ulFCon" id="j_FocusCon">
          <li>
            <a href="#">
              <img src="images/1.jpg" alt="酷狗7正式版发布" />

            </a>
          </li>
          <li>
            <a href="#">
              <img src="images/2.jpg" alt="海量免费高清MV" />
            </a>
          </li>
          <li>
            <a href="#">
              <img src="images/3.jpg" alt="酷狗7:音乐魔方" />
            </a>
          </li>
          <li>
            <a href="#">
              <img src="images/4.jpg" alt="个性化歌曲推荐" />
            </a>
          </li>
          <li>

            <a href="#">
              <img src="images/5.jpg" alt="酷狗收音机" />
            </a>
          </li>
          <li>
            <a href="#">
              <img src="images/6.jpg" alt="手机酷狗3.0" />
            </a>
          </li>

        </ul>
      </div>
      </div>
      
    </div>
  </div>
 </div>
</div>

<script type="text/javascript">
  G.use(['app/person/web/js/default.js'], function(Default) {
      Default.init();
  });
</script>
</body>

</html>
