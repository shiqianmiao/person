<?php

class ErrorPage extends BackendPage {
    private $errorInfo;

    public function init () {
        $this->errorInfo = array(
            'errorCode'    => RequestUtil::getGeT('errorCode', 1),
            'errorMessage' => RequestUtil::getGeT('errorMessage', '操作失败，请重试! '),
        );
    }

    public function defaultAction() {
        $this->render($this->errorInfo, 'error.php');
    }

    public function ajaxAction() {
        $this->render($this->errorInfo);
    }

    public function iframeAction() {
        
        $this->render($this->errorInfo, 'error_iframe.php');
    }
}