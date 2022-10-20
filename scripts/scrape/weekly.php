<?php

$config = json_decode( file_get_contents( dirname(__FILE__) . '/../../config/config.json'), 1);
$importer = new Importer($config);
$importer->import();

class Importer {

    protected $pdo;

    public function __construct($config) {
        $this->pdo = new PDO('mysql:host=' . $config['mysql']['host'] . ';dbname=' . $config['mysql']['database'], $config['mysql']['user'], $config['mysql']['password']);
    }


    public function import() {


        exec('curl https://www.hamburg.de/corona-zahlen', $out);
        $data = implode('', $out);

        $m = [];
        preg_match_all('/<td data-label="Zahlen">(.*?)<\/td><td data-label="Wert">(.*?)<\/td><td data-label="Stand">(.*?)<\/td>/', $data, $m);

        foreach ($m[1] as $n => $v) {
            $m[3][$n] = implode('-', array_reverse( explode('.', $m[3][$n])));
            if ($v == '7-Tage-Inzidenz') {
                $inzidenz = [ 'value' => $m[2][$n], 'date' => $m[3][$n] ];
            } else if ($v == 'Neuinfektionen vergangene 7 Tage') {
                $neuinfektionen = [ 'value' => str_replace('.', '', $m[2][$n]), 'date' => $m[3][$n] ];
            } else if ($v == 'Patienten Krankenhaus (gesamt)') {
                $patientenGesamt = [ 'value' => $m[2][$n], 'date' => $m[3][$n] ];
            } else if ($v == 'Patienten auf Intensivstationen') {
                $patientenIntensiv = [ 'value' => $m[2][$n], 'date' => $m[3][$n] ];
            }
        }


        $data = $this->pdo->query('SELECT max(date) as mdate FROM cases_weekly')->fetch();
        $lastdateInz = $data['mdate'];
        $data = $this->pdo->query('SELECT max(date) as mdate FROM hospitalisierungen_2')->fetch();
        $lastdateHosp = $data['mdate'];


        if (isset($neuinfektionen) && $neuinfektionen['date'] != $lastdateInz) {
            echo "=== Inzidenz neu === \n";
            $this->pdo->prepare('INSERT IGNORE INTO cases_weekly (date, cases) VALUES (:date, :value)')
                ->execute($neuinfektionen);
            exec('service memcached restart');
        }

        if (isset($patientenGesamt) && $patientenGesamt['date'] != $lastdateHosp) {
            echo "=== Hospitalisierungen neu === \n";
            $data = [
                'date' => $patientenGesamt['date'],
                'intensivstation' => $patientenIntensiv['value'],
                'normalstation' => $patientenGesamt['value'] - $patientenIntensiv['value'],
            ];
            $this->pdo->prepare('INSERT IGNORE INTO hospitalisierungen_2 (date, normalstation, intensivstation) VALUES (:date, :normalstation, :intensivstation) ON DUPLICATE KEY UPDATE normalstation=:normalstation, intensivstation=:intensivstation')
                ->execute($data);
            exec('service memcached restart');
        }

        for ($i = time()-60*60*24*14; $i< time()-60*60*24; $i=$i+60*60*24) {
            $this->pdo->prepare('INSERT IGNORE INTO hospitalisierungen_2 (date) VALUES (:date)')
                ->execute(['date' => date('Y-m-d', $i)]);
        }


    }
}