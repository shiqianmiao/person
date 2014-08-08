<!DOCTYPE html>
<html lang="zh_CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>273业管后台系统</title>
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
                <li <?php if (empty($this->mbsAppModule)){?>class="active"<?php }?>>
                    <a href="/">业务管理</a>
                </li>
                <?php if ($this->deptInfo['dept_type'] == 1 || $this->deptInfo['id'] == 261) {?>
                <li <?php if (!empty($this->mbsAppModule) && $this->mbsAppModule == 'shop'){?>class="active"<?php }?>>
                    <a href="/shop/showIndex/">门店管理</a>
                </li>
                <?php }?>
                <?php if (isset($this->userInfo['role']['shop.secretary']) ||  isset($this->userInfo['role']['shop.manager']) || isset($this->userInfo['role']['shop.salesman']) || isset($this->userInfo['role']['shop.salesleader'])) { ?>
                <li style="position: relative;">
                    <a data-action-type="price-evaluate-index" href="/mbs_post/pubPost/iframePriceEvaluation?from=root" id="evaluate_btn">车价评估</a>
                    <span style="display:none">
                    <a href="#" data-eqselog="/mbs@etype=click@mbs=evaluate_main" id="do_evaluate_main_click"></a>
                    </span>
                </li>
                <?php } ?>
            </ul>
            <?php if (!$this->isInfoUser) {?>
            <form id="search_form" action="/mbs_index/Search/advancedSearch" method="get">
            <div class="fr mt10">
                <div class="input-prepend mt10"> 
                    <div class="btn-group">  
                    <input type="hidden" name="info_type" value="<?php if (RequestUtil::getGET('info_type') == 2) { ?>2<?php } else { ?>1<?php } ?>">
                        <button id="btn_type" class="btn dropdown-toggle" data-toggle="dropdown"  data-widget="dropdown">  
                          <?php if (RequestUtil::getGET('info_type') == 2) { ?>买车<?php } else { ?>卖车<?php }?>  
                          <span class="caret"></span>  
                        </button>  
                        <ul class="dropdown-menu selects">  
                          <li class="ml10 active" style="cursor: pointer" data-value="1" data-op="info_type">卖车</li>
                          <li class="ml10" style="cursor: pointer" data-value="2" data-op="info_type">买车</li>
                        </ul>
                    </div>
                    <input class="span2" id="appendedDropdownButton" type="text" name="keyword" value="<?php echo RequestUtil::getGET('keyword');?>" placeholder="分机号/信息编号/关键字">
                    <button class="btn btn-success ml10" id="btn_search" type="submit">搜索</button>
                </div>
            </div>
            </form>
            <?php }?>
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
            <?php if (!$this->isInfoUser){?>
            <div class="caidan-sousuo clearfix">
                <span class="on nav_caidan"><a data-action-type="navCaidan" href="javascript:;">菜单</a></span>
                <span class="nav_search"><a data-action-type="navSearch" href="javascript:;">搜索</a></span>
            </div>
            <?php }?>
            <div id="caidan" class="clearfix">
                <?php if (defined('PERMISSION_APP_NAME') && PERMISSION_APP_NAME == 'mbs.bc') { ?>
                <div class="clearfix ml10 mr10"><a target="_blank" href="/mbs_post/pubPost" class="buycar span6">发布卖车</a><a target="_blank" href="/mbs_post/pubBuyPost" class="buycar span6">发布买车</a></div>
                <?php } ?>   
            <div class="clearfix">
            
            <?php if (empty($this->mbsAppModule)) {?>
            <a href="/mbs_index/showIndex" style="margin-left:35px;font-size:12px;font-weight:bold;text-decoration:none;color:#205484;"><i></i>业管首页</a>
            <?php }?>
            </div>
            <ul class="nav nav-list" id="menu">
            </ul>
            </div>
            
            <div id="sousuo" class="clearfix sidebar-search" style="display: none;">
                <form class="form-horizontal mt10"  id="advance_search_form" action="/mbs_index/Search/advancedSearch" method="get">

                    <div class="clearfix mt10">
                        <div class="thelabel">关键字：</div>
                        <div class="theforms">
                            <input type="text" name="keyword" value="<?php echo RequestUtil::getGET('keyword'); ?>" class="span11"/>
                        </div>
                    </div>

                    <div class="clearfix mt10">
                        <div class="thelabel">类型：</div>
                        <div class="theforms">
                            <p style="margin-top: 3px;">
                                <label class="span5"><input name="info_type" type="radio" value="1" checked /> 卖车信息</label> 
                                <label class="span5"><input name="info_type" type="radio" value="2" <?php if (RequestUtil::getGET('info_type') == 2) { ?>checked<?php } ?> /> 买车信息</label>
                            </p>
                        </div>
                    </div>
                    
                    <div class="clearfix">
                        <div class="thelabel">车类型：</div>
                         <div class="theforms">
                             <div>
                                <select class="span11 province_city" id="type_field" name="vehicle_type" data-type="2" data-text="不限品牌">
                                    <option value="0">不限类型</option>
                                    <?php foreach(Search::$TYPE_NAME as $value => $name){?>
                                        <option value="<?php echo $value;?>"  <?php if (RequestUtil::getGET('vehicle_type') == $value) { ?>selected<?php } ?>><?php echo $name;?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="mt10">
                                <select class="span11 province_city" id="brand_field" name="vehicle_brand" data-type="3" data-text="不限车系">
                                    <option value="0">不限品牌</option>
                                                            
                                </select>
                            </div>
                            <div class="mt10">
                                <select class="span11 province_city" id="series_field" name="vehicle_series" data-type="4" data-text="不限车型">
                                    <option value="0">不限车系</option>
                                                            
                                </select>
                            </div>
                            <div class="mt10">
                                <select class="span11" id="model_field" name="vehicle_model">
                                    <option value="0">不限车型</option>
                                                              
                                </select>
                            </div>
                                              
                         </div>
                    </div>
                    
                    <div class="clearfix mt10">
                        <div class="thelabel">变速箱：</div>
                        <div class="theforms">
                            <p style="margin-top: 3px;">
                                <?php foreach (Search::$GEARBOX_TYPE as $value => $type) { ?>
                                <label class="span3"><input name="gearbox_type" type="radio" value="<?php echo $value;?>" <?php if (RequestUtil::getGET('gearbox_type') == $value) { ?>checked<?php } ?> /> <?php echo $type; ?></label> 
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="clearfix">
                        <div class="thelabel">价格：</div>
                        <div class="theforms">
                            <input type="text"  name="min_price" value="<?php if(is_numeric(RequestUtil::getGET('min_price')) && is_numeric(RequestUtil::getGET('max_price')) && RequestUtil::getGET('min_price') > RequestUtil::getGET('max_price')) { echo RequestUtil::getGET('max_price');} else { echo RequestUtil::getGET('min_price');} ?>" class="fl span5 wanyuan"/>
                            <span class="fl text-center" style="width:11%;">-</span>
                            <input type="text"  name="max_price" value="<?php if(is_numeric(RequestUtil::getGET('min_price')) && is_numeric(RequestUtil::getGET('max_price')) && RequestUtil::getGET('min_price') > RequestUtil::getGET('max_price')) { echo RequestUtil::getGET('min_price');} else { echo RequestUtil::getGET('max_price');} ?>" class="fl span5 wanyuan"/>
                        </div>
                    </div>
                    
                    <div class="clearfix mt10">
                        <div class="thelabel">上牌时间：</div>
                        <div class="theforms">
                            <select name="min_age" class="span5 fl">
                                <option value="">年份</option>
                                <?php $oldYear = 1990; $year = date('Y'); for($i=$year; $i>=$oldYear; $i--) { ?>
                                <option value="<?php echo $i;?>" <?php if (RequestUtil::getGET('min_age') && RequestUtil::getGET('max_age') && (RequestUtil::getGET('min_age') > RequestUtil::getGET('max_age')) && ($i == RequestUtil::getGET('max_age'))) {?>selected<?php } elseif ((RequestUtil::getGET('min_age') < RequestUtil::getGET('max_age')) && (RequestUtil::getGET('min_age') == $i)) {?>selected<?php } elseif (RequestUtil::getGET('min_age') && !RequestUtil::getGET('max_age') && RequestUtil::getGET('min_age') == $i) {?>selected<?php } elseif ((RequestUtil::getGET('min_age') == RequestUtil::getGET('max_age')) && (RequestUtil::getGET('min_age') == $i)) {?>selected<?php }?>><?php echo $i;?></option>
                                <?php } ?>
                            </select>
                            <span class="fl text-center" style="width:11%;">-</span> 
                            <select name="max_age" class="span5">
                                <option value="">年份</option>
                                <?php $oldYear = 1990; $year = date('Y'); for($i=$year; $i>=$oldYear; $i--) { ?>
                                <option value="<?php echo $i;?>" <?php if (RequestUtil::getGET('min_age') && RequestUtil::getGET('max_age') && (RequestUtil::getGET('min_age') > RequestUtil::getGET('max_age')) && ($i == RequestUtil::getGET('min_age'))) {?>selected<?php } elseif ((RequestUtil::getGET('min_age') < RequestUtil::getGET('max_age')) && (RequestUtil::getGET('max_age') == $i)) {?>selected<?php } elseif (RequestUtil::getGET('max_age') && !RequestUtil::getGET('min_age') && RequestUtil::getGET('max_age') == $i) {?>selected<?php } elseif ((RequestUtil::getGET('min_age') == RequestUtil::getGET('max_age')) && (RequestUtil::getGET('max_age') == $i)) {?>selected<?php }?>><?php echo $i;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="clearfix mt10">
                        <div class="thelabel">交易地：</div>
                        <div class="theforms">
                            <select class="span5 province_city" id="pro" name="deal_province" data-type="1" data-text = '城市' >
                                <option value="">省份</option>
                                <?php foreach($this->provinceList as $letter => $lePro){?>
                                    <?php foreach($lePro as $pro){?>
                                        <option value="<?php echo $pro['id'];?>" <?php if (RequestUtil::getGET('deal_province') == $pro['id']) { ?>selected<?php } else if (defined('PERMISSION_APP_NAME') && PERMISSION_APP_NAME == 'mbs.bc' && (count($_GET) <= 7) && ($this->userInfo['user']['province'] == $pro['id'])) { ?>selected<?php } ?>><?php echo strtoUpper($letter) . ' ' . $pro['name'];?></option>
                                    <?php }?>
                                <?php }?>
                            </select>

                            <select class="span5" id="city_field" name="deal_city" >
                            <option value="">城市</option>
                                               
                             </select>
                        </div>
                    </div>
                    
                    <div class="clearfix mt10">
                        <div class="thelabel">颜色：</div>
                        <div class="theforms">
                            <select name="color" class="span5">
                                <option value="0">不限</option>
                                <?php foreach (Search::$COLOR_TEXT_ALL as $key => $value) { ?>
                                <option <?php if (RequestUtil::getGET('color') == $key) {?>selected<?php }?> value="<?php echo $key;?>" style="background-color:<?php echo $value['color']; ?>;color:<?php $value['bg'];?>"><?php echo $value['caption'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="clearfix mt10">
                        <div class="thelabel">归属：</div>
                        <div class="theforms">
                            <p>
                            <select class="span5" name="belong">
                                <?php foreach (Search::$SEARCH_CAR_BELONG as $value => $belong) {?>
                                    <option value="<?php echo $value; ?>" <?php if (RequestUtil::getGET('belong') == $value) {?>selected<?php }?>><?php echo $belong; ?></option>
                                <?php } ?>
                            </select>
                             <?php if ($this->userInfo['user']['role_id'] == 265) {?>
                            <button class="btn" data-title='选择人员所在部门' data-action-type="selectGrouper" data-width='250' data-height='250' data-href="/mbs_index/Search/iframeAreaDept?dept_id=<?php echo $this->deptInfo['id'];?>">选择归属人</button>
                            <?php } else {?>
                            <button id="btn_select_user" class="btn" data-title='选择人员' data-action-type="selectGrouper" data-width='250' data-height='250' data-href="/mbs_index/Search/iframeGroupMembers?dept_id=<?php echo $this->deptInfo['id'];?>">选择归属人</button>
                            <?php }?>
                            <input type="hidden" id="user_list" name="user_list" value="<?php echo RequestUtil::getGET('user_list'); ?>">
                            <input type="hidden" id="user_list_name" name="user_list_name" value="<?php echo RequestUtil::getGET('user_list_name'); ?>">
                            </p>
                            <p id="belong_user"></p>
                        </div>
                    </div>
                    
                    <div class="clearfix">
                        <div class="thelabel">车牌：</div>
                        <div class="theforms">
                            <select class="span5 province_city" id="plate_pro" name="plate_province" data-type="5" data-text = '城市' >
                                <option value="">省份</option>
                                <?php foreach($this->provinceList as $letter => $lePro){?>
                                    <?php foreach($lePro as $pro){?>
                                        <option value="<?php echo $pro['id'];?>" <?php if (RequestUtil::getGET('plate_province') == $pro['id']) { ?>selected<?php } ?>><?php echo strtoUpper($letter) . ' ' . $pro['name'];?></option>
                                    <?php }?>
                                <?php }?>
                            </select>
                            <select class="span5" id="city_field2" name="plate_city">
                                <option value="">城市</option>
                                               
                             </select>
                        </div>
                    </div>

                    <div class="clearfix mt10" style="width:65%;margin:auto;">
                        <button type="submit" id="btn_advance" class="btn btn-success span12">搜索</button>
                    </div>

                    <div class="clearfix mt10"  style="width:70%;margin:auto;">
                        <!-- <button type="submit" class="btn ml10 fl">保存搜索条件</button> -->
                        <span class="ml10" style="margin-top:4px;"><a data-action-type="reset" href='javascript:;'>清空条件</a></span>
                    </div>

                </form>
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

<div id="star_tips" style="position: absolute; display:none;z-index: 1900;">
    <div class="tipsbox tipsw250">
        <div class="tipsico2"></div>
        <div class="tipsclose"><a href="javascript:void(0)" title="关闭">关闭</a></div>
        <div class="tipstxt">
            <p style="text-align: left;padding-left:5px;display:inline-block;color:#b19325;"&#b19325;"&gt;&#b19325;"&gt;&lt;strong style="font-size:14px;">新功能！</strong>
            </br>您的门店还未设置服务之星,请设置！
            </p>
        </div>
        <div class="tipsbtn2 tipsclose_know" style="cursor:pointer;">我知道了</div>
        <div class="clear"></div>
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
G.use(['uploader'], function () {
    G.use(['jquery', 'util/cookie.js'], function ($, Cookie) {
        $minPrice = '<?php echo RequestUtil::getGET('min_price');?>'
        $maxPrice = '<?php echo RequestUtil::getGET('max_price');?>'
        if ($minPrice) {
            $('input[name="min_price"]').removeClass('wanyuan');
        }
        if ($maxPrice) {
            $('input[name="max_price"]').removeClass('wanyuan');
        }

        var preval = '';
        $('input[name="min_price"], input[name="max_price"]').keyup(function() {
            var $this = $(this);
            var $priceVal = $.trim($this.val());
            if ($priceVal) {
                $this.removeClass('wanyuan');
            } else {
                $this.addClass('wanyuan');
            }
            if (isNaN($priceVal)) {
//                 $value = $priceVal.replace(/[^\d\.]/g, '');
                $this.val(preval);
            } else {
                preval = $priceVal;
            }
        });
        
        var searchUrl = '<?php echo $this->searchPathUrl;?>';
        var searchAuctionUrl = '/auction/sessionCar/searchAuctionList';
        var pathname = document.location.pathname;
        if (pathname == searchUrl || pathname == searchAuctionUrl) {
            $('.nav_search').addClass('on');
            $('.nav_caidan').removeClass('on');
            $('#sousuo').show();
            $('#caidan').hide();
        }

        var $proId = $('#pro').val();
        var $plateProId = $('#plate_pro').val();
        if ($proId) {   //获取交易城市列表
            var type1 = $('#pro').data('type');
            var $getCity = '<?php echo RequestUtil::getGET('deal_city');?>';
            var $getParams = location.search;
            paramsArr = $getParams.substr(1).split('&');
            $paramsLen = paramsArr.length;
            var $userCity = '<?php echo $this->userInfo['user']['city'];?>';    //当前帐号所在城市
            var $appName = '<?php echo PERMISSION_APP_NAME?>'; //区分总部或业管后台
            $.ajax({
                url : "/mbs_index/Search/ajaxGetProCity",
                type : "POST",
                dataType : "json",
                data : {
                    type: type1,
                    id: $proId,
                },
                success : function(data) {
                    var html = '', item;
                    html += '<option value="0">城市</option>';
                    for(item in data) {
                        html += '<option value="' + item + '"';
                        if ($appName == 'mbs.bc' && $getParams && $paramsLen < 6 && item == $userCity) {    //业管后台
                            html += ' selected';
                        } else if (!$getParams && $appName == 'mbs.bc' && item == $userCity) {
                            html += ' selected';
                        } else if (item == $getCity) {
                            html += ' selected';
                        }
                        html += '>' + data[item] + '</option>';
                    }
                    $('#city_field').html(html).show();
                },
                error : function() {
                    Backend.trigger('alert-error', "数据获取异常");
                }
            });
        }
        if ($plateProId) {  //获取车牌城市列表
            var type2 = $('#plate_pro').data('type');
            var $getPlateCity = '<?php echo RequestUtil::getGET('plate_city');?>';
            $.ajax({
                url : "/mbs_index/Search/ajaxGetProCity",
                type : "POST",
                dataType : "json",
                data : {
                    type: type2,
                    id: $plateProId,
                },
                success : function(data) {
                    var html = '', item;
                    html += '<option value="0">城市</option>';
                    for(item in data) {
                        html += '<option value="' + item + '"';
                        if (item == $getPlateCity) {
                            html += ' selected';
                        }
                        html += '>' + data[item] + '</option>';
                    }
                    $('#city_field2').html(html).show();
                },
                error : function() {
                    Backend.trigger('alert-error', "数据获取异常");
                }
            });
        }
        var $typeId = '<?php echo RequestUtil::getGET('vehicle_type');?>';;
        var $getBrand = '<?php echo RequestUtil::getGET('vehicle_brand');?>';
        var $getSeries = '<?php echo RequestUtil::getGET('vehicle_series');?>';
        var $getModel = '<?php echo RequestUtil::getGET('vehicle_model');?>';
        if ($typeId) {  //获取品牌列表
            var type3 = $('#type_field').data('type');
            $.ajax({
                url : "/mbs_index/Search/ajaxGetProCity",
                type : "POST",
                dataType : "json",
                data : {
                    type: type3,
                    id: $typeId,
                },
                success : function(data) {
                    var html_vehicle = '', item;
                    html_vehicle += '<option value="0">不限品牌</option>';
                    for(item in data) {
                        html_vehicle += '<option value="' + data[item] + '"';
                        if (data[item] == $getBrand) {
                            html_vehicle += ' selected';
                        }
                        html_vehicle += '>' + item + '</option>';
                    }
                    $('#brand_field').html(html_vehicle).show();
                },
                error : function() {
                    Backend.trigger('alert-error', "数据获取异常");
                }
            });
        }
        if ($typeId && $getBrand) {  //获取车系列表
            var type4 = $('#brand_field').data('type');
            $.ajax({
                url : "/mbs_index/Search/ajaxGetProCity",
                type : "POST",
                dataType : "json",
                data : {
                    type: type4,
                    id: $getBrand,
                    type_id: $typeId,
                },
                success : function(data) {
                    var html_vehicle = '', item;
                    html_vehicle += '<option value="0">不限车系</option>';
                    for(item in data) {
                        html_vehicle += '<option value="' + data[item] + '"';
                        if (data[item] == $getSeries) {
                            html_vehicle += ' selected';
                        }
                        html_vehicle += '>' + item + '</option>';
                    }
                    $('#series_field').html(html_vehicle).show();
                },
                error : function() {
                    Backend.trigger('alert-error', "数据获取异常");
                }
            });
        }
        if ($typeId && $getSeries) {  //获取车型列表
            var type4 = $('#series_field').data('type');
            $.ajax({
                url : "/mbs_index/Search/ajaxGetProCity",
                type : "POST",
                dataType : "json",
                data : {
                    type: type4,
                    id: $getSeries,
                    type_id: $typeId,
                },
                success : function(data) {
                    var html_vehicle = '', item;
                    html_vehicle += '<option value="0">不限车型</option>';
                    for(item in data) {
                        html_vehicle += '<option value="' + data[item] + '"';
                        if (data[item] == $getModel) {
                            html_vehicle += ' selected';
                        }
                        html_vehicle += '>' + item + '</option>';
                    }
                    $('#model_field').html(html_vehicle).show();
                },
                error : function() {
                    Backend.trigger('alert-error', "数据获取异常");
                }
            });
        }

        var $userList = '<?php echo RequestUtil::getGET('user_list');?>';
        var $userListName = '<?php echo RequestUtil::getGET('user_list_name');?>';
        if ($userList) {
            html_user = '';
            $userList = $userList.replace(/(^,)|(,$)/g, '');
            $userListName = $userListName.replace(/(^,)|(,$)/g, '');
            $userArr = $userList.split(',');
            $userNameArr = $userListName.split(',');
            for (i in $userArr) {
                html_user += '<span class="tagsdel">' + $userNameArr[i] + '<em data-action-type="deleteUser" data-value="' + $userArr[i] + '" data-name="' + $userNameArr[i] + '">X</em></span>';
            }
            $('#belong_user').html(html_user).show();
        }
               
        $('#search_form').keydown(function(event){
            if(event.keyCode ==13) {
                $('#btn_type').attr("disabled", true);
                $('#btn_search').click();
            }
        });

        $('#advance_search_form').keydown(function(event){
            if(event.keyCode ==13) {
                $('#btn_select_user').attr("disabled", true);
                $('#btn_advance').click();
            }
        });
        
        $('.selects').find('li').click(function(){
            $(this).addClass('active').siblings('li').removeClass('active');
            var value = $(this).data('value');
            var op = $(this).data('op');
            if (value == 1) {
                $('#btn_type').html('卖车 <span class="caret"></span>');
            } else {
                $('#btn_type').html('买车 <span class="caret"></span>');
            }
            if (op == 'info_type') {
                $('input[name="info_type"]').val(value);
            } 
        });
        
        /*
        //服务之星全局的tip提示,放于店铺管理边上
        var show_star_tip = function() {
            setTimeout(function () {
                $windowHeight = $("#shop_maner_menu").offset().top;
                $windowWidth  = $("#shop_maner_menu").offset().left;
                $("#star_tips").show();
                $("#star_tips").css({
                    "top":  $windowHeight + 15,
                    "left": $windowWidth - 12,
                    "display":'block'
                });
            }, 1500);
        }
        
        $('#star_tips')
        .on('click', '.tipsclose', function (e) {
            $("#star_tips").hide();
        })
        .on('click', '.tipsclose_know', function (e) {
            $("#star_tips").hide();
        });
        
        <?php if ($this->showStarTips) {?>
            show_star_tip();
        <?php }?>
        */
    });
});

G.use(['app/backend/js/backend.js', 'jquery', 'util/cookie.js'], function (Backend, $, Cookie) {
    Backend.run();
    $('.province_city').change(function() {
        var id = $(this).val();
        var type = $(this).data('type');
        var text = $(this).data('text');
        var type_id = $('#type_field').val();
        var brand_id = $('#brand_field').val();
        if (type == 2) {
            $('#series_field').html('<option value="0">不限车系</option>').show();
            $('#model_field').html('<option value="0">不限车型</option>').show();
        } else if (type == 3) {
            $('#model_field').html('<option value="0">不限车型</option>').show();
        }
        $.ajax({
            url : "/mbs_index/Search/ajaxGetProCity",
            type : "POST",
            dataType : "json",
            data : {
                type: type,
                id: id,
                type_id: type_id,
                brand_id: brand_id
            },
            success : function(data) {
                var html = '', item, html_vehicle = '';
                html += '<option value="0">' + text + '</option>';
                html_vehicle += '<option value="0">' + text + '</option>';
                for(item in data) {
                    if (type == 2 || type == 3 || type == 4) {
                        html_vehicle += '<option value="' + data[item] + '">' + item + '</option>';
                    } else {
                        html += '<option value="' + item + '">' + data[item] + '</option>';
                    }                    
                }
                if (type == 1) {
                    $('#city_field').html(html).show();
                } else if (type == 2) {
                    $('#brand_field').html(html_vehicle).show();
                } else if (type == 3) {
                    $('#series_field').html(html_vehicle).show();
                } else if (type == 4) {
                    $('#model_field').html(html_vehicle).show();
                } else if (type == 5) {
                    $('#city_field2').html(html).show();
                }
            },
            error : function() {
                Backend.trigger('alert-error', "数据获取异常");
            }
        });
    });
    
    Backend.on("navCaidan", function() {
        $('.nav_caidan').addClass('on');
        $('.nav_search').removeClass('on');
        $('#caidan').show();
        $('#sousuo').hide();
    });
    Backend.on("navSearch", function() {
        $('.nav_search').addClass('on');
        $('.nav_caidan').removeClass('on');
        $('#sousuo').show();
        $('#caidan').hide();
    });
    Backend.on("deleteUser", function() {
        $userListObj = $('input[name="user_list"]');
        $oldValue = $userListObj.val();
        $userListNameObj = $('input[name="user_list_name"]');
        $nameOldVal = $userListNameObj.val();
        $val = $(this).data('value');
        $nameVal = $(this).data('name');
        $(this).parent().remove();
        if ($oldValue.indexOf($val+',') >= 0) {   
            $newValue = $oldValue.replace($val+',', '');
        } else {
            $newValue = $oldValue.replace($val, '');
        }
        if ($nameOldVal.indexOf($nameVal+',') >= 0) {
            $newNameVal = $nameOldVal.replace($nameVal+',', '');
        } else {
            $newNameVal = $nameOldVal.replace($nameVal, '');
        }
        $userListObj.val($newValue);
        $userListNameObj.val($newNameVal);
    });
    Backend.on("reset", function() {
        $(':input','#advance_search_form')  
        .not(':button, :submit, :reset')  
        .val('')  
        .removeAttr('checked')  
        .removeAttr('selected');
        $('input[name=info_type]').val(1);
        $('input:radio[name=info_type]')[0].checked = true;
        $('input:radio[name=gearbox_type]')[0].checked = true;
        $('#belong_user').html('').show();
        <?php if (defined('PERMISSION_APP_NAME') && PERMISSION_APP_NAME == 'mbs.bc') {?>
        $('#pro').val('<?php echo $this->userInfo['user']['province']?>');
        var $myPro = '<?php echo $this->userInfo['user']['province']?>';
        var $myCity = '<?php echo $this->userInfo['user']['city']?>';
        $.ajax({
            url : "/mbs_index/Search/ajaxGetProCity",
            type : "POST",
            dataType : "json",
            data : {
                type: 1,
                id: $myPro,
            },
            success : function(data) {
                var html = '', item;
                html += '<option value="0">城市</option>';
                for(item in data) {
                    html += '<option value="' + item + '"';
                    if (item == $myCity) {
                        html += ' selected';
                    }
                    html += '>' + data[item] + '</option>';
                }
                $('#city_field').html(html).show();
            },
            error : function() {
                Backend.trigger('alert-error', "数据获取异常");
            }
        });
        <?php } ?>
    });

    Backend.on("selectGrouper", function(e) {
        var url = $(this).data('href');
        var title = $(this).data('title');
        var width = $(this).data('width');
        var height = $(this).data('height');
        e.preventDefault();
        G.use(['jquery', 'widget/dialog/dialog.js'], function ($, Dialog) {
            var dialog = new Dialog({
                title : title,
                width : width,
                height: height,
                close : function() {
                    Backend.widget('#datagrid-1').ready(function () {
                        this.load();
                    });
                }
            });
            dialog.ready(function () {
                this.open(url);
            });

            window.dialog = dialog;
        });
    });
    
    Backend.on('dept_name_click', function() {
        $('.setting_nume').toggle();
    });

    Backend.on("price-evaluate-index", function(e){
        e.preventDefault();
        var url = this.href;
        var title = "车价评估";
        var width = 890;
        var height = 550;
        G.use(['jquery', 'widget/dialog/dialog.js'], function ($, Dialog) {
            var dialog = new Dialog({
                title : title,
                width : width,
                height: height

            });
            dialog.ready(function () {
                this.open(url);
            });
        });
    });
    
    $('#evaluate_btn').click(function() {
        $('#do_evaluate_main_click').click();
    });
});

G.use(['util/log_v2.js', 'jquery', 'util/uuid_v2.js'], function (Log, $, Uuid) {
    var log = new Log({eqsch:'mbs/'} || {});

    log.bindTrackEvent();

    // uuid 检测
    setTimeout(function () {
        Uuid.detect();
    }, 1000);
});
</script>

</body>
</html>
