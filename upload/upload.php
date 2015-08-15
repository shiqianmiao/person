<?php
/**
 * @brief 图片上传到七牛(UMeditor编辑器上传使用此入口)
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015-1-25
 */

require_once dirname(__FILE__) .  '/../../framework/util/qiniu/QiNiu.class.php';
require_once dirname(__FILE__) .  '/../../framework/util/http/Request.class.php';

//允许的文件格式
$allowFiles = array('.gif', '.png', '.jpg', '.jpeg', '.bmp');

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
    out('UNKNOWN', '图片格式不符合');
}
if ($size > 2097152) {
    out('SIZE', '图片不能超出2M');
}
if (empty($file)) {
    out('UNKNOWN', '文件名不存在');
}

$content = file_get_contents($file);

try {
    $result = QiNiu::upLocalFile($file, $pre);
} catch (Exception $e){
    $err = $e->getMessage();
}
$results = array(
    'url' => $result['key'] ? $result['key'] : '',
    'code' => isset($err) ? 500 : 200,
    'msg'  => isset($err) ? $err : '',
);

if ($results['code'] === 200) {
    $url = QiNiu::formatImageUrl($results['url']);
    out($url, 'SUCCESS');
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