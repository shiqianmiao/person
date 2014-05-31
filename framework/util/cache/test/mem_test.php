<?php 
require dirname(__FILE__).'/../adapter/MemCacheAdapter.class.php';
require dirname(__FILE__).'/../CacheNamespace.class.php';

runTest();

function runTest(){
    echo 'run memcache test<br/>';
    $config =  array(
        0 => array(
            'host' => '192.168.5.201',
            'port' => 11211,
            'weight' => 10
        ),
    );
    testConnectConsistent($config);
    testWriteRead();
    testWriteReadMulti();
    testIncrement();
    testDecrement();
}

function testConnectConsistent($config){
    $GLOBALS['memcache'] = new MemCacheAdapter($config);
    if ($GLOBALS['memcache'] instanceof MemCacheAdapter){
        echo 'connect successly!';
    } else {
        echo 'connect fail!';
    }
    echo '<br/>';
}

function testWriteRead(){
    $key = '273';
    $value = 'www.273.cn';
    $dur = 30;
    $result = $GLOBALS['memcache']->write($key,$value,$dur);
    if ($result){
        echo 'write successly!';
    } else {
        echo 'write fail!';
    }
    $readValue = $GLOBALS['memcache']->read($key);
    if ($readValue == $value) {
        echo 'read successly!';
    } else {
        echo 'read fail!';
    }
    echo $readValue.'<br/>';
    $GLOBALS['memcache']->delete($key);
}

function testWriteReadMulti(){
    $keys = array(
        'k1' => 1,
        'k2' => array(1,2),
        'k3' => new CacheNamespace()
    );
    $res = $GLOBALS['memcache']->writeMulti($keys);
    if ($res){
        echo 'writeMulti successly!';
    } else {
        echo 'writeMulti fail!';
    }
    $readResult = $GLOBALS['memcache']->readMulti(array('k1','k2','k3'));
    if ($readResult['k1'] == 1 && $readResult['k2'] == array(1,2) && $readResult['k3'] instanceof CacheNamespace){
        echo 'readMulti successly!';
    } else {
        echo 'readMulti fail!';
    }
    echo '<br/>';
    $GLOBALS['memcache']->clear();
}

function testIncrement(){
    $key = 'abc';
    $GLOBALS['memcache']->write($key,1);
    $ok = $GLOBALS['memcache']->increment($key);
    $ok = $GLOBALS['memcache']->increment($key,2);
    if($ok == 4){
        echo 'test increment successly!';
    } else {
        echo 'test increment fail!';
    }
    echo '<br/>';
    $GLOBALS['memcache']->delete($key);
}

function testDecrement(){
    $key = 'abc';
    $GLOBALS['memcache']->write($key,5);
    $ok = $GLOBALS['memcache']->decrement($key);
    $ok = $GLOBALS['memcache']->decrement($key,2);
    if ($ok == 2) {
        echo 'test decrement successly!';
    } else {
        echo 'test decrement fail!';
    }
    echo '<br/>';
    $GLOBALS['memcache']->delete($key);
}
