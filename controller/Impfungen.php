<?php

class Impfungen extends AbstractController {

    protected $_template = 'impfungen';

    public function action() {
        $sql = "SELECT * FROM impfungen ORDER BY date";

        $erstTotal = 0;
        $zweitTotal = 0;

        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            $values[0]['label'] = 'Erstimpfungen';
            $values[1]['label'] = 'Zweitimpfungen';
            $values[0]['values'][] = $row['erstimpfung'];
            $values[1]['values'][] = $row['zweitimpfung'];
$erstTotal += $row['erstimpfung'];
$zweitTotal += $row['zweitimpfung'];
            $tableValues[0]['label'] = 'Erstimpfungen kumuliert';
            $tableValues[1]['label'] = 'Zweitimpfungen kumuliert';
            $tableValues[0]['values'][] = $erstTotal;
            $tableValues[1]['values'][] = $zweitTotal;

        }
        $this->assign('rtable', [
            'title' => 'Impfungen',
            'subtitle' => 'Quelle: https://www.hamburg.de/corona-zahlen',
        ]);

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('tableValues', $tableValues);
    }

}