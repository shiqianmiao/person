<?php
/**
 * @author 缪石乾<miaoshiqian@anxin365.com>
 * @brief 简介：添加服务者表单类
 * @date 15/3/19
 * @time 下午3:40
 */
class WorkerForm {
    /**
     * @param array() 表单域的值
     */
    private $_fieldData = array();

    public function __construct($data = array()) {
        $this->_fieldData = $data;
    }

    private function _setField($form, $data) {
        //服务者姓名
        $nameField = array(
            'name'         => 'real_name',
            'focusMessage' => '姓名只能输入2-6个字',
            'postValue'    => !empty($data['real_name']) ? $data['real_name'] : 'null',
            'tipTarget'    => '#real_name_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '服务者姓名不能为空',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 2,
                    'max'  => 6,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );

        //服务者手机
        $mobileField = array(
            'name'         => 'mobile',
            'focusMessage' => '请填写正确的手机号码',
            'postValue'    => !empty($data['mobile']) ? $data['mobile'] : 'null',
            'tipTarget'    => '#mobile_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '手机号码不能为空',
                ),
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::PHONE,
                    'errorMessage' => '格式不正确',
                ),
            ),
        );

        //出生日期
        $birthdayField = array(
            'name'         => 'birthday',
            'tipTarget'    => '#birthday_tip',
            'postValue'    => (!empty($data['birthday']) && $data['birthday'] > 0) ? date("Y-m-d", $data['birthday']) : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '出生日期必须填写',
                ),
            ),
        );

        //从业时间
        $serviceField = array(
            'name'      => 'service_time',
            'tipTarget' => '#service_time_tip',
            'postValue' => !empty($data['service_time']) ? date('Y-m-d', $data['service_time']) : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '从业时间必须填写',
                ),
            ),
        );

        //健康证过期时间
        $healthPassField = array(
            'name'      => 'health_pass',
            'tipTarget' => '#health_pass_tip',
            'defaultMessage' => '填写体检过期时间',
            'postValue' => !empty($data['health_pass']) ? date('Y-m-d', $data['health_pass']) : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '体检过期时间必须填写',
                ),
            ),
        );

        //性别
        $sexField = array(
            'name'      => 'sex',
            'tipTarget' => '#sex_tip',
            'postValue' => !empty($data['sex']) ? $data['sex'] : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '性别必须必须选择',
                ),
            ),
        );

        //服务者身份类型
        $workerTypeField = array(
            'name'      => 'type',
            'tipTarget' => '#type_tip',
            'postValue' => !empty($data['type']) ? $data['type'] : 'null',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '身份类型必须必须选择',
                ),
            ),
        );

        //家乡省份
        $homeProvinceField = array(
            'name'      => 'hometown_province',
            'tipTarget' => '#hometown_province_tip',
            'next'      => 'hometown_city',
            'postValue' => !empty($data['hometown_province']) ? $data['hometown_province'] : 'null',
            'rules' => array(
//                array(
//                    'mode' => RuleConfig::REQUIRED,
//                    'errorMessage' => '家乡省份必须选择',
//                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::VALUE,
                    'min'  => 1,
                    'errorMessage' => '家乡省份必须选择'
                ),
            ),
        );

        //家乡城市
        $homeCityField = array(
            'name'         => 'hometown_city',
            'postValue'    => !empty($data['hometown_city']) ? $data['hometown_city'] : 'null',
            'tipTarget'    => '#hometown_province_tip',
            'rules'        => array(
//                array(
//                    'mode' => RuleConfig::REQUIRED,
//                    'errorMessage' => '家乡城市必须选择',
//                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::VALUE,
                    'min'  => 1,
                    'errorMessage' => '家乡城市必须选择'
                ),
            ),
        );

        //身份证
        $idCardField = array(
            'name'         => 'id_card',
            'postValue'    => !empty($data['id_card']) ? $data['id_card'] : 'null',
            'tipTarget'    => '#id_card_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '身份证号必须填写',
                ),
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::IDENTITY,
                    'errorMessage' => '身份证格式不正确',
                )
            ),
        );

        //地址
        $addressField = array(
            'name'      => 'address',
            'postValue' => !empty($data['address']) ? $data['address'] : 'null',
            'defaultMessage' => '填写目前居住的地址',
            'tipTarget' => '#address_tip',
            'rules' => array(
                array(
                    'mode' => RuleConfig::REQUIRED,
                    'errorMessage' => '住址必须填写',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 200,
                    'errorMessage' => '字数超出限制了、限制200字以内'
                ),
            ),
        );

        //身高
        $heightField = array(
            'name'      => 'height',
            'postValue' => !empty($data['height']) ? $data['height'] : 'null',
            'tipTarget' => '#height_tip',
            'rules'     => array(
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::UP_INT,
                    'errorMessage' => '格式不正确,请填写大于0的整数',
                ),
            ),
        );

        //体重
        $weightField = array(
            'name'      => 'weight',
            'postValue' => !empty($data['weight']) ? $data['weight'] : 'null',
            'tipTarget' => '#weight_tip',
            'rules'     => array(
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::UP_INT,
                    'errorMessage' => '格式不正确,请填写大于0的整数',
                ),
            ),
        );

        //学历
        $educationField = array(
            'name'      => 'education',
            'postValue' => !empty($data['education']) ? $data['education'] : 'null',
            'tipTarget' => '#education_tip',
            'rules'     => array(
            ),
        );

        //看过多少孩子
        $takeCountField = array(
            'name'      => 'take_child_count',
            'postValue' => !empty($data['take_child_count']) ? $data['take_child_count'] : 'null',
            'tipTarget' => '#take_child_count_tip',
            'rules'     => array(
            ),
        );

        //紧急联系人姓名
        $contactNameField = array(
            'name'         => 'contact_name',
            'focusMessage' => '姓名只能输入2-6个字',
            'postValue'    => !empty($data['contact_name']) ? $data['contact_name'] : 'null',
            'tipTarget'    => '#contact_name_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 2,
                    'max'  => 6,
                    'errorMessage' => '字符个数不符合要求',
                ),
            ),
        );

        //紧急联系人手机
        $contactMobileField = array(
            'name'         => 'contact_mobile',
            'focusMessage' => '请填写正确的手机号码',
            'postValue'    => !empty($data['contact_mobile']) ? $data['contact_mobile'] : 'null',
            'tipTarget'    => '#contact_mobile_tip',
            'rules'        => array(
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::PHONE,
                    'errorMessage' => '格式不正确',
                ),
            ),
        );

        //手机型号
        $mobileModelField = array(
            'name'         => 'mobile_model',
            'postValue'    => !empty($data['mobile_model']) ? $data['mobile_model'] : 'null',
            'tipTarget'    => '#mobile_model_tip',
            'rules'        => array(
            ),
        );

        //服务者报价价格设置
        $quoteField = array(
            'name'      => 'quote',
            'postValue' => !empty($data['quote']) ? $data['quote'] / 100 : 'null',
            'defaultMessage' => '填写26天的报价',
            'focusMessage' => '填写26天的报价',
            'tipTarget' => '#quote_tip',
            'rules'     => array(
                array(
                    'mode' => RuleConfig::REG_EXP,
                    'pattern' => RegExpRuleConfig::NUMBER,
                    'errorMessage' => '格式不正确,请填写非0数字',
                ),
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::VALUE,
                    'min'  => 0,
                    'errorMessage' => '价格必须大于0'
                ),
            ),
        );

        //个人简介
        $briefField = array(
            'name'      => 'brief',
            'postValue' => !empty($data['brief']) ? $data['brief'] : 'null',
            'defaultMessage' => '限制200字以内',
            'focusMessage' => '限制200字以内',
            'tipTarget' => '#brief_tip',
            'rules' => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 200,
                    'errorMessage' => '字数超出200字限制了'
                ),
            ),
        );

        //备注
        $markField = array(
            'name'      => 'mark',
            'postValue' => !empty($data['mark']) ? $data['mark'] : 'null',
            'defaultMessage' => '限制500字以内',
            'focusMessage' => '限制500字以内',
            'tipTarget' => '#mark_tip',
            'rules' => array(
                array(
                    'mode' => RuleConfig::RANGE,
                    'type' => RangeRuleConfig::LENGTH,
                    'min'  => 0,
                    'max'  => 500,
                    'errorMessage' => '字数超出500字限制了'
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
            $nameField,
            $mobileField,
            $birthdayField,
            $serviceField,
            $healthPassField,
            $sexField,
            $homeProvinceField,
            $homeCityField,
            $workerTypeField,
            $idCardField,
            $addressField,
            $heightField,
            $weightField,
            $educationField,
            $takeCountField,
            $contactNameField,
            $contactMobileField,
            $mobileModelField,
            $quoteField,
            $briefField,
            $markField,
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