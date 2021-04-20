<?php

class Neuinfektionen extends AbstractController {

    protected $_template = 'neuinfektionen';

    public function action() {
        $showNumWeeks = $_GET['showWeeks'] ?? 10;
        $ewz = 1899160;
        $week = 0;
        $weekD = [];
        $day = 1;
        $data = [];
        $data[0] = [];

        $dates = [];
        $values = [];
        $sevenDay = [0, 0, 0, 0, 0, 0, 0];

        $sql = "SELECT * FROM cases ORDER BY cases.date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            array_shift($sevenDay);
            array_push($sevenDay, $row['cases']??0);

            $values[] = round(100000 / $ewz * array_sum($sevenDay), 2);
            $values2[] = $row['value']??0;
        }

        $sql = "SELECT * FROM cases ORDER BY date desc";
        foreach ($this->_pdo->query($sql) as $row) {

            $data[$week][7 - $day] = $row['cases']??0;
            $day++;
            if ($day > 7) {
                $weekD[$week] = $row['date'];
                $week++;
                $day = 1;
            }
        }

        $this->assign('showNumWeeks', min($showNumWeeks, count($data)-1));
        $this->assign('data', $data);
        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('values2', $values2);

        $this->assign('weekD', $weekD);
        $this->assign('ewz', $ewz);
    }

    public function csv() {
        $sql = "SELECT date, cases as neuinfektionen FROM cases ORDER BY date asc";
        $this->_csv($sql);
    }

}