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
if (!defined('CURLE_OPERATION_TIMEDOUT'))
    define('CURLE_OPERATION_TIMEDOUT', 28);

require_once FRAMEWORK_PATH . "/util/elasticsearch/Core/BaseElastic.class.php";
class HttpElastic extends BaseElastic {

    /*
     *
     *curl请求超时时间
     */
    const TIMEOUT = 5;

    const GET_METHOD = "GET";
    const PUT_METHOD = "PUT";
    const POST_METHOD = "POST";
    const DELETE_METHOD = "DELETE";

    const SEARCH_METHOD = "_search";
    const QUERY_METHOD = "_query";
    const REFRESH_METHOD = "_refresh";

    /*
     *object
     *curl连接的handler
     */
    protected $ch;


    public function __construct($host, $port=9200) {
        parent::__construct($host, $port);
        $this->ch = curl_init();
    }

    /*
     *增加一个文档，或者更新文档当文档id存在时
     *
     */
    public function index($document, $id=false, array $options = array()) {
        $url = $this->buildUrl(array($this->type, $id), $options);
        $method = ($id == false) ? self::POST_METHOD : self::PUT_METHOD;
        return $this->call($url, $method, $document);
    }

    /*
     *发起查询
     *
     */
    public function search($query, array $options = array()) {
        $result = false;
        if (is_array($query)) {
            //发起的是json串形式查询
            $url = $this->buildUrl(array(
                $this->type, self::SEARCH_METHOD
            ));
            $result = $this->call($url, self::GET_METHOD, $query);
        } elseif (is_string($query)) {
            //发起字符串形式查询
            $url = $this->buildUrl(array(
                $this->type, self::SEARCH_METHOD . "?q=" . $query
            ));
            $result = $this->call($url, self::POST_METHOD, $options);
        }
        return $result;
    }
    /*
     *根据条件删除文档
     *
     */
    public function deleteByQuery($query) {
        $result = false;
        if (is_array($query)) {
            //发起的是json串形式查询
            $url = $this->buildUrl(array($this->type, self::QUERY_METHOD));
            $result = $this->call($url, self::DELETE_METHOD, $query);
        } elseif (is_string($query)) {
            $url = $this->buildUrl(array($this->type, self::QUERY_METHOD), array('q' => $query));
            $result = $this->call($url, self::DELETE_METHOD);
        }
        $this->request(self::REFRESH_METHOD, self::POST_METHOD);
        return !isset($result['error']) && $result['ok'];
    }

    /*
     *
     *发起一次请求
     */
    public function request($path, $method="GET", $payload=false) {
        return $this->call($this->buildUrl($path), $method, $payload);
    }

    /*
     *删除相应id的文档
     *
     */
    public function delete($id=false, array $options = array()) {
        if ($id)
            return $this->call($this->buildUrl(array($this->type, $id), $options), self::DELETE_METHOD);
        else
            return $this->request(false, self::DELETE_METHOD);
    }

    /*
     *
     *curl请求方法
     *
     */
    protected function call($url, $method="GET", $payload=null) {
        $conn = $this->ch;
        $protocol = "http";
        $requestURL = $protocol . "://" . $this->host . $url;
        curl_setopt($conn, CURLOPT_URL, $requestURL);
        curl_setopt($conn, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($conn, CURLOPT_PORT, $this->port);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($conn, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($conn, CURLOPT_FORBID_REUSE , 0) ;

        if (is_array($payload) && count($payload) > 0)
            curl_setopt($conn, CURLOPT_POSTFIELDS, json_encode($payload)) ;
        else
	       	curl_setopt($conn, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($conn);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (!$data) {
                $data = array('error' => $response, "code" => curl_getinfo($conn, CURLINFO_HTTP_CODE));
            }
        } else {
            $errno = curl_errno($conn);
            switch ($errno) {
                case CURLE_UNSUPPORTED_PROTOCOL:
                    $error = "Unsupported protocol [$protocol]";
                    break;
                case CURLE_FAILED_INIT:
                    $error = "Internal cUrl error?";
                    break;
                case CURLE_URL_MALFORMAT:
                    $error = "Malformed URL [$requestURL] -d " . json_encode($payload);
                    break;
                case CURLE_COULDNT_RESOLVE_PROXY:
                    $error = "Couldnt resolve proxy";
                    break;
                case CURLE_COULDNT_RESOLVE_HOST:
                    $error = "Couldnt resolve host";
                    break;
                case CURLE_COULDNT_CONNECT:
                    $error = "Couldnt connect to host [{$this->host}], ElasticSearch down?";
                    break;
                case CURLE_OPERATION_TIMEDOUT:
                    $error = "Operation timed out on [$requestURL]";
                    break;
                default:
                    $error = "Unknown error";
                    if ($errno == 0)
                        $error .= ". Non-cUrl error";
                    break;
            }
        }
        return $data;
    }
        
}
