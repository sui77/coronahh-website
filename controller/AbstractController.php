<?php

abstract class AbstractController {

    protected $_view = [];
    protected $_template = '404';
    protected $_pdo;

    public function __construct($params) {
        $this->params = $params;
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config/config.json'), 1);
        $this->config = $config;
        $this->_pdo = new PDO('mysql:host=' . $config['mysql']['host'] . ';dbname=' . $config['mysql']['database'], $config['mysql']['user'], $config['mysql']['password']);
        $this->initNavigation();
        $this->assign('ewz', 1899160);
        $this->assign('settings', $config['settings']);

        $inzidenz = $this->_pdo->query('SELECT max(date) as maxdate, 100000/1899160*sum(cases) AS inzidenz FROM cases WHERE date >= DATE(NOW()) - INTERVAL 6 DAY');
        $r = $inzidenz->fetch();
        $this->assign('navbartext', 'Aktuelle Inzidenz (' . date('d.m.Y', strtotime($r['maxdate'])) . '): ' . $this->nf($r['inzidenz'],2));
    }

    abstract public function action();

    protected function nf($n, $prec = 0) {
        return number_format($n, $prec, ',', '.');
    }

    public function initNavigation() {
        $this->assign('navigation', [
            'altersgruppen' => [
                'title' => 'Altersgruppen',
            ],
            'neuinfektionen' => [
                'title' => 'Neuinfektionen',

            ],
            'todesfaelle' => [
                'title' => 'Todesfälle',
            ],

            'hospitalisierungen' => [
                'title' => 'Hospitalisierungen',
                'visible' => $this->config['settings']['dev'],
            ],
            'bezirke' => [
                'title' => 'Bezirke',
            ],
            'impfungen' => [
                'title' => 'Impfungen',
                'visible' => $this->config['settings']['dev'],
            ],
            'faq' => [
                'title' => '(FAQ)',
                'visible' => $this->config['settings']['dev'],
            ],
            'kontakt' => [
                'title' => 'Kontakt',
            ],
            'impressum' => [
                'title' => 'Impressum',
            ],
            'test' => [
                'title' => 'Test',
                'visible' => $this->config['settings']['dev'],
            ]
        ]);
    }

    public function process() {

        if (isset($this->params[2]) && $this->params[2]=='data.csv' && method_exists($this, 'csv')) {
            $this->csv();
        } else if (count($this->params)>2) {
            $this->_template = 'error';
            header('HTTP/1.0 404 not found', true, 404);
            $this->assign('error', '404');
            $this->render();
            exit();
        } else {
            $this->action();
            $this->render();
        }
    }

    public function assign($key, $value) {
        $this->_view[$key] = $value;
    }

    public function render() {
        $this->assign('page', $this->_template);
        foreach ($this->_view as $k => $v) {
            $$k = $v;
        }
        include dirname(__FILE__) . '/../view/layout.phtml';
    }

    protected function _csv($sql) {
            header('Content-type: text/csv');
            header('Access-Control-Allow-Origin: *');
            header('Content-Disposition: attachment; filename=coronahh-' . $this->_template . '-' . date('Y-m-d') . '.csv');
            $first = true;
            foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
                if ($first == true) {
                    echo implode(';', array_keys($row)) . "\n";
                    $first = false;
                }
                echo implode(';', $row)."\n";
            }
    }

}