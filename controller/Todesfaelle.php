<?php

class Todesfaelle extends AbstractController {

    protected $_template = 'todesfaelle';

    public function action() {
        $showNumWeeks = 15;
        $ewz = 1899160;
        $week = 0;
        $weekD = [];
        $day = 1;
        $data = [];
        $data[0] = [];

        $dates = [];
        $values = [];
        $sevenDay = [0, 0, 0, 0, 0, 0, 0];

        $sql = "SELECT * FROM cases WHERE date > '2020-05-24' ORDER BY date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            array_shift($sevenDay);
            array_push($sevenDay, $row['deaths']);
            $values[] = $row['deaths'];
        }

        $sql = "SELECT * FROM cases ORDER BY date desc";
        foreach ($this->_pdo->query($sql) as $row) {

            $data[$week][7 - $day] = $row['deaths'];
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
            'title' => 'Todesfälle Hamburg',
            'subtitle' => '',
        ]);
    }

}