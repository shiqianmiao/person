<?PHP
class BaseImpl {

    protected static function getModel($modelName, $id='') {
        $file = API_PATH . '/model/' . $modelName . '.class.php';
        if (!is_file($file)) {
            trigger_error('model不存在[' . $modelName . ']');
        }
        include_once $file;
        return new $modelName($id);
    }
}