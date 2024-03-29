<?php

class Altersgruppenrki extends AbstractController {

    protected $_template = 'altersgruppenrki';

    public function data3d() {
        $first = true;
        $sql = "SELECT concat(kw, '-', jahr) as date, bis5, `6-14`, `15-19`, `20-29`, `30-39`,`40-49`,`50-59`,`60-69`,`70-79`, `80-89`, `plus90` FROM alter_senat_gruppen_ab_2020_10_13_2 ORDER BY jahr,kw asc";

echo "ok";
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            if ($first == true) {
                //echo ',0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
                echo implode(',', array_keys($row)) . "\n";
                echo "\n";
                $first = false;
            }
            echo implode(',', $row)."\n";
        }
    }

    public function action() {

        $b = [];
        $population = 0;
        $sql = 'SELECT * FROM bevoelkerung_2';
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            $b[$row['alter_id']] = $row['sum'];
            $population += $row['sum'];
        }

        $bg['bis5'] = $b['a0'] + $b['a1'] + $b['a2'] + $b['a3'] + $b['a4'] + $b['a5'];
        $bg['6-14'] = $b['a6'] + $b['a7'] + $b['a8'] + $b['a9'] + $b['a10'] + $b['a11'] + $b['a12'] + $b['a13'] + $b['a14'];
        $bg['15-19'] = $b['a15'] + $b['a16'] + $b['a17'] + $b['a18'] + $b['a19'];
        $bg['20-29'] = $b['a20'] + $b['a21'] + $b['a22'] + $b['a23'] + $b['a24'] + $b['a25'] + $b['a26'] + $b['a27'] + $b['a28'] + $b['a29'];
        $bg['30-39'] = $b['a30'] + $b['a31'] + $b['a32'] + $b['a33'] + $b['a34'] + $b['a35'] + $b['a36'] + $b['a37'] + $b['a38'] + $b['a39'];
        $bg['40-49'] = $b['a40'] + $b['a41'] + $b['a42'] + $b['a43'] + $b['a44'] + $b['a45'] + $b['a46'] + $b['a47'] + $b['a48'] + $b['a49'];
        $bg['50-59'] = $b['a50'] + $b['a51'] + $b['a52'] + $b['a53'] + $b['a54'] + $b['a55'] + $b['a56'] + $b['a57'] + $b['a58'] + $b['a59'];
        $bg['60-69'] = $b['a60'] + $b['a61'] + $b['a62'] + $b['a63'] + $b['a64'] + $b['a65'] + $b['a66'] + $b['a67'] + $b['a68'] + $b['a69'];
        $bg['70-79'] = $b['a70'] + $b['a71'] + $b['a72'] + $b['a73'] + $b['a74'] + $b['a75'] + $b['a76'] + $b['a77'] + $b['a78'] + $b['a79'];
        $bg['plus80'] = $b['a80'] + $b['a81'] + $b['a82'] + $b['a83'] + $b['a84'] + $b['a85'] + $b['a86'] + $b['a87'] + $b['a88'] + $b['a89'] + $b['a90'];


        $updatetime = 'SELECT date FROM scraping_updates WHERE url="survstat"';
        $udt = $this->_pdo->query($updatetime, PDO::FETCH_ASSOC)->fetch();
        $udt = date( 'd.m.Y H:i', strtotime($udt['date']));


        $sql = "SELECT week, year, 
                                                a0+a1+a2+a3+a4+a5 as bis5, 
                                                a6+a7+a8+a9+a10+a11+a12+a13+a14 as `6-14`, 
                                                a15+a16+a17+a18+a19 as `15-19`, 
                                                a20+a21+a22+a23+a24+a25+a26+a27+a28+a29 as `20-29`,
                                                a30+a31+a32+a33+a34+a35+a36+a37+a38+a39 as `30-39`,
                                                a40+a41+a42+a43+a44+a45+a46+a47+a48+a49 as `40-49`,
                                                a50+a51+a52+a53+a54+a55+a56+a57+a58+a59 as `50-59`,
                                                a60+a61+a62+a63+a64+a65+a66+a67+a68+a69 as `60-69`,
                                                a70+a71+a72+a73+a74+a75+a76+a77+a78+a79 as `70-79` ,                                     
                                                a80 as `plus80`                                                
                                                FROM alter_rki 
                                                ORDER BY year,week asc ";



        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {

            if (!isset($_GET['all']) && $row['week'] == date('W') && $row['year'] == date('Y')) {
               // continue;
            }
            $dates[] = $row['week'] . '/' . $row['year'];
            $next = 0;

            $sum = 0;
            foreach (array_keys($row) as $columnName) {

                if ($columnName != 'week' && $columnName != 'year') {
                    $values[$next]['label'] = $columnName;
                    $values[$next]['values'][] = round(100000 / $bg[$columnName] * $row[$columnName], 2);
                    $values[$next]['totalValues'][] = $row[$columnName];
                    $sum += $row[$columnName];
                    $next++;
                }
            }
            $values[$next]['label'] = 'Ø';
            $values[$next]['values'][] = round(100000 / $population * $sum, 2);
            $values[$next]['totalValues'][] = $sum;
        }


        $this->assign('rtable', [
            'title' => 'Infektionen nach Altersgruppen',
            'subtitle' => 'Quelle: https://survstat.rki.de',
        ]);

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('udt', $udt);
        $this->assign('bevoelkerungszahlen', $bg);
    }

    public function csv() {
        $updatetime = 'SELECT date FROM scraping_updates WHERE url="survstat"';
        $udt = $this->_pdo->query($updatetime, PDO::FETCH_ASSOC)->fetch();
        $udt = date( 'd.m.Y H:i', strtotime($udt['date']));

        $sql = "SELECT * FROM alter_rki ORDER BY year,week asc";
        $this->_csv($sql, 'Robert Koch-Institut: SurvStat@RKI 2.0, https://survstat.rki.de, Abfragedatum: ' . $udt);
    }
}