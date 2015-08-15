<?php

class RedisConfig {
    public static $REDIS_APP;
    private static $SERVERS = array(
            'SERVER_1' => array(
                    'host' => '127.0.0.1',
                    'password' => '123456',
                    'port'     => 6379,
                    'db'       => 1,
                    'weight'   => 1,
                    'timeout'  => 1
            ),
    );
    
    public static function init()
    {
        self::$REDIS_APP = array(
                'servers' => array(
                        self::$SERVERS['SERVER_1'],
                )
        );
    }
}
RedisConfig::init();
