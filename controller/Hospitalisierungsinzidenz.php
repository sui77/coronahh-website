<?php

class Hospitalisierungsinzidenz extends AbstractController {

    protected $_template = 'hospitalisierungsinzidenz';

    public function action() {

        $map = [
            'n00-04' => '0-4',
            'n05-14' =>'5-14',
            'n15-34' =>'15-34',
            'n35-59' => '35-59',
            'n60-79' => '60-79',
            'n80p' => 'über 80',
            'n' => 'alle',
        ];

        $sql = "SELECT * FROM rki_hospitalisierungsinzidenz ORDER BY date";

        foreach ($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
            $dates[] = $row['date'];
            $next = 0;
            foreach ($row as $k => $v) {
                if ($k != 'date') {
                    $values[$next]['label'] = $map[$k];
                    $values[$next]['hidden'] = ($k == 'n' ? 'false' : 'true');
                    $values[$next]['values'][] = ($v/100);
                    $next++;
                }
            }

        }



        $this->assign('dates', $dates);
        $this->assign('values', $values);

    }

    public function csv() {
        $sql = "SELECT * FROM rki_hospitalisierungsinzidenz ORDER BY date";
        $this->_csv($sql);
    }
}