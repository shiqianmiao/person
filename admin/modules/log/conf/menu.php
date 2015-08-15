<?php
return [
    [
        'name'     => '日志统计',
        'url'      => 'javascript:;',
        'code'     => 'bc.center.worker',
        //该菜单需要的权限. 一般为bc.xxx
        'icon'     => 'fa fa-users',
        'sign'     => [ //用于菜单选中判断
                        'bc.log',
        ],
        'sub_menu' => [
            [
                'name' => '日志列表',
                'url'  => '/log',
                'code' => 'log.index.logList',
                'sign' => 'log.index.logList',
                //用于子菜单选中判断
            ],
        ],
    ]
];