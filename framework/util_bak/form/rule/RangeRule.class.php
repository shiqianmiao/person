<?php
/**
 * @desc 范围规则类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-29
 */

require_once dirname(__FILE__) . '/Rule.class.php';
require_once dirname(__FILE__) . '/RangeRuleConfig.class.php';

class RangeRule extends Rule {

    /**
     * 范围比较的类型
     * @var int
     */
    private $type = null;

    /**
     * 最小范围（null表示无限小）
     * @var int | date
     */
    private $min = null;

    /**
     * 最大范围（null表示无限大）
     * @var int | date
     */
    private $max = null;

    public function __construct ($params = array()) {

        $this->name = 'range';

        if (!isset($params['type'])) {

            throw new Exception('rang mode needs type');
        }

        $this->type = $params['type'];

        if (isset($params['min'])) {

            $this->min = $this->_formatValue($params['min']);
        }

        if (isset($params['max'])) {

            $this->max = $this->_formatValue($params['max']);
        }

        parent::__construct($params);
    }

    private function _formatValue ($value) {

        $type = $this->type;

        if ($type === RangeRuleConfig::COUNT && is_array($value)) {

            $value = count($value);
        } else if ($type === RangeRuleConfig::LENGTH && is_string($value)) {

            $value = mb_strlen($value, 'utf-8');
        } else {

            $value = (int) $value;
        }

        return $value;
    }

    public function validate ($value) {

        $min = $this->min;
        $max = $this->max;

        if (is_null($max) && is_null($min)) {
            return true;
        }

        $value = $this->_formatValue($value);

        if (is_null($min)){

            return $value <= $max ? true : false;
        } else if (is_null($max)) {

            return $value >= $min ? true : false;
        } else {

            return $min <= $value && $value <= $max ? true : false;
        }
    }
}