<?php
/**
 * @desc 自定义规则类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-29
 */

require_once dirname(__FILE__) . '/Rule.class.php';

class CustomizeRule extends Rule {

    /**
     * 后端php自定义函数
     * @var string | array
     */
    private $operator = null;

    public function __construct ($params = array()) {

        $this->name = 'customize';
        $this->multiple = true;

        if (isset($params['jsCallback'])) {

            $params['operator'] = $params['jsCallback'];
            unset($params['jsCallback']);
        }

        if (isset($params['phpCallback'])) {

            $this->operator = $params['phpCallback'];
            unset($params['phpCallback']);
        }

        parent::__construct($params);
    }

    public function validate ($value) {

        $operator = $this->operator;

        if (is_null($operator)) {
            return true;
        }

        if (call_user_func($operator, $value)) {

            return true;
        }

        return false;
    }
}