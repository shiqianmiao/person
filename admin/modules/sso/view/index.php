<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>273业务管理系统</title>
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
        273业务管理系统 </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-user"></i> <span>杜涛 <i class="caret"></i></span> </a>
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
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> 业管首页 <small>Control panel</small> </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">业管首页</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3> 150 </h3>
                            <p> 上架车源 </p>
                        </div>
                        <div class="icon"> <i class="ion ion-bag"></i> </div>
                        <a href="#" class="small-box-footer"> 详情 <i class="fa fa-arrow-circle-right"></i> </a> </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3> 44 </h3>
                            <p> 买主信息 </p>
                        </div>
                        <div class="icon"> <i class="ion ion-person-add"></i> </div>
                        <a href="#" class="small-box-footer"> 详情 <i class="fa fa-arrow-circle-right"></i> </a> </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3> 53 </h3>
                            <p> 昨日电话量 </p>
                        </div>
                        <div class="icon"> <i class="ion ion-stats-bars"></i> </div>
                        <a href="#" class="small-box-footer"> 详情 <i class="fa fa-arrow-circle-right"></i> </a> </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                        <div class="inner"> <br />
                            <p> 昨日回访买主：20/15 </p>
                            <p style="color:#096"> 昨日回访买主：10/10 </p>
                        </div>
                        <div class="icon"> <i class="ion ion-pie-graph"></i> </div>
                        <a href="#" class="small-box-footer"> 详情 <i class="fa fa-arrow-circle-right"></i> </a> </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-7 connectedSortable">

                    <!-- Custom tabs (Charts with tabs)-->
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs pull-right">
                            <li class="active"><a href="#revenue-chart" data-toggle="tab">Line</a></li>
                            <li class="pull-left header"><i class="fa fa-inbox"></i> 一周数据总览</li>
                        </ul>
                        <div class="tab-content no-padding">
                            <!-- Morris chart - Sales -->
                            <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
                        </div>
                    </div>
                    <!-- /.nav-tabs-custom -->
                </section>
                <!-- /.Left col -->
                <!-- right col (We are only adding the ID to make the widgets sortable)-->
                <section class="col-lg-5 connectedSortable">

                    <!-- Map box -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab_1">全部资讯</a></li>
                            <li><a data-toggle="tab" href="#tab_2">喜讯</a></li>
                            <li><a data-toggle="tab" href="#tab_3">区域</a></li>
                            <li><a data-toggle="tab" href="#tab_4">督察</a></li>
                            <li class="pull-right"><a class="text-muted" href="#"><i class="fa fa-gear"></i></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab_1" class="tab-pane active">
                                <table class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td>福州紫阳服务中心 隔壁老王被列入员工黑名单</td>
                                        <td>11-7-2014</td>
                                        <td><span class="label label-success">推荐</span></td>
                                    </tr>
                                    <tr>
                                        <td>福州紫阳服务中心 隔壁老王被列入员工黑名单</td>
                                        <td>11-7-2014</td>
                                        <td><span class="label label-warning"></span></td>
                                    </tr>
                                    <tr>
                                        <td>福州紫阳服务中心 隔壁老王被列入员工黑名单</td>
                                        <td>11-7-2014</td>
                                        <td><span class="label label-primary"></span></td>
                                    </tr>
                                    <tr>
                                        <td>福州紫阳服务中心 隔壁老王被列入员工黑名单</td>
                                        <td>11-7-2014</td>
                                        <td><span class="label label-danger">热门</span></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->
                            <div id="tab_2" class="tab-pane"> 222 </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.box -->

                </section>
                <!-- right col -->
            </div>
            <!-- /.row (main row) -->

        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->
</body>
</html>
