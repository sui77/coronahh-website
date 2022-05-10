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
        //$this->assign('ewz', 1899160);
        $this->assign('settings', $config['settings']);

        $inzidenz = $this->_pdo->query('SELECT date, cases FROM cases_weekly ORDER BY date desc LIMIT 1');
        $r = $inzidenz->fetch();
        $inzidenz = $r['cases'] / (1904444 / 100000);
        $this->assign('navbartext', 'Inzidenz (Stand ' . date('d.m.Y', strtotime($r['date'])) . '): ' . $this->nf($inzidenz,2));
    }

    abstract public function action();

    protected function nf($n, $prec = 0) {
        return number_format($n, $prec, ',', '.');
    }

    protected function colorInzidenz($value) {
        $colors = [
            "#ffebee",
            "#ffcdd2",
            "#ef9a9a",
            "#e57373",
            "#ef5350",
            "#f44336",
            "#e53935",
            "#d32f2f",
            "#c62828",
            "#b71c1c",
            "#6b0a0a",
            "#7d0c0c",
            "#610d0d",
            "#3D0707",
            "#3D002D",
            "#2D002D",
            "#1D002D",
            "#0D002D",
            "#0D001D",
            "#0D000D",

        ];
        $colr = '';
        if ($value > 35) { $colr = $colors[0]; }
        if ($value > 50) { $colr = $colors[1]; }
        if ($value > 100) { $colr = $colors[2]; }
        if ($value > 150) { $colr = $colors[3]; }
        if ($value > 200) { $colr = $colors[4]; }
        if ($value > 250) { $colr = $colors[6]; }
        if ($value > 300) { $colr = $colors[8]; }
        if ($value > 400) { $colr = $colors[9]; }
        if ($value > 500) { $colr = $colors[11] . ';color:#aaa'; }
        if ($value > 600) { $colr = $colors[12] . ';color:#aaa'; }
        if ($value > 700) { $colr = $colors[13] . ';color:#aaa'; }
        if ($value > 1000) { $colr = $colors[14] . ';color:#aaa'; }
        if ($value > 1250) { $colr = $colors[15] . ';color:#aaa'; }
        if ($value > 1500) { $colr = $colors[16] . ';color:#aaa'; }
        if ($value > 1750) { $colr = $colors[17] . ';color:#aaa'; }
        if ($value > 2000) { $colr = $colors[18] . ';color:#aaa'; }
        if ($value > 2250) { $colr = $colors[19] . ';color:#aaa'; }

        return $colr;
    }

    public function initNavigation() {
        $this->assign('navigation', [
            'neuinfektionen2022' => [
                'section' => 'Aktuell',
                'title' => 'Neuinfektionen',
            ],

            'altersgruppenrki' => [
                'title' => 'Altersgruppen (RKI)',
            ],

            'hospitalisierungen' => [
                'title' => 'Hospitalisierungen',
            ],
            'intensivbelegung' => [
                'title' => 'Intensivbelegung',
            ],
            'hospitalisierungsinzidenz' => [
                'title' => 'Hospitalisierungsinzidenz',
            ],
            'pcrtests' => [
                'title' => 'PCR Tests',
                'visible' => true,
            ],

            'altersgruppen' => [
                'title' => 'Altersgruppen (Senat)',
                'section' => 'Archiv',
            ],
            'neuinfektionen' => [
                'title' => 'Neuinfektionen (täglich)',
            ],
            'todesfaelle' => [
                'title' => 'Todesfälle',
            ],
            'bezirke' => [
                'title' => 'Bezirke',
            ],
            'impfungen' => [
                'title' => 'Impfungen',
                'visible' => false, //$this->config['settings']['dev'],
            ],
            'faq' => [
                'title' => '(FAQ)',
                'visible' => false, // $this->config['settings']['dev'],
            ],
            'kontakt' => [
                'title' => 'Kontakt',
                'visible' => false,
            ],
            'impressum' => [
                'section' => 'Info',
                'title' => 'Impressum',
            ],
            'test' => [
                'title' => 'Test',
                'visible' => false, //$this->config['settings']['dev'],
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

    protected function _csv($sql, $copyright='') {
            header('Content-type: text/csv');
            header('Access-Control-Allow-Origin: *');
            header('Content-Disposition: attachment; filename=coronahh-' . $this->_template . '-' . date('Y-m-d') . '.csv');
            $first = true;
            if ($copyright != '') {
                echo 'Quelle: ' . $copyright . "\n";
            }
            foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
                if ($first == true) {
                    echo implode(';', array_keys($row)) . "\n";
                    $first = false;
                }
                echo implode(';', $row)."\n";
            }
    }

}