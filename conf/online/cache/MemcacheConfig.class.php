<?php
/**
 * Created by PhpStorm.
 * User: water
 * Date: 15/1/27
 * Time: 下午3:53
 */

class MemcacheConfig {
    public static $GROUP_DEFAULT = array(
        0 => array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 10
        ),
    );
    public static $GROUP_WEB = array(
        0 => array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 10
        ),
    );
    public static $GROUP_WEB2 = array(
        0 => array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 10
        ),
    );
}