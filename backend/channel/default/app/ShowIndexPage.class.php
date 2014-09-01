<?php
class ShowIndexPage extends BackendPage {
    public function defaultAction() {
        $this->render(array(
        ), 'index.php');
    }
}

