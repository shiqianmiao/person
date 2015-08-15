<?php

//判断是否有php集成
if (!class_exists('SphinxClient')) {
    include_once FRAMEWORK_PATH . '/util/sphinx/sphinxapi.php';
}

class BaseSearch {
    //相等
    private $_filterArray = array();
    //范围
    private $_rangeArray = array();
    //排序字段
    private $_sortby = '';
    //限数
    private $_limit = 20;
    //偏移量
    private $_offset = 0;
    //搜索词
    private $_keyword = '';
    
    const DIRTY_INDEX = 'is_dirty';

    const DELETE_ERROR_KEY = 'search.delete.error';
    const SEARCH_ERROR_KEY = 'search.result.error';
    const RETRY_TIMES = 3;

    public $searchFilter = array();

    private $_handle;
    /**
     *以数组条件传入参数,获取车源数据
     *
     */
    public function __construct($config) {
        $this->_handle = $this->createHandle($config['host'], $config['port']);
    }

    private static function createHandle($host, $port) {
        $handle = new SphinxClient();
        $handle->SetServer($host, $port);
        $handle->SetConnectTimeout(5);
        $handle->setRetries(2, 300);
        $handle->SetArrayResult(true);
        $handle->SetMatchMode(SPH_MATCH_EXTENDED2);
        return $handle;
    }
    
    public function deleteSearch($id) {
        $handle = $this->_handle;
        $retMain = -1;
        $retDelta = -1;
        if (is_numeric($id)) {
            $id = (int)$id;
            $retMain = $handle->UpdateAttributes("main", array(self::DIRTY_INDEX), array($id=>array(0)));
            $retDelta = $handle->UpdateAttributes("delta", array(self::DIRTY_INDEX), array($id=>array(0)));
        } elseif (is_array($id) && !empty($id)) {
            $valueArray = array();
            foreach ($id as $item) {
                $valueArray[$item] = array(0);
            }
            $retMain = $handle->UpdateAttributes("main", array(self::DIRTY_INDEX), $valueArray);
            $retDelta = $handle->UpdateAttributes("delta", array(self::DIRTY_INDEX), $valueArray);
        }
        $handle->close();
        if ($retMain < 0 || $retDelta < 0) {
            include_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';
            LoggerGearman::logFatal(array('data'=>$id, 'identity'=>self::DELETE_ERROR_KEY));
        } 
    }

    /**
     *获取车源数据id
     *
     */
    public function getIds() {
        $handle  = $this->_handle;
        $handle->SetLimits($this->_offset, $this->_limit, $this->_offset + $this->_limit);
        if ($this->_sortby) {
            $handle->SetSortMode(SPH_SORT_EXTENDED, $this->_sortby);
        }
        if (!empty($this->_filterArray)) {
            foreach ($this->_filterArray as $field=>$value) {
                $handle->SetFilter($field, $value);
            }
        }
        if (!empty($this->_rangeArray)) {
            foreach ($this->_rangeArray as $field=>$value) {
                if ($this->searchFilter[$field] == 'int') {
                    $handle->SetFilterRange($field, (int)$value[0], (int)$value[1]);
                } else {
                    $handle->SetFilterFloatRange($field, (float)$value[0], (float)$value[1]);
                }
            }
        }
        $res = $handle->Query($this->_keyword);
        if ($res === false) {
            include_once FRAMEWORK_PATH . '/util/gearman/LoggerGearman.class.php';
            include_once FRAMEWORK_PATH . '/util/common/Ip.class.php';
            $serverIp = Ip::getLocalIp();
            if (in_array($serverIp, array('58.83.237.196', '211.151.189.56', '211.151.189.60'))) {
                $errorArray = array(
                    'sphinxError' => $handle->GetLastError(),
                    'sphinxWarning' => $handle->GetLastWarning(),
                    'filter' => $this->_filterArray,
                    'range'  => $this->_rangeArray,
                    'offset' => $this->_offset,
                    'limit'  => $this->_limit,
                    'sortby' => $this->_sortby,
                    'keyword'=> $this->_keyword,
                    'server' => $serverIp,
                );
                LoggerGearman::logFatal(array('data'=>$errorArray, 'identity'=>self::SEARCH_ERROR_KEY));
            }
        }
        $handle->close();
        $ids = array();
        if (!empty($res['matches'])) {
            foreach ($res['matches'] as $item) {
                $ids[] = $item['id'];
            }
        }
        return array($ids, $res['total_found']);
    }


    /**
     * 准备过滤条件
     *
     */
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
                    $sort = '';
                    $hasUpdate = 0;
                    if (is_array($value[0])) {
                        foreach ($value as $item) {
                            $sort .= (($sort ? ',' : '') . $item[0] . ' ' . $item[1]);
                            if ($item[0] == 'update_time') {
                                $hasUpdate = 1;
                            }
                        }
                        if (!$hasUpdate) {
                            $sort .= ',update_time desc';
                        }
                    } else {
                        $sort = $value[0] . ' ' . $value[1];
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
                        if (is_array($value[0])) {
                            if ($this->searchFilter[$field] == 'int') {
                                $this->setIn($field, $value[0]);
                            }
                        } else {
                            $this->setRange($field, $value);
                        }
                    } elseif ($this->searchFilter[$field] == 'int') {
                        $this->setFilter($field, $value);
                    }
                    break;
             }
        }
        $this->setFilter(self::DIRTY_INDEX, 1);
    }

    public function setKw($keyword) {
        $this->_keyword = $keyword;
    }

    public function setIn($field, $value){
        $this->_filterArray[$field] = $value;
    }

    public function setFilter($field, $value) {
        $this->_filterArray[$field] = array((int)$value);
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
