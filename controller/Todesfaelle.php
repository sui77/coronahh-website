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

        $sql = "SELECT * FROM cases WHERE deaths IS NOT NULL AND date > '2020-05-24' ORDER BY date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            array_shift($sevenDay);
            array_push($sevenDay, $row['deaths']);
            $values[] = $row['deaths'];
        }

        $sql = "SELECT * FROM cases WHERE deaths IS NOT NULL ORDER BY date desc";
        foreach ($this->_pdo->query($sql) as $row) {

            $data[$week][7 - $day] = $row['deaths'];
            $day++;
            if ($day > 7) {
                $weekD[$week] = $row['date'];
                $week++;
                $day = 1;
            }
        }

        $sql = "SELECT concat(month(date), '/', year(date)) as monat,  sum(deaths) as val FROM cases group by monat ";
        //$byMonthValues[0]['label'] = 'monat';
        $byMonthValues[0]['label'] = 'anzahl';
        $byMonthCols = [];
        foreach ($this->_pdo->query($sql) as $row) {
            //$byMonthValues[0]['values'] [] = $row['monat'];
            $byMonthValues[0]['values'] [] = $row['val'];
            $byMonthCols[] = $row['monat'];
        }

        $this->assign('byMonthValues', $byMonthValues);
        $this->assign('byMonthCols', $byMonthCols);



            $this->assign('showNumWeeks', $showNumWeeks);
        $this->assign('data', $data);
        $this->assign('dates', $dates);
        $this->assign('values', $values);
        $this->assign('weekD', $weekD);
        $this->assign('ewz', $ewz);
    }

    public function csv() {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=coronahh-' . $this->_template . '-' . date('Y-m-d') . '.csv');
        $sql = "SELECT date, deaths as todesfaelle FROM cases ORDER BY date asc";
        $first = true;
        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            if ($first == true) {
                echo implode(';', array_keys($row)) . "\n";
                $first = false;
            }
            echo implode(';', $row)."\n";
        }
    }
}