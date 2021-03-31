<?php

class Bezirke extends AbstractController {

    protected $_template = 'bezirke';

    public function action() {
        $bezirke = [
            'altona' => 275265,
            'bergedorf' => 130260,
            'eimsbuettel' => 267053,
            'mitte' => 301546,
            'nord' => 314595,
            'harburg' => 169426,
            'wandsbek' => 441015
        ];
        $sql = "SELECT * FROM cases WHERE date > '2020-06-01' ORDER BY date asc";
        foreach ($bezirke as $bezirk => $ewz) {
            $sevenDay[$bezirk] = [0]; //,0,0,0,0,0,0];
        }
        foreach ($this->_pdo->query($sql) as $row) {
            if ($row['altona']+$row['bergedorf']+$row['eimsbuettel']+$row['mitte']+$row['nord']+$row['harburg']+$row['wandsbek'] > 0) {
                $dates[] = $row['date'];
            }
            $index = 0;
            foreach ($bezirke as $bezirk => $ewz) {
                array_shift($sevenDay[$bezirk]);
                array_push($sevenDay[$bezirk], $row[$bezirk]);

                if ($row['altona']+$row['bergedorf']+$row['eimsbuettel']+$row['mitte']+$row['nord']+$row['harburg']+$row['wandsbek'] > 0) {
                    $values[$index]['label'] = ucfirst($bezirk);
                    $values[$index]['values'][] = round(100000 / $ewz * array_sum($sevenDay[$bezirk]), 2);
                    $values[$index]['totalValues'][] = array_sum($sevenDay[$bezirk]);
                }
                $index++;
            }
        }

        $this->assign('rtable', [
            'title' => 'Inzidenz nach Bezirken',
            'subtitle' => 'Quelle: https://www.hamburg.de/corona-zahlen',
        ]);

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('bevoelkerungszahlen', $bezirke);

    }
}