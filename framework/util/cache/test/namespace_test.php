<?php 
require dirname(__FILE__).'/../CacheNamespace.class.php';

runtest();

function runtest(){
    echo 'run CacheNamespace test...<br/>';
    testApcCacheAdapter();
    testMemCacheAdapter();
}

function testApcCacheAdapter(){
    $adapter = CacheNamespace::createCache(CacheNamespace::MODE_APC);
    if ($adapter instanceof ApcCacheAdapter){
        if($adapter->write('a','123',30)){
            echo 'new ApcCacheAdapter successly!<br/>';
            return;
        }
    }
    echo 'new ApcCacheAdapter fail!<br/>';
}

function testMemCacheAdapter(){
    $config =  array(
        array(
            'host' => '192.168.5.201',
            'port' => 11211,
            'weight' => 10
        ),
    );
    $adapter = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE,$config);
    if ($adapter instanceof MemCacheAdapter){
        if ($adapter->write('a','1',30)) {
            echo 'new MemCacheAdapter successly!';
            return;
        }
    } else {
        echo 'new MemCacheAdapter fail!';
    }
    echo '<br/>';
}
