<?php
/**
 * @desc 门店管理表单
 * @author 缪石乾<miaosq@273.cn>
 * @since 2013-9-5
 */

require_once FRAMEWORK_PATH . '/util/form/Form.class.php';

class ShopManageForm {
    
    private $form = null;
    
    private $fieldData = array();
    
    private $options = array();

    
    function __construct($fieldData = array()) {
        $this->fieldData = empty($fieldData) ? array() : $fieldData;
        $this->form = new Form();
        $this->setFields();
    }
    
    /**
     * @desc 配置表单字段
     */
    private function setFields() {
        $data = $this->fieldData;
        //门店口号
        $sloganField = array(
            'name'         => 'shop_slogan',
            'focusMessage' => '不能超过14个字',
            'tipTarget'    => '#slogan_tip',
            'postValue'    => !empty($data['shop_slogan']) ? $data['shop_slogan'] : 'null',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 14,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );
        //门店寄予
        $messageField = array(
            'name'         => 'shop_hope',
            'focusMessage' => '不能超过100个字',
            'tipTarget'    => '#message_tip',
            'postValue'    => !empty($data['shop_hope']) ? $data['shop_hope'] : 'null',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 100,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );
        
        
        //添加表单域
        $this->form->addFields(array(
            $sloganField,
            $messageField,
        ));
    }
    
    /**
     * @desc 获取表单对象
     */
    public function getForm() {
        return $this->form;
    }
}