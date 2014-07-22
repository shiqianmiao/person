<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>【缪石乾】个人主页_优秀个人网站_缪石乾个人网站</title>
    <meta name="keywords" content="个人网站,miaoshiqian, shiqianmiao, 缪石乾, 优秀个人网站" />
    <meta name="description" content="缪石乾的个人网站" />
    <?php echo $this->commonStaHtml;?>
    <?php echo $this->staHtml;?>
</head>

<body> 

<div id="header">
    <?php $this->load('widget/header.php');?>
</div>

<div id="templatemo_wrapper">
    
    <div id="content_wrapper">
      
        <div id="content">
            
                <div class="grid">
                    <figure class="effect-sarah">
                        <img src="http://sta.miaosq.com/app/flatui/images/demo/browser-pic-1.jpg" alt="img13"/>
                        <figcaption>
                            <h2>Free <span>Sarah</span></h2>
                            <p>Sarah likes to watch clouds. She's quite depressed.</p>
                            <a href="http://www.lanrentuku.com/" target="_blank">View more</a>
                        </figcaption>           
                    </figure>

                    <figure class="effect-sarah">
                        <img src="http://sta.miaosq.com/app/flatui/images/demo/browser-pic-1.jpg" alt="img13"/>
                        <figcaption>
                            <h2>Free <span>Sarah</span></h2>
                            <p>Sarah likes to watch clouds. She's quite depressed.</p>
                            <a href="http://www.lanrentuku.com/" target="_blank">View more</a>
                        </figcaption>           
                    </figure>
                </div>
            
      </div>
        
        <div class="cleaner"></div>
    </div>

</div> <!-- end of template wrapper -->

<?php echo $this->load('widget/footer.php');?>

<script type="text/javascript">
    G.use(['app/flatui/js/backend_v2.cmb.js']);
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
