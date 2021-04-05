<?php

class Hospitalisierungen extends AbstractController {

    protected $_template = 'hospitalisierungen';

    public function action() {
        $sql = "SELECT date, weekday, stationaer,  IF (normalstationhh is null, normalstation,null) as normalstation_gesamt, normalstation, normalstationhh, normalstation_nichthh, intensivstation, intensivstationhh, intensivstation_nichthh FROM hospitalisierungen ORDER BY date asc";

        $valuesTable = [];
        $datesTable = [];
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            if (!empty($row['weekday'])) {
                $datesTable[] = $row['date'];
            }
            $dates[] = $row['date'];

            $next = 0;
            $nextDt = 0;
            foreach (array_keys($row) as $columnName) {

                if ($columnName != 'date' && $columnName != 'weekday') {

                    $values[$next]['label'] = $columnName;
                    $values[$next]['values'][] = $row[$columnName];
                    $values[$next]['totalValues'][] = $row[$columnName];

                    if (!empty($row['weekday'])) {
                        $valuesTable[$nextDt]['label'] = $columnName;
                        $valuesTable[$nextDt]['values'][] = $row[$columnName];
                        $valuesTable[$nextDt]['totalValues'][] = $row[$columnName];
                        $nextDt++;
                    }

                    $next++;
                }

            }


        }

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('valuesTable', $valuesTable);
        $this->assign('datesTable', $datesTable);


    }

    public function csv() {
        $sql = "SELECT date, stationaer,  IF (normalstationhh is null, normalstation,null) as normalstation_gesamt, normalstation, normalstationhh, normalstation_nichthh, intensivstation, intensivstationhh, intensivstation_nichthh, weekday FROM hospitalisierungen ORDER BY date asc";
        $this->_csv($sql);
    }
}