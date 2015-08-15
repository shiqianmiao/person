<?php
/**
 * 兼容不同环境下的 gearman client 实现
 *
 * @author    林云开 <cloudop@gmail.com>
 * @since     2013-2-28
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 */
//pear
/*require_once 'Net/Gearman/Client.php';
$client= new Net_Gearman_Client('192.168.95.129:4730');
$a = array('k' => rand(1111, 9999));
$r = $client->reverse ($a);
var_dump($r);
*/

//pecl
/*$client= new GearmanClient();
$client->addServer('192.168.95.129:4730');
print $client->doBackground("reverse", "Hello World!");
*/

require_once(CONF_PATH. '/messagepassing/GearmanConfig.class.php');
if(!class_exists('GearmanClient')){

    require_once(FRAMEWORK_PATH. '/util/gearman/Net/Gearman/Client.php');
    class ClientGearman extends Net_Gearman_Client{
        
        public static $instance;
        public static $hosts;
        public $sendDownLog = false;
        public $downLogs = array();
        function __construct($hosts, $timeout){

            if(!self::$instance){

                $this->_selfDefence($hosts);
            }

            if(!empty(self::$hosts)){

                parent::__construct(self::$hosts, $timeout);
            }else{
                //所有的jobserver都down掉
                throw new Exception('Gearman down totally!!!');
            }
        }
        
        /**
         * 连不上的服务器给一个warnning日志
         *
         * @param array $servers
         * @return array
         */
        private function _selfDefence($hosts){

            $timeout = 1;
            $fatalLog = array();
            foreach($hosts as $key => $host){

                $tmp = explode(':', $host);
                $handle = @fsockopen($tmp[0], $tmp[1], $errorNumber, $errorString, $timeout);
                if(!$handle){
                    $this->sendDownLog = true;
                    unset($hosts[$key]);
                    $this->downLogs[] = 'Can not connect to '. $tmp[0]. ' '. $errorNumber. ':'. $errorString;
                    //cat log
                }else{
                    fclose($handle);
                }
            }
            self::$hosts = $hosts;
            return self::$hosts;
        }
        
        /**
         * 后台执行
         *
         * @param string $wokerName
         * @param string $params
         * @return unknown
         */
        public function doBackground($wokerName, $params){

            $jsonParams = json_encode($params);
            return $this->$wokerName($jsonParams);
        }
        
        /**
         * 所有的jobserver不可用的话，trigger E_USER_ERROR
         *
         * @return object
         */
        public static function instance(){
            
            if(!isset(self::$instance) && !(self::$instance instanceof ClientGearman)){

                self::$instance = new ClientGearman(GearmanConfig::$servers, GearmanConfig::$timeout);
                //down的jobserver发送fatallog 修改此处注意死循环
                if(self::$instance->sendDownLog){
    
                    include_once(FRAMEWORK_PATH. '/util/gearman/LoggerGearman.class.php');
                    foreach(self::$instance->downLogs as $key => $val){

                        LoggerGearman::logFatal(array('data' => $val, 'identity' => 'gearman'));
                    }
                }
            }
            return self::$instance;
        }
    }
}else{

    //这个要装扩展，暂时废弃，有个bug会导致服务端worker不能用，所以干脆client端都不要装扩展了。
//    class ClientGearman extends GearmanClient{
//        
//        public static $instance;
//        function __construct($host, $timeout){
//                
//            parent::addServers(implode(',', $host));
//        }
//        
//        public static function instance(){
//            
//            if(!isset(self::$instance) && !(self::$instance instanceof ClientGearman)){
//
//                self::$instance = new ClientGearman(GearmanConfig::$servers, GearmanConfig::$timeout);
//            }
//            return self::$instance;
//        }
//    }
}

