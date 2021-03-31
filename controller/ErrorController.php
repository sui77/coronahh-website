<?php

class ErrorController extends AbstractController {

    protected $_template = 'error';

    public function action() {
        header('HTTP/1.0 404 not found', true, 404);
        $this->assign('error', '404');

    }
}