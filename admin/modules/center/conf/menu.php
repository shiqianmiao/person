<?php
/**
 * Created by PhpStorm.
 * User: chansun
 * Date: 15/1/13
 * Time: 下午9:03
 */
return array(
    array(
        'name' => 'Banner管理',
        'url'  => 'javascript:;',
        'code' => 'bc.center.banner', //该菜单需要的权限. 一般为bc.xxx
        'icon' => 'fa fa-users',
        'sign'  => array( //用于菜单选中判断
            'center.banner',
        ),
        'sub_menu' => array(
            array(
                'name' => '添加Banner',
                'url'  => '/center/banner/default',
                'code' => 'center.banner.default',
                'sign' => 'center.banner.default', //用于子菜单选中判断
            ),
            array(
                'name' => 'Banner列表',
                'url'  => '/center/banner/list',
                'code' => 'center.banner.list',
                'sign' => 'center.banner.list', //用于子菜单选中判断
            ),
        ),
    ),
    array(
        'name' => '访客留言管理',
        'url'  => 'javascript:;',
        'code' => 'bc.center.customer',
        'icon' => 'fa fa-user',
        'sign' => array( //用于菜单选中判断
            'center.customer',
        ),
        'sub_menu' => array(
            array(
                'name' => '客户列表',
                'url'  => '/center/customer/default',
                'code' => 'center.customer.list',
                'sign' => 'center.customer.default', //用于子菜单选中判断
            ),
        ),
    ),
    array(
        'name' => '文章分类管理',
        'url'  => 'javascript:;',
        'code' => 'bc.center.cat',
        'icon' => 'fa fa-user',
        'sign' => array( //用于菜单选中判断
            'center.cat',
        ),
        'sub_menu' => array(
            array(
                'name' => '添加分类',
                'url'  => '/center/cat/default',
                'code' => 'center.cat.default',
                'sign' => 'center.cat.default', //用于子菜单选中判断
            ),
            array(
                'name' => '分类列表',
                'url'  => '/center/cat/list',
                'code' => 'center.cat.list',
                'sign' => 'center.cat.list', //用于子菜单选中判断
            ),
        ),
    ),
    array(
        'name' => '技术文章管理',
        'url'  => 'javascript:;',
        'code' => 'bc.center.order',
        'icon' => 'fa fa-list',
        'sign' => array( //用于识别当前菜单是否选中,用module.controller来识别,一级菜单要包括所有二级菜单的controller
            'center.order',
            'center.orderyuesao',
            'center.orderweixin',
            'center.orderlist',
            'center.orderyuesaolist',
            'center.ordercommon',
        ),
        'sub_menu' => array(
            array(
                'name' => '微信订单列表',
                'url'  => '/center/orderWeixin/default',
                'code' => 'center.orderWeixin.default',
                'sign' => array(
                    'center.orderweixin.default',
                ),
            ),
            array(
                'name' => '育儿嫂订单列表',
                'url'  => '/center/orderList/default',
                'code' => 'center.orderList.list',
                'sign' => array(
                    'center.orderlist.default',
                    'center.orderlist.detail'
                ),
            ),
            array(
                'name' => '下育儿嫂订单',
                'url'  => '/center/orderCommon/addOrderOne/?type=1',
                'code' => 'center.order.add',
                'sign' => array(
                    'center.order.default',
                    'center.order.audition',
                    'center.order.contract',
                    'center.order.trial',
                    'center.order.serving',
                    'center.order.over',
                    'center.order.match',
                ),
            ),
            array(
                'name' => '月嫂订单列表',
                'url'  => '/center/orderYuesaoList/default',
                'code' => 'center.orderList.list',
                'sign' => array(
                    'center.orderyuesaolist.default',
                    'center.orderyuesaolist.detail'
                ),
            ),
            array(
                'name' => '下月嫂订单',
                'url'  => '/center/orderCommon/addOrderOne/?type=2',
                'code' => 'center.orderYuesao.add',
                'sign' => array(
                    'center.orderyuesao.default',
                    'center.orderyuesao.audition',
                    'center.orderyuesao.contract',
                    'center.orderyuesao.trial',
                    'center.orderyuesao.serving',
                    'center.orderyuesao.over',
                    'center.orderyuesao.match',
                ),
            ),
            
            //不可见菜单的配置,可以通过指定sign来把菜单焦点交给某个可见菜单
            array(
                'name' => 'unsee',
                'url'  => '/center/order/audition',
                'code' => 'help.Article.pub',
                'sign' => 'center.order.audition',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/order/match',
                'code' => 'help.Article.pub',
                'sign' => 'center.order.match',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/order/contract',
                'code' => 'help.Article.pub',
                'sign' => 'center.order.contract',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/order/trial',
                'code' => 'help.Article.pub',
                'sign' => 'center.order.trial',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/order/serving',
                'code' => 'help.Article.pub',
                'sign' => 'center.order.serving',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/order/over',
                'code' => 'help.Article.pub',
                'sign' => 'center.order.over',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/orderList/detail',
                'code' => 'help.Article.pub',
                'sign' => 'center.orderlist.detail',
            ),
        ),
    ),
    array(
        'name' => '友情链接管理',
        'url'  => 'javascript:;',
        'code' => 'bc.center.help',
        'icon' => 'fa fa-sitemap',
        'sign'  => array(
            'center.help',
        ),
        'sub_menu' => array(
            array(
                'name' => 'App帮助中心',
                'url'  => '/center/Help/default',
                'code' => 'center.help.list',
                'sign' => array(
                    'center.help.default',
                    'center.help.articlelist',
                    'center.help.addarticle',
                    'center.help.editarticle',
                ),
            ),
            //不可见的菜单
            array(
                'name' => 'unsee',
                'url'  => '/center/help/articleList',
                'code' => 'help.Article.pub',
                'sign' => 'center.help.articlelist',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/help/addArticle',
                'code' => 'help.Article.pub',
                'sign' => 'center.help.addarticle',
            ),
            array(
                'name' => 'unsee',
                'url'  => '/center/help/editArticle',
                'code' => 'help.Article.pub',
                'sign' => 'center.help.editarticle',
            ),
        ),
    ),
    array(
        'name' => '技术期刊',
        'url'  => 'javascript:;',
        'code' => 'bc.center.account',
        'icon' => 'fa fa-rmb',
        'sign' => array( //用于识别当前菜单是否选中,用module.controller来识别,一级菜单要包括所有二级菜单的controller
            'center.account',
            'center.accountcus',
        ),
        'sub_menu' => array(
            array(
                'name' => '阿姨账户管理',
                'url'  => '/center/account/default',
                'code' => 'center.account.worker',
                'sign' => array(
                    'center.account.default',
                ),
            ),
            array(
                'name' => '客户账户管理',
                'url'  => '/center/accountCus/default',
                'code' => 'center.account.customer',
                'sign' => array(
                    'center.accountcus.default',
                ),
            ),
        ),
    ),
    array(
        'name' => 'DEMO管理',
        'url'  => 'javascript:;',
        'code' => 'log.stat',
        'icon' => 'fa fa-cogs',
        'sign'  => array(
        ),
    ),
);
