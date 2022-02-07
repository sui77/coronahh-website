<?php

class Altersgruppen extends AbstractController {

    protected $_template = 'altersgruppen';

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
        $bg['80-89'] = $b['a80'] + $b['a81'] + $b['a82'] + $b['a83'] + $b['a84'] + $b['a85'] + $b['a86'] + $b['a87'] + $b['a88'] + $b['a89'];
        $bg['plus90'] = $b['a90'];


        $sql = "SELECT kw as week, jahr as year, bis5, `6-14`, `15-19`, `20-29`, `30-39`,`40-49`,`50-59`,`60-69`,`70-79`, `80-89`, `plus90` FROM alter_senat_gruppen_ab_2020_10_13_2 ORDER BY year,week asc";

        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            if ($row['week'] == 41 && $row['year'] == 2020) {
                continue;
            }
            if ($row['year'] >= 2022 && $row['week'] >2 || $row['year'] > 2022) {
                $week = $row['week']<10?'0'.$row['week']:$row['week'];
                $t = strtotime($row['year']."W".$week."4");
                $date1 = date( "d.m.y", $t );
                $date2 = date( "d.m.y", $t + 60*60*27*7 );
                $dates[] = $date1 . ' - ' . $date2; // "yup"; //$row['week'] . '/' . $row['year'];
            } else {
                $dates[] = $row['week'] . '/' . $row['year'];
            }
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

        $januar2022 = array_search('2/2022', $dates);
        $dates[$januar2022] = '2/2022 *';
        for ($i=0; $i<$next; $i++) {
            //$values[$i]['values'][$januar2022] = $values[$i]['values'][$januar2022] *7/10;
        }


        $this->assign('rtable', [
            'title' => 'Infektionen nach Altersgruppen',
            'subtitle' => 'Quelle: https://survstat.rki.de',
        ]);

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('bevoelkerungszahlen', $bg);
    }

    public function csv() {
        $sql = "SELECT kw as week, jahr as year, bis5, `6-14`, `15-19`, `20-29`, `30-39`,`40-49`,`50-59`,`60-69`,`70-79`, `80-89`, `plus90` FROM alter_senat_gruppen_ab_2020_10_13_2 ORDER BY year,week asc";
        $this->_csv($sql);
    }
}