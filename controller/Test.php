<?php

class Test extends AbstractController {

    protected $_template = 'test';

    public function action() {
        $showNumWeeks = 10;
        $ewz = 1899160;
        $week = 0;
        $weekD = [];
        $day = 1;
        $data = [];
        $data[0] = [];

        $dates = [];
        $values = [];
        $sevenDay = [0, 0, 0, 0, 0, 0, 0];

        $vaxxed = 0;
        $sql = "SELECT * FROM cases WHERE date > '2021-01-01' ORDER BY date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            array_shift($sevenDay);
            array_push($sevenDay, $row['cases']);
            $vaxxed += $row['vaccination-2nd'];
            $values[] = round(100000 / $ewz * array_sum($sevenDay), 2);
            $values2[] = round(100000 / ($ewz-$vaxxed) * array_sum($sevenDay), 2);
        }

        $sql = "SELECT * FROM cases ORDER BY date desc";
        foreach ($this->_pdo->query($sql) as $row) {

            $data[$week][7 - $day] = $row['cases'];
            $day++;
            if ($day > 7) {
                $weekD[$week] = $row['date'];
                $week++;
                $day = 1;
            }
        }

        $this->assign('showNumWeeks', $showNumWeeks);
        $this->assign('data', $data);
        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('values2', $values2);
        $this->assign('weekD', $weekD);
        $this->assign('ewz', $ewz);
    }

}