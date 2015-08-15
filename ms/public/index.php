<?PHP
//是否开启错误调试模式
$_SERVER['DEBUG'] = true;

require_once dirname(__FILE__) . '/../conf/config.inc.php';
require_once FRAMEWORK_PATH . '/common/AutoLoader.class.php';
require_once FRAMEWORK_PATH . '/mvc/bootstrap.php';

//设置该模块自动加载的目录,可以设置多个,如果多个目录中有同一个类的话，先设置哪个目录，就自动加载这个目录下面的类
AutoLoader::setAutoDir(FRAMEWORK_PATH . "/util/");
AutoLoader::setAutoDir(FRAMEWORK_PATH . "/../conf/conf/");
AutoLoader::setAutoDir(FRAMEWORK_PATH . "/../conf/common/");
AutoLoader::setAutoDir(FRAMEWORK_PATH . "/../api/interface/");
AutoLoader::setAutoDir(FRAMEWORK_PATH . "/../api/model/");
AutoLoader::setAutoDir(PROJECT_PATH . "/conf/");
AutoLoader::setAutoDir(PROJECT_PATH . "/common/");

//配置该模块下面的modules 子模块.例如：'blog','news'
Router::setModules(array(
    //'blog', 'news'
));

Dispatch::run(
    Router::getController(),
    Router::getAction(),
    Router::getParams(),
    Router::getModule()
);