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

        $json = json_decode(file_get_contents('https://api.corona-zahlen.org/states/history/cases'), 1);

        foreach ($json['data']['HH']['history'] as $data) {

            $date = substr($data['date'], 0, 10);
            print_r($data);
            echo $date . "\n";
            $this->pdo->prepare('INSERT IGNORE INTO cases_rki_marlon (date, cases) VALUES (:date, :cases)')
                ->execute(['date' => $date, 'cases' => $data['cases']]);

        }

    }

}