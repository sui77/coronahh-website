<?php

abstract class AbstractController {

    protected $_view = [];
    protected $_template = '404';
    protected $_pdo;

    public function __construct() {
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config/config.json'), 1);
        $this->config = $config;
        $this->_pdo = new PDO('mysql:host=' . $config['mysql']['host'] . ';dbname=' . $config['mysql']['database'], $config['mysql']['user'], $config['mysql']['password']);
        $this->assign('page', $this->_template);
        $this->initNavigation();
        $this->action();
        $this->assign('ewz', 1899160);
        $this->assign('settings', $config['settings']);
    }

    abstract public function action();

    protected function nf($n, $prec = 0) {
        return number_format($n, $prec, ',', '.');
    }

    public function initNavigation() {
        $this->assign('navigation', [

            'neuinfektionen' => [
                'title' => 'Neuinfektionen',

            ],
            'todesfaelle' => [
                'title' => 'Todesfälle',
            ],

            'hospitalisierungen' => [
                'title' => '(3 Hospitalisierungen)',
                'visible' => $this->config['settings']['dev'],
            ],
            'altersgruppen' => [
                'title' => 'Altersgruppen',
            ],
            'bezirke' => [
                'title' => 'Bezirke',
            ],
            'impfungen' => [
                'title' => 'Impfungen',
            ],
            'faq' => [
                'title' => '(FAQ)',
                'visible' => $this->config['settings']['dev'],
            ],
            'impressum' => [
                'title' => 'Impressum',
            ],
            'test' => [
                'title' => 'Test',
            ]
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