<?php
/**
 * @author 缪石乾<miaoshiqian@anxin365.com>
 * @brief 简介：添加banner表单类
 * @date 15/3/19
 * @time 下午3:40
 */
class BannerForm {
    /**
     * @param array() 表单域的值
     */
    private $_fieldData = array();

    public function __construct($data = array()) {
        $this->_fieldData = $data;
    }

    private function _setField($form, $data) {
        //banner的大标题
        $title1Field = array(
            'name'         => 'title1',
            'focusMessage' => '限制输入20个字',
            'defaultMessage' => '限制输入20个字',
            'postValue'    => !empty($data['title1']) ? $data['title1'] : 'null',
            'tipTarget'    => '#title1_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => 'banner大标题必须填写',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 1,
                    'max'  => 20,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );

        //banner简介
        $briefField = array(
            'name'         => 'brief',
            'focusMessage' => '限制输入50个字',
            'defaultMessage' => '限制输入50个字',
            'postValue'    => !empty($data['brief']) ? $data['brief'] : 'null',
            'tipTarget'    => '#brief_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '简介内容必须填写',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 1,
                    'max'  => 50,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );

        //隐藏表单域
        $idField = array(
            'name'      => 'id',
            'postValue' => !empty($data['id']) ? $data['id'] : 0,
            'rules' => array(
            ),
        );

        $form->addFields(array(
            $title1Field,
            $briefField,
            $idField,
        ));
    }

    /**
     * @return 返回form对象
     */
    public function getForm() {
        $formObj = new Form();
        $this->_setField($formObj, $this->_fieldData);
        return $formObj;
    }
}