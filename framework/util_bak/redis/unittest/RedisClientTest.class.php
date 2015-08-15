<?php
require_once dirname(__FILE__) . '/../RedisClient.class.php';

function test(){
    $option = array(
            'servers'   =>   array(
                    array(
                            'host' => '192.168.5.201',
                            'password' => '123456',
                            'port' => 6379,
                            'db'       => 2,
                            'weight'   => 1,
                            'timeout' => 6
                    ),
            )
    );
    try {
        $redisClient =  new RedisClient($option);
        $redis = $redisClient->getMasterRedis();
var_dump($redis);
    } catch (Exception $e) {
        print_r($e);
    }
    
    
}
test();