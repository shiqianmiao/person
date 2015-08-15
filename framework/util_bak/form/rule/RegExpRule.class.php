<?php
/**
 * @desc 正则表达式规则类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-28
 */

require_once dirname(__FILE__) . '/Rule.class.php';

class RegExpRule extends Rule {

    /**
     * 正则表达式
     * @var string
     */
    private $pattern = '';

    /**
     * 不匹配（非）
     * @var bool
     */
    private $not = false;

    public function __construct ($params = array()) {

        $this->name = 'regExp';
        $this->multiple = true;

        if (!isset($params['pattern'])) {

            throw new Exception('regexp mode needs pattern');
        }

        // php和js表单正则不一致
        if (isset($params['phpPattern'])) {
            $this->pattern = $params['phpPattern'];
            unset($params['phpPattern']);
        } else {
            $this->pattern = $params['pattern'];
        }


        if (isset($params['not'])) {

           $this->not = (bool) $params['not'];

           $params['not'] = $this->not;
        }

        parent::__construct($params);
    }

    public function validate ($value) {

        $pattern = $this->pattern;
        $not = $this->not;

        $match = empty($pattern) ? true : (bool) preg_match($pattern, $value);

       if ($not ^ $match) {
           return true;
       }

        return false;
    }
}
