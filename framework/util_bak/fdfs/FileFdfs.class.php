<?php
/**
 * @package              273_img
 * @subpackage           
 * @author               $Author:   weihongfeng$
 * @file                 $HeadURL$
 * @version              $Rev$
 * @lastChangeBy         $LastChangedBy$
 * @lastmodified         $LastChangedDate$
 * @copyright            Copyright (c) 2013, www.273.cn
 */
class FileFdfs {

    private $filename = '';
    private $group = '';
    private $meta = null;
    function __construct($filename='') {
        if ($filename) {
            $this->parse_filename($filename);
        }
    }
    //解析组和文件id
    private function parse_filename($filename) {
        $sep = strpos( $filename , '/', 0);
        if ($sep === false) {
            return;
        }
        $group = substr($filename, 0, $sep);
        $remote_filename = substr($filename, $sep+1);
        $this->group = $group;
        $this->filename = $remote_filename;
    }
    //判断是否合格请求
    private function is_empty() {
        return !$this->group || !$this->filename;
    }

    //从fdfs中读出文件内容
    public function Read() {
        if ($this->is_empty) {
            return false;
        }
        return fastdfs_storage_download_file_to_buff($this->group, $this->filename);
    }

    //删除文件
    public function Delete() {
        if ($this->is_empty) {
            return false;
        }
        return fastdfs_storage_delete_file($this->group, $this->filename);
    }

    //判断文件是否存在
    public function IsExist() {
        if ($this->is_empty) {
            return false;
        }
        return fastdfs_get_file_info($this->group, $this->filename) !== false;
    }
    
    //创建文件
    public function Create($content, $ext) {
        $file_info = fastdfs_storage_upload_by_filebuff($content, $ext);
        if($file_info === false) {
            return false;
        }
        $this->group = $file_info['group_name'];
        $this->filename = $file_info['filename'];
        return true;
    }
    //拿到文件名
    public function GetFileName() {
        if ($this->is_empty) {
            return false;
        }
        return $this->group . '/' . $this->filename;
    }
    //获取文件附加属性
    public function GetMeta() {
        if ($this->is_empty) {
            return false;
        }
        $this->meta = fastdfs_storage_get_metadata($this->group, $this->filename);
        return $this->meta;
    }
    //设置文件附加属性,$meta array(filed1=>value1, field2=>value2, ....)
    public function SetMetaField($meta) {
        if ($this->is_empty) {
            return false;
        }
        return fastdfs_storage_set_metadata($this->group, $this->filename, $meta);
    }

    public function type() {
        return 'fdfs';
    }       

    public function Close() {
        fastdfs_tracker_close_all_connections();
    }

}
        
        
        
        
        
    
        


