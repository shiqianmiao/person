<?PHP

require_once dirname(__FILE__) . '/../config/config.inc.php';

$jsonpCallback = RequestUtil::getGET(BackendPageConfig::$JSONP_CALLBACK_NAME, '');

ResponseUtil::setContentType(ResponseUtil::CONTENT_TYPE_JSONP, $jsonpCallback);
ResponseUtil::output(array(
	'errorCode'    => 1,
	'errorMessage' => '您查看的信息不存在或已删除',
	'data'         => null,
));
