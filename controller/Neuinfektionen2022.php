<?php

class Neuinfektionen2022 extends AbstractController {

    protected $_template = 'neuinfektionen2022';

    public function action() {
        $ewz = 1899160;
        $ewza = [
            '2020-02-29' => 1899160,
            '2021-05-24' => 1904444,
        ];


        $sql = "SELECT date, cases FROM cases WHERE cases.date >= '2020-03-04' ORDER BY cases.date asc";
        $wcount = 0;
        $data = [];
        $tw = ['count' => 0, 'ewz' => $ewz];
        $dates = [];
        $dataTable = [
            0 => ['label' => 'Fälle'],
            1 => ['label' => 'Inzidenz']
        ];

        foreach ($this->_pdo->query($sql) as $row) {

            $tw['count'] += $row['cases'];

            if ($wcount == 0) {
                $tw['start'] = $row['date'];
            } else if ($wcount == '6') {
                $wcount = -1;
                $tw['end'] = $row['date'];
                $data[] = $tw;

                $dates[] = $tw['start'] . ' - ' . $tw['end'];
                $dataComp[] = number_format($tw['count'] / ($tw['ewz']/100000), 2, '.', '');
                $dataTable[0]['values'][] = $tw['count'];
                $dataTable[1]['values'][] = number_format($tw['count'] / ($tw['ewz']/100000), 2, '.', '');
                $tw = ['count' => 0, 'ewz' => $ewz];
            } else {

            }

            $wcount++;


            if (isset($ewza[$row['date']])) {
                $ewz = $ewza[$row['date']];
            }

        }


        $sql = "SELECT date, cases FROM cases_weekly ORDER BY date asc";
        foreach ($this->_pdo->query($sql) as $row) {
            $dates[] = $row['date'];
            $dataComp[] = number_format($row['cases'] / ($tw['ewz'] / 100000), 2, '.', '');
            $dataTable[0]['values'][] = $row['cases'];
            $dataTable[1]['values'][] = number_format($row['cases'] / ($tw['ewz'] / 100000), 2, '.', '');
        }


        $this->assign('data', $dataComp);
        $this->assign('dataTable', $dataTable);
        $this->assign('dates', $dates);

        $this->assign('ewz', $ewz);
        $this->assign('ewza', $ewza);
    }

    public function csv() {
        $sql = "SELECT date, cases as neuinfektionen FROM cases ORDER BY date asc";
        $this->_csv($sql);
    }

}