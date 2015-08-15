<?php

class LogGearman{
    //对象实例数组
    public static $instances = [];

    //gearmanClient 对象
    private $gmc;

    //服务端的需要执行的函数名
    private $queue;

    /**
     * @brief 获取或创建（如果需要）一个日志实例
     */
    public static function getInstance($params = []) {
        $hash = json_encode($params);
        if (!array_key_exists($hash, self::$instances)) {
            self::$instances[$hash] = new self ($params);
        }
        return self::$instances[$hash];
    }

    /**
     * @brief 构造函数
     */
    public function __construct($params) {
        //默认参数
        $defaultParams = [
            'server' => '127.0.0.1',
            'port'   => 4730,
            'queue'  => 'log',
        ];

        $params = empty($params) ? $defaultParams : $params;

        $this->queue = $params['queue'];
        $this->gmc   = new gearmanClient();
        $this->gmc->addServer($params['server'], $params['port']);
    }

    /**
     * @brief 记录日志
     */
    public function log($params) {
        $this->gmc->doBackground($this->queue, json_encode($params));
    }

}