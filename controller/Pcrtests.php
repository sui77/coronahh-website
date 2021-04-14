<?php

class Pcrtests extends AbstractController {

    protected $_template = 'pcrtests';

    public function action() {
        $sql = "SELECT * FROM briefing_test ORDER BY year, week asc";

        $dates = [];
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            $dates[] = $row['week'] . '/' . $row['year'];
            $values[0]['values'][] = $row['number'];
            $values[1]['values'][] = $row['positive']*100;
        }
        $values[0]['label'] = 'Tests';
        $values[1]['label'] = 'Positivenrate';

        $this->assign('dates', $dates);
        $this->assign('values', $values);
    }

    public function csv() {
        $sql = "SELECT * FROM briefing_test ORDER BY year, week asc";
        $this->_csv($sql);
    }
}