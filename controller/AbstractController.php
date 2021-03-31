<?php

abstract class AbstractController {

    protected $_view = [];
    protected $_template = '404';
    protected $_pdo;

    public function __construct() {
        $this->_pdo = new PDO('mysql:host=localhost;dbname=coronahh', 'website', '');
        $this->assign('page', $this->_template);
        $this->initNavigation();
        $this->action();
        $this->assign('ewz', 1899160);
    }

    abstract public function action();

    protected function nf($n, $prec=0) {
        return number_format($n, $prec, ',', '.');
    }

    public function initNavigation() {
        $this->assign('navigation', [
            'neuinfektionen' => 'Neuinfektionen',
            'todesfaelle' => 'Todesfälle',
            //'hospitalisierungen' => '(3 Hospitalisierungen)',
            'altersgruppen' => 'Altersgruppen',
            'bezirke' => 'Bezirke',
            'impfungen' => 'Impfungen',
            //'_faq' => '(FAQ)',
            'impressum' => 'Impressum',
            //'test' => '(Test)'
        ]);
    }


    public function assign($key, $value) {
        $this->_view[$key] = $value;
    }

    public function render() {
        foreach ($this->_view as $k => $v) {
            $$k = $v;
        }
        include dirname(__FILE__) . '/../view/layout.phtml';
    }

}