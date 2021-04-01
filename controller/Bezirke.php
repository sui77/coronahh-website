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
        $sql = "SELECT * FROM cases WHERE altona IS NOT NULL AND date > '2020-06-01' ORDER BY date asc";

        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            $index = 0;
            foreach ($bezirke as $bezirk => $ewz) {
                $values[$index]['label'] = ucfirst($bezirk);
                $values[$index]['values'][] = round(100000 / $ewz * $row[$bezirk], 2);
                $values[$index]['totalValues'][] = $row[$bezirk];
                $index++;
            }
        }

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('bevoelkerungszahlen', $bezirke);

    }
}