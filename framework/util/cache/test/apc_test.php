<?php 
require dirname(__FILE__).'/../adapter/ApcCacheAdapter.class.php';
require dirname(__FILE__).'/../CacheNamespace.class.php';

runTest();

function runTest(){
    echo 'run apc test<br/>';
    $GLOBALS['apc_Adapter'] = new ApcCacheAdapter();
    testWrite('273','www.273.cn',60);
    echo '<br/>';
    testRead('273');
    echo '<br/>';
    testDelete('273');
}

function testWrite($key,$value,$dur){
    $result = $GLOBALS['apc_Adapter']->write($key,$value,$dur);
    if ($result){
        echo 'write successly!';
    } else {
        echo 'write fail';
    }
}

function testRead($key){
    $readResult = $GLOBALS['apc_Adapter']->read($key);
    if ($readResult === false) {
        echo 'read fail!';
    } else if ($readResult != null){
        echo 'read successly!'.$readResult;
    } else {
        echo 'no exist';
    }
}

function testDelete($key){
    $result = $GLOBALS['apc_Adapter']->delete($key);
    if ($result){
        echo 'delete successly!';
    } else {
        echo 'fail';
    }
}
