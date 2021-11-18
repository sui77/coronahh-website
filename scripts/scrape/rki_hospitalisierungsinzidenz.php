<?php

$config = json_decode( file_get_contents( dirname(__FILE__) . '/../../config/config.json'), 1);
$pdo = new PDO('mysql:host=' . $config['mysql']['host'] . ';dbname=' . $config['mysql']['database'], $config['mysql']['user'], $config['mysql']['password']);
/*
$start = strtotime('2021-09-21');
for ($i=$start; $i<time(); $i += 60*60*24) {
    exec('cd ./COVID-19-Hospitalisierungen_in_Deutschland; git checkout ' . date('Y-m-d', $i));
}
*/
todb();


function todb() {
    global $pdo;

    $map = [
        '00-04' => 'n00-04',
        '05-14' => 'n05-14',
        '15-34' => 'n15-34',
        '35-59' => 'n35-59',
        '60-79' => 'n60-79',
        '80+' => 'n80p',
        '00+' => 'n',
    ];

    $fp = fopen('./COVID-19-Hospitalisierungen_in_Deutschland/Aktuell_Deutschland_COVID-19-Hospitalisierungen.csv', 'r');
    while (!feof($fp)) {
        $l = fgetcsv($fp, 255, ',', '"');



        if (isset($l[1]) && $l[1] == 'Hamburg' && isset($l[3]) && isset($map[$l[3]])) {
            $data = ['date' => $l[0], 'val' => round($l[5]*100)];
            echo $map[$l[3]];
            print_r($data);
            $stmt = $pdo->prepare('INSERT INTO rki_hospitalisierungsinzidenz (`date`, `' . $map[$l[3]]. '`) VALUES (:date, :val) ON DUPLICATE KEY UPDATE `' . $map[$l[3]]. '`=:val');
            if (!$stmt) {
                var_dump( $pdo->errorInfo());
            } else {
                $stmt->execute($data);
            }


        }

    }
}

exit();
$fp = fopen('./COVID-19-Hospitalisierungen_in_Deutschland/gitlog.txtx', 'r');

while (!feof($fp)) {
    $l = fgets($fp, 255);
    echo $l;
}
exit();
$fp = fopen('https://raw.githubusercontent.com/robert-koch-institut/COVID-19-Hospitalisierungen_in_Deutschland/master/Aktuell_Deutschland_COVID-19-Hospitalisierungen.csv', 'r');
while (!feof($fp)) {

}