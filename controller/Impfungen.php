<?php

class Impfungen extends AbstractController {

    protected $_template = 'impfungen';

    public function action() {
        $sql = "SELECT `date`, `vaccination-1st`, `vaccination-2nd` FROM cases WHERE date > '2020-12-26' and `vaccination-1st` IS NOT NULL ORDER BY date";

        $erstTotal = 0;
        $zweitTotal = 0;

        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            $values[0]['label'] = 'Erstimpfungen';
            $values[1]['label'] = 'Zweitimpfungen';
            $values[0]['values'][] = $row['vaccination-1st'];
            $values[1]['values'][] = $row['vaccination-2nd'];

            $erstTotal += $row['vaccination-1st'];
            $zweitTotal += $row['vaccination-2nd'];
            $tableValues[1]['label'] = 'Erstimpfungen kumuliert';
            $tableValues[0]['label'] = 'Zweitimpfungen kumuliert';
            $tableValues[1]['values'][] = $erstTotal;
            $tableValues[0]['values'][] = $zweitTotal;

        }
        $this->assign('rtable', [
            'title' => 'Impfungen',
            'subtitle' => 'Quelle: https://www.hamburg.de/corona-zahlen',
        ]);

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('tableValues', $tableValues);
    }

    public function csv() {
        $sql = "SELECT `date`, `vaccination-1st`, `vaccination-2nd` FROM cases WHERE date > '2020-12-26' and `vaccination-1st` IS NOT NULL ORDER BY date";
        parent::_csv($sql);
    }

}