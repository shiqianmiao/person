<?php
/**
 * @desc 验证规则类 -- 生成rule规则
 * @copyright (c) 2013 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-08-27
 */

require_once dirname(__FILE__) . '/rule/Rule.class.php';
require_once dirname(__FILE__) . '/rule/RuleConfig.class.php';
require_once dirname(__FILE__) . '/rule/RegExpRuleConfig.class.php';
require_once dirname(__FILE__) . '/rule/RangeRuleConfig.class.php';

class Validator {

    public static function createRule ($mode, $params = array()) {

        if (is_array($mode)) {
            $params = $mode;
            $mode = $params['mode'];
            unset($params['mode']);
        }

        switch ($mode) {

            case RuleConfig::REQUIRED :
                include_once dirname(__FILE__) . '/rule/RequiredRule.class.php';

                $validator = new RequiredRule($params);
                break;

            case RuleConfig::REG_EXP :
                include_once dirname(__FILE__) . '/rule/RegExpRule.class.php';

                $validator = new RegExpRule($params);
                break;

            case RuleConfig::RANGE :
                include_once dirname(__FILE__) . '/rule/RangeRule.class.php';

                $validator = new RangeRule($params);
                break;

            case RuleConfig::CUSTOMIZE :
                include_once dirname(__FILE__) . '/rule/CustomizeRule.class.php';

                $validator = new CustomizeRule($params);
                break;

            case RuleConfig::AJAX :
                include_once dirname(__FILE__) . '/rule/AjaxRule.class.php';

                $validator = new AjaxRule($params);
                break;

            default :
                throw new Exception('mode（' . $mode . '）is not supported');
        }

        return $validator;
    }
}
