<?php

/**
 * Class IndexController
 * @author chenchaoyang@iyuesao.com
 * @since  2015-01-11
 */
class IndexController extends BcController {
    public function init() {
        //echo 'test';
    }
    public function defaultAction() {
        $data = array();
        $tpl = 'index.php';
        $content = $this->view->fetch($tpl, $data);
        echo $content;
    }
}



