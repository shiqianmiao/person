<?php
define('APP_CONFIG_FILE_NAME', 'cdcConfig.inc.php');
require_once dirname(__FILE__) . '/../config/bootstrap.php';
$routeParams = Bootstrap::getRouteParams();
Bootstrap::run();

exit;
