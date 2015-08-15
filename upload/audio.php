<?php
/**
 * @brief 音频上传到七牛(音频上传走此入口)
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015-1-25
 */

require_once dirname(__FILE__) .  '/../../framework/util/qiniu/QiNiu.class.php';
require_once dirname(__FILE__) .  '/../../framework/util/http/Request.class.php';

//允许的文件格式
$allowFiles = array('.mp3');

$file = $_FILES['upfile']['tmp_name'];
$size = $_FILES['upfile']['size'];
$ext = strtolower(strrchr($_FILES['upfile']['name'], '.'));
$pre = Request::getGET('pre', 'nopre');
$editorId = Request::getGET('editorid', '');
if (!empty($editorId)) {
    $pre = 'umeditor';
}

//2M图片限额
if (!in_array($ext, $allowFiles)) {
    out('UNKNOWN', '音频格式不符合');
}
if ($size > 20971520) {
    out('SIZE', '音频不能超出20M');
}
if (empty($file)) {
    out('UNKNOWN', '文件名不存在');
}


try {
    $result = QiNiu::upLocalFile($file, $pre, $suffix = '.mp3');
} catch (Exception $e){
    $err = $e->getMessage();
}
$result = json_encode(array(
    'url' => $result['key'] ? $result['key'] : '',
    'code' => isset($err) ? 500 : 200,
    'msg'  => isset($err) ? $err : '',
));

$results = json_decode($result, true);
if ($results['code'] === 200) {
    $fdfsUrl = QiNiu::formatImageUrl($results['url']);
    echo $fdfsUrl;exit;
    out($fdfsUrl, 'SUCCESS');
} else {
    out('UNKNOWN', $results['msg']);
}

/**
 * 输出回调
 */
function out($info, $state, $editorId = '') {
    $editorId = Request::getGET('editorid', '');
    $type = Request::getREQUEST('type', '');

    // 返回数据，调用父页面的ue_callback回调
    if($type == 'ajax') {
        echo $info;
    } else {
        echo "<script>document.domain='anxin365.com';" .
                "parent.UM.getEditor('". $editorId ."').getWidgetCallback('image')('" . $info . "','" . $state . "')</script>";
    }
    exit();
}