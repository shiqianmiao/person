<?php

class IndexPage extends BackendPage {
    public function defaultAction() {
        $this->render(array(), 'index.php');
    }
}
