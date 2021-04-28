<?php

class Hospitalisierungen extends AbstractController {

    protected $_template = 'hospitalisierungen';

    protected $table = 'hospitalisierungen_2';

    public function action() {
        $table = $this->table;
        $sql = "SELECT date, weekday,   intensivstation, intensivstationhh, intensivstation_nichthh, normalstation, normalstationhh, normalstation_nichthh FROM $table ORDER BY date asc";

        $valuesTable = [];
        $datesTable = [];
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {

            $dates[] = $row['date'];

            $next = 0;
            $nextDt = 0;
            foreach (array_keys($row) as $columnName) {

                if ($columnName != 'date' && $columnName != 'weekday') {

                    if (in_array($columnName, ['intensivstationhh', 'intensivstation_nichthh', 'normalstationhh', 'normalstation_nichthh'])) {
                        $values[$next]['label'] = $columnName;
                        $values[$next]['values'][] = $row[$columnName];
                    }

                    //if (!empty($row['weekday']) && $row['weekday'] != '-') {
                        $valuesTable[$nextDt]['label'] = $columnName;
                        $valuesTable[$nextDt]['values'][] = $row[$columnName] ?? '-';
                        $nextDt++;
                    //}

                    $next++;
                }

            }


        }

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('valuesTable', $valuesTable);



    }

    public function csv() {
        $sql = "SELECT date, stationaer,  normalstation, normalstationhh, normalstation_nichthh, intensivstation, intensivstationhh, intensivstation_nichthh, weekday FROM hospitalisierungen ORDER BY date asc";
        $this->_csv($sql);
    }
}