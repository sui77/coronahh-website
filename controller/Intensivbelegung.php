<?php

class Intensivbelegung extends AbstractController {

    protected $_template = 'intensivbelegung';

    public function action() {

        $valuesTable = [];

        $sql = "SELECT date, frei, belegt, frei+belegt as gesamt, 100/(frei+belegt)*frei as prozentual, notfall as notfallreserve FROM divi_intensivbetten WHERE date > now() - INTERVAL 18 MONTH ORDER BY date asc";
        //$values2[0]['label'] = 'foo';
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            $next = 0;
            $nextDt = 0;
            $dates2[] = $row['date'];
            //$tbl = [$row['date']];
            foreach (array_keys($row) as $columnName) {
                if ($columnName != 'date') {
                    $values2[$next]['label'] = $columnName;
                    $values2[$next]['values'][] = $row[$columnName];
                    $tbl[$nextDt]['label'] = $columnName;
                    if ($columnName == 'prozentual') {
                        $tbl[$nextDt]['values'][] = number_format($row[$columnName]*1, 2, ',', '.' ) . ' %';
                    } else {
                        $tbl[$nextDt]['values'][] = $row[$columnName] ?? '-';
                    }
                    $next++;
                    $nextDt++;
                }

            }
/*
            $values2[$next]['label'] = ucfirst($columnName);
            $values2[$next]['values'][] = 100/($row['frei']+$row['belegt']) * $row['frei'];
*/




        }



        $this->assign('dates2', $dates2);
        $this->assign('values2', $values2);

        $this->assign('valuesTable', $tbl);



    }

    public function csv() {
        $sql = "SELECT date, frei, belegt, notfall as notfallreserve, 100/(frei+belegt)*frei as prozentual FROM divi_intensivbetten ORDER BY date asc";
        $this->_csv($sql);
    }
}