<?php
/**
 * @desc 表单类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-26
 */

require_once dirname(__FILE__) . '/Field.class.php';

class Form {

    /**
     * 表单字段集合
     * @var array
     */
    private $fields = array();

    /**
     * 获取指定（或全部）表单字段
     * @param string $name
     * @return object Field
     */
    public function getField ($name) {

        if (empty($name)) {
            return $this->fields;
        }

        return empty($this->fields[$name]) ? null : $this->fields[$name];
    }

    /**
     * 增加字段
     * @param string | array | object(Field) $params
     */
    public function addField($params) {

        if (is_string($params)) {
            $params = array('name' => $params);
        }

        if (is_object($params) && $params instanceof Field) {
            $field = $params;
        } else {
            $field = new Field($params);
        }

        $this->fields[$field->getName()] = $field;
    }

    /**
     * 增加字段集合
     * @param array
     */
    public function addFields($fields) {

        foreach ($fields as $field) {

            $this->addField($field);
        }
    }

    /**
     * 删除指定表单字段
     * @param string $name
     */
    public function removeField ($name) {

        if (empty($name)) {
            return;
        }

        if (is_array($name)) {
            foreach ($name as $value) {

                $this->removeField($value);
            }
            return;
        }

        unset($this->fields[$name]);
    }

    /**
     * 执行表单验证
     * @return bool
     */
    public function validate () {

        $valid = true;
        $fields = $this->fields;

        foreach ($fields as $field) {

            // 验证所有字段，不做break
            if (!$field->validate() && $valid) {

                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * 获取表单数据
     * @param string $name
     * @return array | string
     */
    public function getValue ($name = '') {

        static $data = array();

        if (empty($data)) {
            foreach ($this->fields as $field) {

                $data[$field->getName()] = $field->getValue();
            }
        }

        if ($name) {

            if (array_key_exists($name, $data)) {

                return $data[$name];
            }

            return null;
        }
        return $data;
    }
}