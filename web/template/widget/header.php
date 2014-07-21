<div id="templatemo_header">
    <div id="header_left">   
        <div id="site_title">
            <a href="http://www.miaosq.com/" target="_parent"><img src="http://sta.miaosq.com/app/person/web/img/templatemo_logo.png" alt="logo" /></a>
        </div>
    </div>

    <div id="templatemo_menu">   
        <ul class="js-menu">
            <li><a href="/" <?php if ($this->pageType == 'default') {?>class="current"<?php }?>>家园主页</a></li>
            <li><a href="/resume/" <?php if ($this->pageType == 'resume') {?>class="current"<?php }?>>关于园主</a></li>
            <li><a href="/blog/" <?php if ($this->pageType == 'blog') {?>class="current"<?php }?>>WEB记事本</a></li>
            <li><a href="/photo/" <?php if ($this->pageType == 'photo') {?>class="current"<?php }?>>相册</a></li>
            <li><a href="/todo/" <?php if ($this->pageType == 'todo') {?>class="current"<?php }?>>TODO列表</a></li>
            <li><a href="/weblog/" <?php if ($this->pageType == 'weblog') {?>class="current"<?php }?>>网站日志</a></li>
        </ul>     
    </div>

    <div class="cleaner"></div>  
</div>