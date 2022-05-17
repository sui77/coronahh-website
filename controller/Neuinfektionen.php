<?php

class Neuinfektionen extends AbstractController {

    protected $_template = 'neuinfektionen';

    public function action() {
        $showNumWeeks = $_GET['showWeeks'] ?? 10;
        $ewz = 1899160;
        $ewza = [
            '2020-02-29' => 1899160,
            '2021-05-24' => 1904444,
        ];

        $week = 0;
        $weekD = [];
        $day = 1;
        $data = [];
        $data[0] = [];

        $dates = [];
        $values = [];
        $values2 = [];
        $values3 = [];
        $sevenDay = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $vaxxed = 0;
        $sql = "SELECT * FROM cases AS cases ORDER BY cases.date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            array_shift($sevenDay);
            array_push($sevenDay, $row['cases']??0);
            $vaxxed += $row['vaccination-2nd'];
            $sevenDayCurrent = array_sum( array_slice( $sevenDay, 7, 7));
            $sevenDayPrevious = array_sum( array_slice( $sevenDay, 0, 7));

            if (isset($ewza[$row['date']])) {
                $ewz = $ewza[$row['date']];
            }

            $values[] = round(100000 / $ewz * $sevenDayCurrent, 2);
            $values2[] = round(100000 / ($ewz-$vaxxed) * $sevenDayCurrent, 2);
            $values3[] = round(  ($sevenDayPrevious==0) ? 0 : ($sevenDayCurrent / $sevenDayPrevious), 2);
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
        $this->assign('values3', $values3);

        $this->assign('weekD', $weekD);
        $this->assign('ewz', $ewz);
        $this->assign('ewza', $ewza);
    }

    public function csv() {
        $sql = "SELECT date, cases as neuinfektionen FROM cases ORDER BY date asc";
        $this->_csv($sql);
    }

}