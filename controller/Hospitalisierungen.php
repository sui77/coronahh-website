<?php

class Hospitalisierungen extends AbstractController {

    protected $_template = 'hospitalisierungen';

    public function action() {
        $sql = "SELECT * FROM hospitalisierungen ORDER BY date asc";

        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            $dates[] = $row['date'];
            $next = 0;
            $sum = 0;

            foreach (array_keys($row) as $columnName) {

                if ($columnName != 'date') {
                    $values[$next]['label'] = $columnName;
                    $values[$next]['values'][] = $row[$columnName];
                    $values[$next]['totalValues'][] = $row[$columnName];
                    $sum += $row[$columnName];
                    $next++;
                }
            }

        }

        $this->assign('dates', $dates);
        $this->assign('values', $values);

    }
}