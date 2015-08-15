<header class="header">
    <a href="/center/" class="logo">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        安心客后台管理系统
    </a>
    
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> 
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-user"></i> <span><?php echo $this->userInfo['user']['real_name'];?> <i class="caret"></i></span> </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-blue">
                            <img src="http://sta.anxin365.com/app/lte/img/avatar3.png" class="img-circle" alt="User Image" />
                            <p> <?php echo $this->userInfo['group_name'];?><small>努力工作、再接再厉！</small> </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-4 text-center" style="padding:0"> <a href="javascript:;">个人信息</a> </div>
                            <div class="col-xs-4 text-center" style="padding:0"> <a href="javascript:;">修改密码</a> </div>
                            <div class="col-xs-4 text-center"  style="padding:0"> <a href="javascript:;">修改头像</a> </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right"> <a href="http://<?php echo UrlConfig::TEST_BC_URL;?>/sso/user/logout" class="btn btn-default btn-flat">退出</a> </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>