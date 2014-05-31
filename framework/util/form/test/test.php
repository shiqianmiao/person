<?php
require_once (dirname(__FILE__) . '/../Form.class.php');

$form = new Form();

$form->addFields(array(
    array(
        'name' => 'username',
        'display' => '用户名',
        'focusMessage' => '请认真填写姓名',
        'rules' => array(
            array(
                'mode' => RuleConfig::REQUIRED,
                'errorMessage' => '用户名字段不可以为空',
            ),
        ),
    ),
    array(
        'name' => 'email',
        'display' => '邮箱',
        'defaultValue' => 'lovesueee@me.com',
        'rules' => array(
                array(
                        'mode' => RuleConfig::REG_EXP,
                        'pattern' => RegExpRuleConfig::EMAIL,
                        'errorMessage' => '邮箱格式不正确',
                )
        ),
    ),
));

// var_export($form);

// var_export(json_decode('{}', true));

// echo $form->getField('username');
// echo $form->getField('email');

$ret = $form->validate();

echo $form->getField('username');

echo "\n-------------------\n";
echo $form->getField('email');
echo "\n-------------------\n";

// var_export($form);
var_export($ret);
echo "\n-------------------\n";

// $arr = array(
//         "a" => '你好',
//         "b" => 'hello',
//         "c" => false
// );

// $arr = json_encode($arr);

// echo $arr;

// echo addslashes($arr);

// echo stripslashes(addslashes($arr));

// preg_match('/hello/i', 'hello', $match);

// var_export($match);




