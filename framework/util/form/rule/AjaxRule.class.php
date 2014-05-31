<?php
/**
 * @desc ajax规则类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-27
 */

require_once dirname(__FILE__) . '/Rule.class.php';

class AjaxRule extends Rule {


    /**
     * 请求地址
     * @var string
     */
    private $url = '';

    /**
     * 对应于ajax的后端验证函数
     * @var string | array
     */
    private $operator = null;

    public function __construct ($params = array()) {

        $this->name = 'ajax';

        if (!isset($params['url'])) {

            throw new Exception('ajax mode needs url');
        }

        if (isset($params['phpCallback'])) {

            $this->operator = $params['phpCallback'];
            unset($params['phpCallback']);
        }

        $this->url = $params['url'];

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