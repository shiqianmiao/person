<?php
/**
 * @desc 规则抽象基类
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-27
 */

abstract class Rule {

    /**
     * 规则名
     * @var string
     */
    protected $name = '';

    /**
     * 规则配置选项
     * @var array
     */
    protected  $options = array();

    /**
     * 验证错误提示信息
     * @var string
     */
    protected $errorMessage = '';

    /**
     * 同种规则多个实例
     * @var bool
     */
    protected $multiple = false;

    public function __construct ($params) {

        if (isset($params['errorMessage'])) {
            $this->errorMessage = $params['errorMessage'];
        }

        $this->options = $params;
    }

    protected abstract function validate ($value);

    /**
     * 获取规则名
     */
    public function getName () {

        return $this->name;
    }

    /**
     * 获取规则配置选项
     * @return multitype:
     */
    public function getOptions () {

        return $this->options;
    }

    /**
     * 获取错误提示信息
     * @return string
     */
    public function getErrorMessage () {

        return $this->errorMessage;
    }

    /**
     * 设置错误提示信息
     * @param string $message
     */
    public function setErrorMessage ($message) {

        $this->errorMessage = $message;
    }

    /**
     * 是否拥有多个规则实例
     */
    public function isMultiple () {

        return $this->multiple;
    }

}
