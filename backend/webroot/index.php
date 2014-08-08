<?PHP
set_time_limit(0);
define('APP_CONFIG_FILE_NAME', 'config.inc.php');
require_once dirname(__FILE__) . '/../config/bootstrap.php';

$routeParams = Bootstrap::getRouteParams();

/*$channelConfigFile   = "{$routeParams['channel_dir']}/config/config.inc.php";
if (is_file($channelConfigFile)) {
    include_once $channelConfigFile;
}

$channelBootstrapFile   = "{$routeParams['channel_dir']}/config/bootstrap.php";
if (is_file($channelBootstrapFile)) {
    include_once $channelBootstrapFile;
}*/

Bootstrap::run();

exit;
