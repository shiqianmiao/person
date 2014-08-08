<?php

require_once dirname(__FILE__) . '/include/SsoBasePage.class.php';

class IndexPage extends SsoBasePage {
    public function defaultAction() {
        
        $this->render(array(), 'index.php');
    }
}