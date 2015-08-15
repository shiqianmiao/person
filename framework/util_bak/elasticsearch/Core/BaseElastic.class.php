<?php
/**
 * @package              v3
 * @subpackage           
 * @author               $Author:   weihongfeng$
 * @file                 $HeadURL$
 * @version              $Rev$
 * @lastChangeBy         $LastChangedBy$
 * @lastmodified         $LastChangedDate$
 * @copyright            Copyright (c) 2014, www.273.cn
 */
abstract class BaseElastic {
    /*
     *服务地址如127.0.0.1
     *
     */
    protected $host = "";

    /*
     *服务端口如9200
     *
     */
    protected $port = 9200;


    /*
     *服务索引名
     *
     */
    protected $index = "";

    /*
     *服务文档名
     *
     */
    protected $type = "";


    /*
     *构造方法
     *
     */
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }
    /*
     *增加一个新的文档
     *
     */
    abstract public function index($document, $id=false, array $options = array());

    /*
     *发送一次请求
     *
     */
    abstract public function request($path, $method="GET", $payload=false);


    /*
     *删除一个文档
     *
     */
    abstract public function delete($id=false);


    /*
     *进行一次查询
     *
     */
    abstract public function search($query);

    /*
     *设置索引名
     *
     */
    public function setIndex($index) {
        $this->index = $index;
    }

    /*
     *设置文档名
     *
     */
    public function setType($type) {
        $this->type = $type;
    }

    /*
     *构造请求url
     *
     */
    protected function buildUrl($path = false, array $options = array()) {
        $isAbsolute = (is_array($path) ? $path[0][0] : $path[0]) === '/';
        $url = $isAbsolute ? '' : "/" . $this->index;
        if ($path && is_array($path) && count($path) > 0)
            $url .= "/" . implode("/", array_filter($path));
        if (substr($url, -1) == "/")
            $url = substr($url, 0, -1);
        if (count($options) > 0)
          $url .= "?" . http_build_query($options, '', '&');
        return $url;
    }
}



