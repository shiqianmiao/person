<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>【缪石乾】个人主页_优秀个人网站_缪石乾个人网站</title>
    <meta name="keywords" content="个人网站,miaoshiqian, shiqianmiao, 缪石乾, 优秀个人网站" />
    <meta name="description" content="缪石乾的个人网站" />
    <?php echo $this->staHtml;?>
</head>

<body> 
<div id="templatemo_wrapper">

    <?php $this->load('widget/header.php');?>
    
    <?php $this->load('widget/menu.php');?>
    
    <div id="content_wrapper">
      
        <div id="content">

            <p><em>我？是谁？为了方便仇家找上门来，今儿个就在这里做个简单的介绍。<br/>
	               我就像一片落叶，随风飘荡，忽东忽西,或许在这里只是瞬间的停靠，片刻的停留，风起了又要继续漂浮......<br/>
            </em> </p>
            <h3>TODO列表</h3>
            <p>别偷懒哦，要及时完成任务哦！！</p>
            <ul class="templatemo_list col_w260 float_l">
                <li><a data-action-type="confirm" href="javascript:;">未完成的任务一</a></li>
                <li><a href="#">未完成的任务一</a></li>
                <li><a href="#">未完成的任务一</a></li>
            </ul> 
            
            <ul class="templatemo_list col_w260 float_l">
                <li><a href="#">未完成的任务一</a></li>
                <li><a href="#">未完成的任务一</a></li>
                <li><a href="#">未完成的任务一</a></li>
            </ul>
          <div class="cleaner"></div>
            <div class="button float_r"><a href="services.html">更多TODO</a></div>
            
            <div class="cleaner_h20"></div>
            
            <h3>网络相册</h3> 
             <p>Etiam in lorem ac nulla imperdiet sodales. Phasellus in justo at lectus pulvinar ultrices. Mauris ultrices sollicitudin turpis. Fusce non lacus et lorem ornare interdum. Nunc eget urna. Mauris vestibulum felis eget tortor. Praesent mattis varius quam.</p>
             
<div id="gallery_home">
                <ul>
                
                    <li>
                        <a href="http://sta.miaosq.com/app/person/web/img/gallery/image_01_b.jpg" class="pirobox" title="Project 1"><img src="http://sta.miaosq.com/app/person/web/img/gallery/image_01.jpg" alt="" /></a>
                    </li>
                    <li>
                        <a href="http://sta.miaosq.com/app/person/web/img/gallery/image_02_b.jpg" class="pirobox" title="Project 1"><img src="http://sta.miaosq.com/app/person/web/img/gallery/image_02.jpg" alt="" /></a>
                    </li>
                    <li class="last">
                        <a href="http://sta.miaosq.com/app/person/web/img/gallery/image_03_b.jpg" class="pirobox" title="Project 1"><img src="http://sta.miaosq.com/app/person/web/img/gallery/image_03.jpg" alt="" /></a>
                    </li>
                    
                    </ul>
                </div>
            
                <div class="cleaner_h30"></div>
            <div class="button float_r"><a href="gallery.html">View All</a></div>
            
      </div>
        
        <div id="sidebar_wrapper">
          <div id="sidebar_top"></div>
            <div id="sidebar">
                <h3>最新记事</h3>
                   <div class="sb_news_box">  
                        <img src="http://sta.miaosq.com/app/person/web/img/templatemo_image_01.jpg" alt="image" />
                        <div class="nb_right">
                            <a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit dictumst.</a>
                        </div>
                        <div class="cleaner"></div>
                    </div>
                    
                    <div class="sb_news_box">
                        <img src="http://sta.miaosq.com/app/person/web/img/templatemo_image_02.jpg" alt="image" />
                        <div class="nb_right">
                            <a href="#">Praesent sed est urna, id adipiscing nunc. Etiam laoreet ante quis.</a>
                        </div>
                        <div class="cleaner"></div>
                    </div>
                    
                    <div class="sb_news_box">
                        <img src="http://sta.miaosq.com/app/person/web/img/templatemo_image_03.jpg" alt="image" />
                        <div class="nb_right">
                            <a href="#">Cras bibendum, mi vitae pharetra tincidunt sed ullamcorper mauris</a>
                        </div>
                        <div class="cleaner"></div>
                    </div>
                    
                    <div class="button float_r"><a href="#">查看更多</a></div>
                    <div class="cleaner_h30"></div>

                <h3>记事分类</h3>
                <ul class="templatemo_list col_w260">
                  <li><a href="#">PHP学习笔记</a></li>
                    <li><a href="#">Javascript笔记</a></li>
                    <li><a href="#">生活趣事</a></li>
                    <li><a href="#">服务器相关</a></li>
                </ul>
            </div>
            <div id="sidebar_bottom"></div>
        </div>
        <div class="cleaner"></div>
    </div>

    <?php echo $this->load('widget/footer.php');?>
     
</div> <!-- end of template wrapper -->

<script type="text/javascript">
    //G.use(['app/flatui/js/backend_v2.cmb.js']);
    G.use(['app/person/web/js/default.js'], function(Default) {
        Default.init();
    });
    /*G.use(['app/backend/js/backend.js', 'widget/dialog/dialog.js', 'jquery'], function (Backend, Dialog, $) {
        Backend.run();
        Backend.on("confirm", function(e){
            console.log('ff mm');         
        });
        var dialog = new Dialog ({
            title : 'title',
            content : 'hello everyone',
            skin : 'blue',
        });
    });*/
</script>
</body>
</html>
