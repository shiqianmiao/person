<?php
require_once 'FileSystem.class.php';

/**
 * @author 缪石乾<miaosq@273.cn>
 * @since - 2013-10-14
 * @desc 材料上传工具类
 */

class UploadUtil {
    
    /**
     * @desc 允许上传的文件类型
     * @var array()
     */
    private $allowFileTypes = array('html','htm','doc','zip','rar','txt','jpg','gif','bmp','png','jpeg', 'pdf');
    
    /**
     * @desc 允许上传的文件大小，单位字节, 默认1M
     * @var int
     * @access public
     */
    public $maxFileSize = 1048576;
    
    public function __construct() {
        
    }

    /**
     * @desc 设置上传文件类型
     * @return void
     * @access public
     * @param $fileTypes array 例如：array('html','htm','doc','zip','rar','txt','jpg','gif','bmp','png','jpeg', 'pdf')
     */
    public function setAllowFileType($fileTypes) {
        if (is_array($fileTypes) && !empty($fileTypes)) {
            $this->allowFileTypes = $fileTypes;
        }
    }
    
    /**
     * @desc 上传文件
     * @param string $fileField 要上传的文件如$_FILES['file']
     * @param string $destFolder 上传的目录，会自动建立
     * @param string $fileTypes 上传后文件命名方式0使用原文件名1使用当前时间戳作为文件名
     * @return int 
     * @access public
     */
    public function upload($fileField, $destFolder = './', $fileNameType = 1, $shopId = 0) {

        switch ($fileField['error']) {
        case UPLOAD_ERR_OK : //其值为0，没有错误发生，文件上传成功。
            $upload_succeed = true;
            break;
        case UPLOAD_ERR_INI_SIZE : //其值为1，上传文件超过了php.ini 中upload_max_filesize 选项限制的值
        case UPLOAD_ERR_FORM_SIZE : //其值为 2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
            $errorMsg = '文件上传失败！失败原因：文件大小超出限制';
            $errorCode = -100;
            $upload_succeed = false;
            break;
        case UPLOAD_ERR_PARTIAL : //其值为3，文件只有部分被上传
            $errorMsg = '文件上传失败！失败原因：文件只有部分被上传';
            $errorCode = -101;
            $upload_succeed = false;
            break;
        case UPLOAD_ERR_NO_FILE : //其值为4，没有文件被上传
            $errorMsg = '文件上传失败！失败原因：没有文件被上传';
            $errorCode = -102;
            $upload_succeed = false;
            break;
        case UPLOAD_ERR_NO_TMP_DIR : //其值为 6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。
            $errorMsg = '文件上传失败！失败原因：找不到临时文件夹！';
            $errorCode = -103;
            $upload_succeed = false;
            break;
        case UPLOAD_ERR_CANT_WRITE : //其值为 7，文件写入失败。PHP 5.1.0 引进。
            $errorMsg = '文件上传失败！失败原因：文件写入失败！';
            $errorCode = -104;
            $upload_succeed = false;
            break;
        default : //文件上传失败
            $errorMsg = '文件上传失败！失败原因：其它错误';
            $errorCode = -105;
            $upload_succeed = false;
            break;
        }
        if ($upload_succeed !== false && !is_uploaded_file($fileField['tmp_name'])) {
            $errorMsg = '文件上传失败！失败原因：不是一个上传文件';
            $errorCode = -108;
            $upload_succeed = false;
        }
        if ($upload_succeed) {
            if ($fileField['size'] > $this->maxFileSize) {
                $errorMsg = '文件上传失败！失败原因：文件大小超出限制！不能大于'.formatFileSize($this->maxFileSize);
                $errorCode = -100;
                $upload_succeed = false;
            }
            if ($upload_succeed) {
                $fileExt = FileSystem::fileExt($fileField['name']);
                if (!in_array(strtolower($fileExt),$this->allowFileTypes)) {
                    $errorMsg = '文件上传失败！失败原因：文件类型不被允许！只能上传'. implode(',',$this->allowFileTypes).'的格式';
                    $errorCode = -106;
                    $upload_succeed = false;
                }
            }
        }
        
        if ($upload_succeed) {
            if (!is_dir($destFolder) && $destFolder!='./' && $destFolder!='../') {
                $dirname = '';
                $folders = explode('/',$destFolder);
                foreach ($folders as $folder) {
                    $dirname .= $folder . '/';
                    if ($folder!='' && $folder!='.' && $folder!='..' && !is_dir($dirname)) {
                        mkdir($dirname);
                    }
                }
                chmod($destFolder,0777);
            }
            switch ($fileNameType) {
                case 1:
                    $fileName = date('YmdHis');
                    $dot = '.';
                    $fileFullName = $fileName . $dot . $fileExt;
                    $i = 0;
                    //判断是否有重名文件
                    while (is_file($destFolder . $fileFullName)) {
                        $fileFullName = $fileName . $i++ . $dot . $fileExt;
                    }
                    break;
                case 2:
                    $fileFullName = date('YmdHis');
                    $i = 0;
                    //判断是否有重名文件
                    while (is_file($destFolder . $fileFullName)) {
                        $fileFullName = $fileFullName . $i++;
                    }
                    break;
                case self::SALE_IMG_NAME:
                    list($usec, $sec) = explode(' ', microtime());
                    $usec *= 1000;
                    $shopStr = '';
                    if ($shopId > 0) {
                        $shopStr = $shopId;
                    }
                    $fileNamePart = sprintf('%s%03d', $sec, $usec);
                    $dot = '.';
                    $fileFullName = $fileNamePart . sprintf('%02d', $i++) . $shopStr . $dot;
                    while (is_file($destFolder . $fileFullName) && $i < 100) {
                        $fileFullName = $fileNamePart . sprintf('%02d', $i++) . $shopStr . $dot;
                    }
                    if ($i == 100) { //
                        return '';
                    }
                    if ($fileExt == self::GIF_EXT) {
                        $transDestFullName = $fileFullName . self::PNG_EXT;
                    }
                    $fileFullName .= $fileExt;
                    break;
                case self::CUSTOM_NAME:
                    $fileFullName = $shopId . '.' . $fileExt;;
                    break;
                default:
                    $fileFullName = $fileField['name'];
                    break;
            }
            if (@move_uploaded_file($fileField['tmp_name'], $destFolder . $fileFullName)) {
                
                if ($upload_succeed) {
                    return $fileFullName;
                }
            } else {
                $errorMsg = '文件上传失败！失败原因：本地文件系统读写权限出错！';
                $errorCode = -107;
                $upload_succeed = false;
            }
        }
        if (!$upload_succeed) {
            throw new Exception($errorMsg,$errorCode);
        } else {
            throw new Exception('unknown error', -108);
        }

    }
}

function formatFileSize($size) {
    if ($size < 1024) {
        $result = $size . '字节';
    } else if ($size < 1024*1024) {
        $fStr = '%0.2f';
        if ($size % 1024 == 0) {
            $fStr = '%d';
        }
        $result = sprintf($fStr, $size/1024) . 'K';
    } else {
        $unitVal = 1024*1024;
        $fStr = '%0.2f';
        if ($size % $unitVal == 0) {
            $fStr = '%d';
        }
        $result = sprintf($fStr, $size / $unitVal) . 'M';
    }
    return $result;
}
?>