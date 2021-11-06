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
        $data = fopen('https://datawrapper.dwcdn.net/dRfTF/283/dataset.csv', 'r');
        while ($l = fgetcsv($data, 4000, ",", '"')) {
            $date = substr($l[0], 0, 10);
            if (strlen($date) == 10) {
                $this->pdo->prepare('INSERT IGNORE INTO divi_intensivbetten (date, belegt, frei, notfall) VALUES (:date, :belegt, :frei, :notfall)')
                    ->execute(['date' => $date, 'belegt' => $l[1], 'frei' => $l[2], 'notfall' => $l[3]]);
            }
            //print_r($l);
        }
    }

}