<?php
/**
 * @brief 简介：
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年3月13日 下午4:53:59
 */

//set_magic_quotes_runtime(0);
require dirname(__FILE__) . '/../../conf/config.inc.php';
require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

$tableName = 'banner';
$dbName = 'DBConfig::DB_BC';
$dbMasterConfig = 'DBConfig::$SERVER_MASTER';
$dbSlaveConfig = 'DBConfig::$SERVER_SLAVE';

class AutoCreateModel extends BaseModel {
    protected function init() {
        global $dbName,$tableName,$dbMasterConfig,$dbSlaveConfig;
        $this->dbName         = eval('return '.$dbName.';');
        $this->tableName      = $tableName;
        $this->dbMasterConfig = eval('return '.$dbMasterConfig.';');
        $this->dbSlaveConfig  = eval('return '.$dbSlaveConfig.';');
        $this->fieldTypes     = array(1);
    }

    function getTableName() {
        return $this->tableName;
    }

    public static function getClassName($tableName) {
        $fs = explode('_', $tableName);
        $name = '';
        foreach ($fs as $f) {
            $name .= ucfirst($f);
        }
        $name .= 'Model';
        return $name;
    }

}

$className = AutoCreateModel::getClassName($tableName);

$a = new AutoCreateModel();
$fields = $a->getFields();

$fieldCode = "array(\n";
foreach ($fields as $field) {
    $fieldCode .= "            '{$field['Field']}' => '{$field['type']}', // {$field['Comment']}\n";
}
$fieldCode .= '        )';

$tplFile = dirname(__FILE__) . '/TplModel.class.php';
$code = file_get_contents($tplFile);
$code = str_replace('TplModel', $className, $code);
$code = str_replace('\'{DB_NAME}\'', $dbName, $code);
$code = str_replace('\'{DB_MASTER_CONFIG}\'', $dbMasterConfig, $code);
$code = str_replace('\'{DB_SLAVE_CONFIG}\'', $dbSlaveConfig, $code);
$code = str_replace('{DATE}', date('Y-m-d'), $code);
$code = str_replace('{TABLE_NAME_TPL}', $a->getTableName(), $code);
$code = str_replace('\'{FILE_TYPES_TPL}\'', $fieldCode, $code);

echo '<pre>';
echo $code;
echo '</pre>';
if (!is_file(dirname(__FILE__) . '/' . $className . '.class.php')) {
    file_put_contents(dirname(__FILE__) . '/' . $className . '.class.php', $code);
    echo 'succeed';
} else {
    echo 'file exists';
}
