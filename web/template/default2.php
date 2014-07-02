<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>miaoshiqian's home page!</title>
        <?php echo $this->staHtml;?>
        <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!--[if IE 6]>
        <script src="js/belatedPNG.js"></script>
        <script>
        	DD_belatedPNG.fix('*');
        </script>
        <![endif]-->
    </head>

    <body>
        <div id="wrap">
            <section id="top">
                <nav id="mainnav">
                    <h1 id="sitename" class="logotext">
                        <a href="#">乐家园</a>
                    </h1>
                    <ul>
                        <li class="active"><a href="index.html"><span>家园主页</span></a></li>
                        <li><a href="photos.html"><span>园主档案</span></a></li>
                        <li><a href="blog.html"><span>博客日志</span></a></li>
                        <li><a href="form.html"><span>访客留言</span></a></li>
                        <li><a href="form.html"><span>图册</span></a></li>
                    </ul>
                </nav>
            </section>

            <section id="page">
                <header id="pageheader" class="homeheader">
                    <div class="fl">
                        <img id="headpic" width="212" height="150" src="http://<?php echo STATIC_DOMAIN;?>/app/person/web/attr/images/head_pic.jpg"/>

                        <div style="background-color:#fff;padding:0px;margin-left:10px;width:227px;height:90px;">
                            <iframe name="weather_inc" src="http://tianqi.xixik.com/cframe/7" style="border:solid 1px #7ec8ea" width="225" height="90" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>
                        </div>
                    </div>

                    <div id="introduce" class="fr">
                        <table cellspacing="10">
                            <tbody>
                                <tr><td class="r">姓名：</td><td class="l">缪石乾</td>   <td class="r">性别：</td><td class="l">男</td></tr>
                                <tr><td class="r">民族：</td><td class="l">汉 &nbsp;&nbsp;&nbsp;族</td>   <td class="r">婚姻状况：</td><td class="l">否</td></tr>
                                <tr><td class="r">学历：</td><td class="l">本 &nbsp;&nbsp;&nbsp;科</td>   <td class="r">专业：</td><td class="l">计算机科学</td></tr>
                                <tr><td class="r">籍贯：</td><td class="l">福 &nbsp;&nbsp;&nbsp;建</td>   <td class="r">政治面貌：</td><td class="l">党 &nbsp;&nbsp;&nbsp;员</td></tr>
                                <tr><td class="r">生日：</td><td class="l">90.11.10</td>   <td class="r">星座：</td><td class="l">魔羯座</td></tr>
                                <tr><td class="r">兴趣爱好：</td><td colspan="3" class="l">乒乓球 游戏 旅游 摄影。</td></tr>
                                <tr><td class="r">工作内容：</td><td colspan="3" class="l">Web向光技术研发。</td></tr>
                                <tr><td class="r">座右铭：</td><td colspan="3" class="l">不要抱怨，要么闭嘴，要么改变自己 。</td></tr>
                            </tbody>
                        </table>
                    </div>
                </header>

                <section id="contents">
                    <article class="post">
                        <header class="postheader">
                            <h2><a href="#">Blog Post Title</a></h2>
                            <p class="postinfo">Published on <time>18 August, 2010</time> under <a href="#">CSS Templates </a></p>
                        </header>

                        <p>Lorem ipsum dolor sit amet. Phasellus varius nulla vel metus facilisis congue. Phasellus sed velit ipsum. Morbi suscipit ipsum vel tellus pulvinar aliquam. Aliquam non dui vel magna mollis molestie a nec quam.<a href="#"> Read More</a></p>

                        <footer class="postfooter">
                            <ul>
                                <li class="plink"><a href="#">Permalink</a></li>
                                <li class="cment"><a href="#">10 Comments</a></li>
                            </ul>
                        </footer>
                    </article>

                    <article class="post">
                        <header class="postheader">
                            <h2><a href="#">Blog Post Title</a></h2>
                            <p class="postinfo">Published on <time>18 August, 2010</time> under <a href="#">CSS Templates </a></p>
                        </header>

                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus varius nulla vel metus facilisis congue. Phasellus sed velit ipsum. Morbi suscipit ipsum vel tellus pulvinar aliquam. Aliquam non dui vel magna mollis molestie a nec quam.<a href="#"> Read More</a></p>
                        
                        <footer class="postfooter">
                            <ul>
                                <li class="plink"><a href="#">Permalink</a></li>
                                <li class="cment"><a href="#">10 Comments</a></li>
                            </ul>
                        </footer>
                    </article>
                </section>

                <!--right parts-->
                <section id="sidebar">
                    <h2>站点日志</h2>
                    <ul>
                    	<li><a href="#nogo">Drawings</a></li>
                        <li><a href="#nogo">Illustrations</a></li>
                        <li><a href="#nogo">Digital Arts</a></li>
                        <li><a href="#nogo">Music</a></li>
                        <li><a href="#nogo">Literature</a></li>
                        <li><a href="#nogo">Web Design</a></li>
                        <li><a href="http://www.cssheaven.org">CSS Templates</a></li>

                    </ul>
                    <h2>Blogroll</h2>
                    <ul>
                    	<li><a href="#nogo">Drawings</a></li>
                        <li><a href="#nogo">Illustrations</a></li>
                        <li><a href="#nogo">Digital Arts</a></li>
                        <li><a href="#nogo">Music</a></li>
                        <li><a href="#nogo">Literature</a></li>
                        <li><a href="#nogo">Web Design</a></li>
                        <li><a href="http://www.cssheaven.org">CSS Templates</a></li>

                    </ul>
                </section>
                <div class="clear"></div>
            </section>
        </div>

        <footer id="pagefooter">
            <div id="f-content">
                <img src="http://sta.273.com.cn/app/person/web/img/bamboo.png" width="96" height="125" alt="bamboo" id="footerimg">
                <div id="social-links">
                    <a href="#" class="fblink">Facebook</a>
                    <a href="#" class="twtlink">Twitter</a>
                </div>
                <div id="credits">
                    <p class="sitecredit"> 2014 &amp; All Rights Reserved | miaoshiqian.com </p>
                </div>
            </div>
        </footer>

    </body>
</html>
