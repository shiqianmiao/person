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
class Mapping {
    /*
     *字段属性列表
     */
    protected $properties = array();
    /*
     *全量属性信息
     */
    protected $config = array();
    /*
     *内容
     */
    protected $source = array(
            'enabale' => 'true',
            'compress' => 'true',
        );

    public function __construct (array $properties = array(), array $config = array(), array $source = array()) {
        $this->properties = $properties;
        $this->config = $config;
        if (!empty($source)) {
            $this->source = $source;
        }
    }


    public function exportProperties($type) {
        return array(
            $type => array(
                '_all' => $this->config,
                'properties' => $this->properties,
                '_source' => $this->source
            )
        );
    }

    /*
     *为字段设置属性
     *
     */
    public function field($field, $config = array()) {
        if (is_string($config)) {
            $config = array('type' => $config);
        }
        $this->properties[$field] = $config;
        return $this;
    }
}

