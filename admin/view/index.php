<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>安心客后台系统</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="http://sta.anxin365.com/app/lte/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="http://sta.anxin365.com/app/lte/css/ionicons.css" rel="stylesheet" type="text/css" />
    <!-- Morris chart -->
    <link href="http://sta.anxin365.com/app/lte/css/morris/morris.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="http://sta.anxin365.com/app/lte/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="skin-blue">
<!-- header logo: style can be found in header.less -->
<header class="header"> <a href="index.html" class="logo">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        安心客后台系统 </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-user"></i> <span>缪石乾 <i class="caret"></i></span> </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-blue"> <img src="http://sta.anxin365.com/app/lte/img/avatar3.png" class="img-circle" alt="User Image" />
                            <p>  - php研发工程师 <small>网络中心-经纪业务产品部</small> </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-4 text-center" style="padding:0"> <a href="#">个人信息</a> </div>
                            <div class="col-xs-4 text-center" style="padding:0"> <a href="#">修改密码</a> </div>
                            <div class="col-xs-4 text-center"  style="padding:0"> <a href="#">修改头像</a> </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right"> <a href="#" class="btn btn-default btn-flat">退出</a> </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<div class="wrapper row-offcanvas row-offcanvas-left">
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image"> <img src="http://sta.anxin365.com/app/lte/img/avatar3.png" class="img-circle" alt="User Image" /> </div>
                <div class="pull-left info">
                    <p>Hello</p>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="搜索..."/>
          <span class="input-group-btn">
          <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span> </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <?php if (!empty($this->menu)) {?>
                <ul class="sidebar-menu">
                <?php foreach($this->menu as $menu) {?>
                    <?php if(!empty($menu)){ ?>
                        <li <?php echo isset($menu['active']) ? 'class="active"' : (isset($menu['sub_menu']) ? 'class="treeview"' : '') ?>>
                            <a href="<?php echo $menu['url']?>"> <i class="fa fa-bar-chart-o"></i> <span><?php echo $menu['name']?></span> </a>
                            <?php if(!empty($menu['sub_menu'])){?>
                                <ul class="treeview-menu">
                                <?php foreach($menu['sub_menu'] as $subMenu) {?>
                                    <li><a href="<?php echo $subMenu['url']?>"><i class="fa fa-angle-double-right"></i><?php echo $subMenu['name']?></a></li>
                                <?php } ?>
                                </ul>
                            <?php }?>
                        </li>
                    <?php } ?>
                <?php }?>
                </ul>
            <?php } ?>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side">
        <h1>欢迎来到BC后台</h1>
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->
</body>
</html>
