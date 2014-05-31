<?php
//haha
require_once '../../../conf/config.inc.php';

include_once CONF_PATH . '/sphinx/SphinxConfig.class.php';
include_once FRAMEWORK_PATH . '/util/sphinx/BaseSearch.class.php';
$search = new BaseSearch(SphinxConfig::$SEARCH_SERVER);

var_dump($search->deleteSearch(6495642));
var_dump($search->deleteSearch(array(6500014, 6386529)));
