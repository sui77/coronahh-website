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
        $population = array_sum($bezirke);

        $sql = "SELECT date, altona, bergedorf, eimsbuettel, mitte, nord, harburg, wandsbek FROM cases WHERE altona IS NOT NULL AND date > '2020-06-01' ORDER BY date asc";

        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = date('W/Y', strtotime($row['date'])-60*60*24*7);
            $next = 0;
            $sum = 0;
            foreach ($bezirke as $bezirk => $ewz) {
                $values[$next]['label'] = ucfirst($bezirk);
                $values[$next]['values'][] = round(100000 / $ewz * $row[$bezirk], 2);
                $values[$next]['totalValues'][] = $row[$bezirk];
                $sum += $row[$bezirk];
                $next++;
            }
            $values[$next]['label'] = 'Ø';
            $values[$next]['values'][] = round(100000 / $population * $sum, 2);
            $values[$next]['totalValues'][] = $sum;
        }

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('bevoelkerungszahlen', $bezirke);

    }

    public function csv() {
        $sql = "SELECT date, altona, bergedorf, eimsbuettel, mitte, nord, harburg, wandsbek FROM cases WHERE altona IS NOT NULL ORDER BY date asc";
        $this->_csv($sql);
    }
}