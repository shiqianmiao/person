<?PHP
//组件抽象类
//必须继承并实现setFormatCallback()和toHTML()方法

require_once FRAMEWORK_PATH . '/util/pager/Pager.class.php';

abstract class BaseWidget {

    abstract public function toHTML($params='');

}