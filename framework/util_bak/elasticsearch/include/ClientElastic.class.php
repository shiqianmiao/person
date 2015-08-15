<?php
/**
 * @package              v3
 * @subpackage           
 * @author               $Author:   weihongfeng$
 * @file                 $HeadURL$
 * @version              $Rev$
 * @lastChangeBy         $LastChangedBy$
 * @lastmodified         $LastChangedDate$
 * @copyright            Copyright (c) 2013, www.273.cn
 */
require_once FRAMEWORK_PATH . "/util/elasticsearch/Core/HttpElastic.class.php";
require_once FRAMEWORK_PATH . "/util/elasticsearch/include/BulkElastic.class.php";
require_once FRAMEWORK_PATH . "/util/elasticsearch/include/MappingElastic.class.php";

class ClientElastic {
    /*
     *http操作对象
     */
    private $transport;

    /*
     *检索名
     */
    private $index;

    /*
     *文档名
     */
    private $type;

    /*
     *批量
     */
    private $bulk;

    /*
     *构造方法
     */
    public function __construct($transport, $index = null, $type = null) {
        $this->transport = $transport;
        $this->setIndex($index);
        $this->setType($type);
    }

    /*
     *
     *获得连接对象Client
     */
    public static function connection($config = array()) {
        $server = $config['server'];
        $index = $config['index'];
        $type = $config['type'];
        list($host, $port) = explode(':', $server);
        $transport = new HttpElastic($host, $port);
        $client = new self($transport, $index, $type);
        return $client;
    }

    /*
     *设置索引名
     */
    public function setIndex($index) {
        if(is_array($index)) {
            $index = implode(",", array_filter($index));
        }
        $this->index = $index;
        $this->transport->setIndex($index);
    }

    /*
     *设置文档名
     */
    public function setType($type) {
        if (is_array($type)) {
            $type = implode(",", array_filter($type));
        }
        $this->type = $type;
        $this->transport->setType($type);
    }

    /*
     *获取文档
     */
    public function get($id) {
        return $this->request($id, "GET");
    }

    /*
     *发起请求
     */
    public function request($path, $method = 'GET', $payload = false, $verbose=false) {
        $response = $this->transport->request($this->expandPath($path), $method, $payload);
        return ($verbose || !isset($response['_source']))
            ? $response
            : $response['_source'];
    }
    /*
     *加入文档，或更新文档
     *
     */
    public function index($document, $id=false, array $options = array()) {
        if ($this->bulk) {
            return $this->bulk->index($document, $id, $this->index, $this->type, $options);
        }
        return $this->transport->index($document, $id, $options);
    }
    /*
     *发起查询
     *
     */
    public function search($query, array $options = array()) {
        $start = microtime(true);
        $result = $this->transport->search($query, $options);
        $result['time'] = microtime(true) - $start;
        return $result;
    }
    /*
     *
     *删除文档
     */
    public function delete($id=false, array $options = array()) {
        if ($this->bulk) {
            return $this->bulk->delete($id, $this->index, $this->type, $options);
        }
        return $this->transport->delete($id, $options);
    }
    /*
     *按条件删除文档
     */
    public function deleteByQuery($query, array $options = array()) {
        return $this->transport->deleteByQuery($query, $options);
    }
    /*
     *刷新索引
     */
    public function refresh() {
        return $this->request('_refresh', "POST");
    }

    protected function expandPath($path) {
        $path = (array) $path;
        $isAbsolute = $path[0][0] === '/';

        return $isAbsolute
            ? $path
            : array_merge((array) $this->type, $path);
    }

    /*
     *创建批量操作事件
     */
    public function createBulk() {
        return new BulkElastic($this);
    }

    /*
     *开始批量事件
     */
    public function beginBulk() {
        if (!$this->bulk) {
            $this->bulk = $this->createBulk($this);
        }
        return $this->bulk;
    }

    /*
     *提交批量事件
     */
    public function commitBulk() {
        $result = false;
        if ($this->bulk) {
            $result = $this->bulk->commit();
            unset($this->bulk);
        }
        return $result;
    }
    /*
     *设置表属性
     *
     */
    public function map($mapping, $config, $source = array()) {
        $mapping = new Mapping($mapping, $config, $source);
        return $this->request('_mapping', 'PUT', $mapping->exportProperties($this->type), true);
    }
}
