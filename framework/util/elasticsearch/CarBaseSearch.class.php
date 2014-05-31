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
require_once FRAMEWORK_PATH . "/util/elasticsearch/include/ClientElastic.class.php";
class CarBaseSearch {
    //相等
    private $_filterArray = array();
    //范围
    private $_rangeArray = array();
    //排序字段
    private $_sortby = array();
    //限数
    private $_limit = 20;
    //偏移量
    private $_offset = 0;
    //搜索词
    private $_keyword = '';
    //字段过滤器
    public $searchFilter = array();
    //组装后的查询条件
    private $_queryFilter = array();

    private $_handle;

    public function __construct($config) {
        $this->_handle = $this->createHandle($config);
    }

    private static function createHandle($server) {
        $handle = ClientElastic::connection($server);
        return $handle;
    }

    public function getResult() {
        $handle  = $this->_handle;
        $this->buildFilter();
        $result = $handle->search($this->_queryFilter);
        $ret = $this->format($result);
        return $ret;
    }

    public function format($result) {
        if (empty($result['hits']['total'])) {
            return array(array(), 0);
        }
        $postArray = array();
        foreach ($result['hits']['hits'] as $key=>$value) {
            $post = array();
            $post = $value['_source'];
            $post['cover_photo'] = $post['cover_photo_url'];
            unset($post['cover_photo_url']);
            $post['id'] = $value['_id'];
            $postArray[] = $post;
        }
        return array($postArray, $result['hits']['total']);
    }

    private function buildFilter() {
        $query = array();
        $query['from'] = $this->_offset;
        $query['size'] = $this->_limit;
        if (!empty($this->_filterArray)) {
            foreach ($this->_filterArray as $field => $value) {
                $query['query']['bool']['must'][] = array(
                    'term' => array(
                        $field => $value,
                    )
                );
            }
        }
        if (!empty($this->_rangeArray)) {
            foreach ($this->_rangeArray as $field => $value) {
                $query['query']['bool']['must'][] = array(
                    'range' => array(
                        $field => array('from'=>$value[0], 'to' => $value[1]),
                    )
                );
            }
        }
        if (!empty($this->_keyword)) {
            $query['query']['bool']['must'][] = array(
                'term' => array(
                    'title' => $this->_keyword,
                )
            );
        }
        if (!empty($this->_sortby)) {
            $query['sort'] = $this->_sortby;
        }
        $this->_queryFilter = $query;
    }

    public function prefreFilter($filter) {
        if (empty($filter)) {
            trigger_error('无检索条件');
        }
        foreach ($filter as $field=>$value) {
            switch($field) {
                case 'offset':
                    $this->setOffset($value);
                    break;
                case 'limit':
                    $this->setLimit($value);
                    break;
                case 'sort':
                    $hasUpdate = 0; 
                    $sort = array();
                    if (is_array($value[0])) {
                        foreach ($value as $item) {
                            if ($item[0] == 'update_time') {
                                $hasUpdate = 1;
                            }
                            $sort[$item[0]] = $item[1];
                        }
                        if (!$hasUpdate) {
                            $sort['update_time'] = 'desc';
                        }
                    } else {
                         $sort[$value[0]] = $value[1];;
                    }
                    $this->setSortBy($sort);
                    break;
                case 'kw':
                    $this->setKw($value);
                    break;
                default:
                    if (!isset($this->searchFilter[$field])) {
                        break;
                    }
                    if (is_array($value)) {
                        $this->setRange($field, $value);
                    } else {
                        $this->setFilter($field, $value);
                    }
                    break;
            }
        }
    }

    public function setKw($keyword) {
        $this->_keyword = $keyword;
    }

    public function setFilter($field, $value) {
        $this->_filterArray[$field] = $value;
    }

    //必须指定min和max,如$value = array(100,200);
    public function setRange($field, $value) {
        if (!is_array($value)) {
            return;
        }
        $this->_rangeArray[$field] = $value;
    }

    public function setLimit($limit) {
        $this->_limit = $limit;
    }

    public function setOffset($offset) {
        $this->_offset = $offset;
    }

    public function setSortBy($sort) {
        $this->_sortby = $sort;
    }

}
                    
                        
                
                
        

    
        

    
