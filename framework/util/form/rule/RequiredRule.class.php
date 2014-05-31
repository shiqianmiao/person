<?php
/**
 * @desc 不为空规则类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-27
 */

require_once dirname(__FILE__) . '/Rule.class.php';

class RequiredRule extends Rule {


    public function __construct ($params = array()) {

        $this->name = 'required';

        parent::__construct($params);
    }

    public function validate ($value) {

        if (is_array($value) && count($value) === 0) {

            return false;
        }

        if (!is_array($value) && $value === '') {

            return false;
        }

        return true;
    }
}