<?php
/**
 * @brief 图片上传到七牛(普通的上传控件走此入口)
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015-1-25
 */

require_once dirname(__FILE__) .  '/../framework/util/qiniu/QiNiu.class.php';
require_once dirname(__FILE__) .  '/../framework/util/http/Request.class.php';

//允许的文件格式
$allowFiles = array('.gif', '.png', '.jpg', '.jpeg', '.bmp');

$file = $_FILES['file']['tmp_name'];
$size = $_FILES['file']['size'];
$ext = strtolower(strrchr($_FILES['file']['name'], '.'));
$pre = Request::getGET('pre', 'nopre');
$msg = '';

//2M图片限额
if (!in_array($ext, $allowFiles)) {
    out('', '', 500, '图片格式不符合');
}
if ($size > 2097152) {
    out('', '', 500, '图片不能超出2M');
}
if (empty($file)) {
    out('', '', 500, '文件名不存在');
}

try {
    $result = QiNiu::upLocalFile($file, $pre);
    $url = $result['key'] ? QiNiu::formatImageUrl($result['key']) : '';
    $key = $result['key'] ? $result['key'] : '';
    out($url, $key, 200, '');
} catch (Exception $e){
    $err = $e->getMessage();
    out('', '', 500, $err);
}

/**
 * @brief 输出函数
 */
function out($url = '', $key = '', $code = 200, $msg = '') {
    echo json_encode(array(
        'url'  => $url,
        'code' => $code,
        'msg'  => $msg,
        'key'  => $key,
    ));
    exit;
}