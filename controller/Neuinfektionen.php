<?php

class Neuinfektionen extends AbstractController {

    protected $_template = 'neuinfektionen';

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

        $sql = "SELECT * FROM cases ORDER BY date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            array_shift($sevenDay);
            array_push($sevenDay, $row['cases']);

            $values[] = round(100000 / $ewz * array_sum($sevenDay), 2);
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
        $this->assign('weekD', $weekD);
        $this->assign('ewz', $ewz);
        $this->assign('rtable', [
            'title' => 'Neuinfektionen Hamburg',
            'subtitle' => '',
        ]);
    }

    public function csv() {
        header('Content-type: text/plain');
        $sql = "SELECT * FROM cases ORDER BY date asc";
        $first = true;
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            if ($first == true) {
                echo implode(';', array_keys($row)) . "\n";
                $first = false;
            }
            echo implode(';', $row)."\n";

        }
        exit();
    }

}