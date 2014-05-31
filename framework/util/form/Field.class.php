<?php
/**
 * @desc 字段类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-26
 */

require_once dirname(__FILE__) . '/Validator.class.php';

class Field {

    /**
     * 规范属性关键字
     * @var array
     */
    private static $keywords = array('id', 'name', 'value', 'type', 'disabled', 'checked');

    /**
     * 自定义属性前缀
     * @var string
     */
    const attrPrefix = 'data-';

    /**
     * 字段名
     * @var string
     */
    private $name = '';

    /**
     * 字段是否必填
     * @var unknown
     */
    private $required = false;

    /**
     * 隐藏域字段名（用于控制disabled）
     * @var unknown
     */
    private $hiddenName = '';

    /**
     * 字段默认值
     * @var string | array
     */
    private $defaultValue = null;

    /**
     * 字段属性
     * @var array
     */
    private $attributes = array();

    /**
     * 字段验证规则
     * @var array
     */
    private $rules = array();

    /**
     * 提交内容是否去除html标签
     * @var bool
     */
    private $tagsEnabled = false;

    public function __construct ($params = array()) {

        if (empty($params['name'])) {
            throw new Exception('field name is incorrect');
        }

        $this->name = (string) $params['name'];

        if (isset($params['hiddenName'])) {

            $this->hiddenName = (string) $params['hiddenName'];
        } else {

            $this->hiddenName = 'hidden-' . $this->name;
        }

        $params['hiddenName'] = $this->hiddenName;

        $this->setAttributes($params);
    }

    /**
     * 获取字段名
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * 添加验证规则
     * @param array $params
     */
    public function addRule($params) {

        if (is_string($params)) {

            $params = array('mode' => $params);
        }

        if (is_object($params) && $params instanceof Rule) {

            $rule = $params;
        } else {

            $rule = Validator::createRule($params);
        }

        if (empty($rule)) {
            return;
        }

        $name = $rule->getName();
        $multiple = $rule->isMultiple();

        if (!$multiple) {

            $this->rules[$name] = $rule;
        } else {

            if (empty($this->rules[$name])) {

                $this->rules[$name] = array();
            }

            $this->rules[$name][] = $rule;
        }

        if (!$this->required && $name === 'required') {
            $this->required = true;
        }

        // todo: need to optimize
        $ruleAttrs = array();

        foreach ($this->rules as $name => $rule) {

            if (is_array($rule)) {

                foreach ($rule as $r) {

                    $ruleAttrs[$r->getName()][] = $r->getOptions();
                }
            } else {

                $ruleAttrs[$rule->getName()] = $rule->getOptions();
            }
        }

        $this->setAttribute('rules', json_encode($ruleAttrs));
    }

    public function addRules ($rules) {

        foreach ($rules as $rule) {

            $this->addRule($rule);
        }
    }

    /**
     *
     * @param string $name
     * @return string
     */
    public function getAttribute ($name) {

        $name = self::attrPrefix . strtolower(preg_replace('/([A-Z])/','-$1', $name));

        return isset($this->attributes[$name]) ? $this->attributes[$name] : '';
    }

    /**
     * 设置属性
     * @param string $name 属性名
     * @param string $value 属性值
     */
    public function setAttribute ($name, $value) {

        if (!in_array(strtolower($name), self::$keywords)) {

            $name = self::attrPrefix . strtolower(preg_replace('/([A-Z])/','-$1', $name));
        }

        if (is_array($value)) {

            $value = json_encode($value);
        }

        $this->attributes[$name] = $value;
    }

    /**
     * 设置属性集合
     * @param array $params
     * array(
     *     "name" => 'username',
     *     "rules" => array(
     *         array(),
     *         array(),
     *         ...
     *     ),
     *     ...
     * )
     */
    public function setAttributes ($params) {

        $params = $this->filterAttributes($params);

        foreach ($params as $name => $value) {

            $this->setAttribute($name, $value);
        }
    }

    /**
     * 获取属性集合
     * @return array
     */
    public function getAttributes () {

        return $this->attributes;
    }

    /**
     * 返回字段属性（用于标签内部）
     * @return string
     */
    public function __toString () {

        $str = '';

        foreach ($this->attributes as $key => $value) {

            $str .= sprintf(" %s='%s' ", $key, $value);
        }

        return $str;
    }

    /**
     * 过滤特定属性
     * @param array $params
     */
    public function filterAttributes ($params) {

        if (isset($params['rules'])) {

            $this->addRules($params['rules']);
            unset($params['rules']);
        }

        if (isset($params['defaultValue'])) {

            $this->setDefaultValue($params['defaultValue']);
            unset($params['defaultValue']);
        }

        return $params;
    }

    /**
     * 执行验证
     * @return boolean
     */
    public function validate () {

        $valid = true;
        $rules = $this->rules;
        $value = $this->getValue();

        if (($this->isDisabled()) || (!$this->isRequired() && $this->isEmpty($value))) {
            $this->setAttribute('filtered', 'true');
            return true;
        }

        foreach ($rules as $rule) {

            if (!is_array($rule)) {
                $rule = array($rule);
            }

            foreach ($rule as $r) {

                if (!$r->validate($value)) {
                    $valid = false;
                    $this->setAttribute('errorMessage', $r->getErrorMessage());
                    break;
                }
            }

            if (!$valid) {
                break;
            }
        }

        $this->setAttribute('postValue', $value);

        return $valid;
    }

    /**
     * 获取字段提交的值
     * @return string | array
     */
    public function getValue ($name = '') {

        $value = '';

        if (empty($name)) {
            $name = $this->name;
        }

        if (isset($_POST[$name])) {

            $value = $this->stripTags($_POST[$name]);
        } else if (isset($_GET[$name])) {

            $value = $this->stripTags($_GET[$name]);
        }

        $value = trim($value);

        return $value;
    }

    /**
     * 去除表单值的html标签
     * @param string | string $value
     * @return string | array
     */
    private function stripTags ($value) {

        if (is_array($value)) {

            foreach ($value as &$val) {

                $val = $this->tagsEnabled ? $val : strip_tags($val);
            }
        } else {

            $value = $this->tagsEnabled ? $value : strip_tags($value);
        }

        return $value;
    }

    /**
     * 设置字段默认值
     * @param string | array
     */
    public function setDefaultValue ($value) {

        if ($value) {
            $this->defaultValue = $value;
        }

        $this->setAttribute('defaultValue', $this->defaultValue);
    }

    /**
     * 该字段是否禁用（即不用验证）
     * @return boolean
     */
    public function isDisabled () {

        $hiddenValue = $this->getValue($this->hiddenName);

        if ($hiddenValue) {
            return true;
        }

        return false;
    }

    /**
     * 字段是否必填
     */
    public function isRequired () {

        return $this->required;
    }

    public function isEmpty ($value = '') {

        if (!$value) {
            $value = $this->getValue();
        }

        if (is_array($value) && count($value) === 0) {

            return true;
        }

        if (!is_array($value) && $value === '') {

            return true;
        }

        return false;
    }


}
