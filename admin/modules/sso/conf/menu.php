<?php
/**
 * Created by PhpStorm.
 * User: chansun
 * Date: 15/1/13
 * Time: 下午9:03
 */
return array(
    array(
        'name' => '帮助索引',
        'url'  => 'javascript:;',
        'code' => 'log.stat',
        'sub_menu' => array(
            array(
                'name' => 'test',
                'url'  => '/log/index/index',
                'code' => 'log.stat',
            )
        ),
    ),
    array(
        'name' => '日志列表',
        'url'  => '/log/index/stat',
        'code' => 'log.stat'
    ),
    array(
        'name' => '程序错误',
        'url'  => '/log/index/error',
        'code' => 'log.stat'
    ),
);
