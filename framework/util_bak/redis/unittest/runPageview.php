<?php
/**
 *
 * @author    chenchaoyang <chency@273.cn>
 * @since     2013-7-11
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc      详情页帖子浏览量初始化脚本
 */
require_once dirname(__FILE__) . '/../RedisClient.class.php';

function run(){
    $optionTest = array(
            'servers'   =>   array(
                    array(
                            'host' => 'idc01-v2-web-01.273hosts',
                            'password' => '123456',
                            'port' => 6379,
                            'db'       => 2,
                            'weight'   => 1,
                            'timeout' => 3
                    ),
            )
    );
    $optionOnline = array(
            'servers'   =>   array(
                    array(
                            'host' => 'idc02-web-lvs-03.273hosts',
                            'password' => '123456',
                            'port' => 6379,
                            'db'       => 2,
                            'weight'   => 1,
                            'timeout' => 3
                    ),
            )
    );
    try {
        if($GLOBALS['argv'][1]=='online'){
            $option = $optionOnline;
        } else {
            $option = $optionTest;
        }
        $redisClient =  new RedisClient($option);
        $redis = $redisClient->getMasterRedis();
        $file = file_get_contents("all_post.txt");
        $items = explode("\n", $file);
        $count = 0;
        if($redis instanceof Redis){
            if($GLOBALS['argv'][2]=='get') {
                foreach ($items as $item) {
                    ++$count;
                    $temp = explode("\t", $item);
                    $id = trim($temp[1]);
                    $rt = $redis->get('page_view_count'.'_'.$id);
                    echo $rt.'_';
                }
                echo $count;
                exit();
            }
            foreach ($items as $item) {
                ++$count;
                $temp = explode("\t", $item);
                $id = trim($temp[1]);
                $value = trim($temp[0]);
                if ($value>=0 && $id){
                    $redis->set('page_view_count'.'_'.$id, $value);
                }
                echo $id.'_'.$value.',';
            }
            echo 'finish'.$count;
        }
    } catch (Exception $e) {
        print_r($e);
    }
}
ini_set("max_execution_time", "600");
run();