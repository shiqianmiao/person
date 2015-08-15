<?php
/**
 * 百度编辑器图片上传
 * @author chenhan <chenhan@273.cn>
 */
class QiniuController extends BcController {
    
    public function init() {
        
    }
    
    public function defaultAction() {
        
        $data = '/Users/miaoshiqian/Downloads/fengjing3.jpg';
        $result = array();
        try {
            $result = QiNiu::upLocalFile($data);
        } catch (Exception $e){
            $err = $e->getMessage();
        }
        
        echo json_encode(array(
                'url' => $result['key'] ? $result['key'] : '',
                'code' => isset($err) ? 500 : 200,
                'msg'  => isset($err) ? $err : '',
        ));
    }
    
    public function upAction() {
        include_once(FRAMEWORK_PATH . "/util/qiniu/include/io.php");
        include_once(FRAMEWORK_PATH . "/util/qiniu/include/rs.php");
        
        $bucket = "anxin365";
        $key1 = "file_name1";
        $accessKey = 'rsjgE_LBEsVfx4tXJbL1KVr2HColENU3mxAFEMUF';
        $secretKey = 'dxxr2f_t7UofvyBp6LeAJuLjyCIzFacl7lPR1zNF';
        
        Qiniu_SetKeys($accessKey, $secretKey);
        $putPolicy = new Qiniu_RS_PutPolicy($bucket);
        $upToken = $putPolicy->Token(null);
        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        list($ret, $err) = Qiniu_PutFile($upToken, $key1, '/Users/miaoshiqian/Downloads/fengjing3.jpg', $putExtra);
        echo "====> Qiniu_PutFile result: \n";
        if ($err !== null) {
            var_dump($err);
        } else {
            var_dump($ret);
        }
    }
    
    public function showImgAction() {
        $option = array(
            'height' => 700,
        );
        $key = '1422282692-1368';
        $url = QiNiu::formatImageUrl($key, $option);
        
        echo "<img src='{$url}'/>";
        
    }
}