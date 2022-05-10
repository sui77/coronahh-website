<?php

class Inzidenz extends AbstractController {

    protected $_template = 'inzidenz';

    public function action() {
        $showNumWeeks = $_GET['showWeeks'] ?? 10;
        $ewz = 1899160;
        $ewz2 = 1847253;
        $week = 0;
        $weekD = [];
        $day = 1;
        $data = [];
        $data[0] = [];
        $data2 = [];
        $data2[0] = [];

        $dates = [];
        $values = [];
        $values4 = [];
        $sevenDay = [0, 0, 0, 0, 0, 0, 0];
        $sevenDay2 = [0, 0, 0, 0, 0, 0, 0];

        $vaxxed = 0;
        $sql = "SELECT * FROM cases ORDER BY cases.date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            array_shift($sevenDay);
            array_push($sevenDay, $row['cases']??0);

            array_shift($sevenDay2);
            array_push($sevenDay2, $row['faelle_rki']??0);

            $vaxxed += $row['vaccination-2nd'];
            $values[] = round(100000 / $ewz * array_sum($sevenDay), 2);
            $values2[] = round(100000 / ($ewz-$vaxxed) * array_sum($sevenDay), 2);
            $values3[] = round(100000 / $ewz2 * array_sum($sevenDay2), 2);
            $values4[] = $row['inzidenz_rki'];
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

        $day = 1; $week=0; $weekD = [];
        $sql = "SELECT * FROM cases WHERE faelle_rki IS NOT NULL ORDER BY date desc";
        foreach ($this->_pdo->query($sql) as $row) {
            $data2[$week][7 - $day] = $row['faelle_rki']??0;
            $day++;
            if ($day > 7) {
                $weekD[$week] = $row['date'];
                $week++;
                $day = 1;
            }
        }

        $this->assign('showNumWeeks', min($showNumWeeks, count($data)-1));
        $this->assign('data', $data);
        $this->assign('data2', $data2);

        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('values2', $values2);
        $this->assign('values3', $values3);
        $this->assign('values4', $values4);

        $this->assign('weekD', $weekD);
        $this->assign('ewz', $ewz);
    }

}