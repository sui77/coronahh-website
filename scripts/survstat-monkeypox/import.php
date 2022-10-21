<?php


$config = include(dirname(__FILE__) . '/../../config/config.php');
$importer = new Importer($config);



$importer->scrape();
$importer->import();
$importer->cleanup();

class Importer {

    protected $pdo;

    public function __construct($config) {
        $this->pdo = new PDO('mysql:host=' . $config['mysql']['host'] . ';dbname=' . $config['mysql']['database'], $config['mysql']['user'], $config['mysql']['password']);
    }

    public function scrape() {

        $drop1 = [
            'add3' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$ImageButtonAddFilterRow.x' => '21',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$ImageButtonAddFilterRow.y' => '21',
            ],
            'add4' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl04$ImageButtonAddFilterRow.x' => '21',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl04$ImageButtonAddFilterRow.y' => '21',
            ],


            'other' => [
                '__SCROLLPOSITIONX' => 0,
                '__SCROLLPOSITIONY' => 400,
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RadioButtonListReportingPath'  => 'Über Gesundheitsamt und Landesstelle',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl01$DropDownListFilterHierarchy'  => '[ReferenzDefinition].[ID]',
            ],
            'p1' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl01$DropDownListFilterHierarchy' => '[ReferenzDefinition].[ID]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl01$RepeaterFilterLevel$ctl01$HiddenFieldFilterLevelId' => '[ReferenzDefinition].[ID].[ID]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl01$RepeaterFilterLevel$ctl01$ListBoxFilterLevelMembers' => '[ReferenzDefinition].[ID].&[1]',
            ],
            'p2' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl02$DropDownListFilterHierarchy' => '[DeutschlandNodes].[Kreise71Web]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl02$RepeaterFilterLevel$ctl01$HiddenFieldFilterLevelId' => '[DeutschlandNodes].[Kreise71Web].[FedStateKey71]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl02$RepeaterFilterLevel$ctl01$ListBoxFilterLevelMembers' => '[DeutschlandNodes].[Kreise71Web].[FedStateKey71].&[02]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl02$RepeaterFilterLevel$ctl02$HiddenFieldFilterLevelId' => '[DeutschlandNodes].[Kreise71Web].[NutsKey71]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl02$RepeaterFilterLevel$ctl03$HiddenFieldFilterLevelId' => '[DeutschlandNodes].[Kreise71Web].[CountyKey71]',
            ],
            'p3' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$DropDownListFilterHierarchy' => '[PathogenOut].[KategorieNz]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$RepeaterFilterLevel$ctl01$HiddenFieldFilterLevelId' => '[PathogenOut].[KategorieNz].[Krankheit DE]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$RepeaterFilterLevel$ctl01$ListBoxFilterLevelMembers' => '[PathogenOut].[KategorieNz].[Krankheit DE].&[Affenpocken]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$RepeaterFilterLevel$ctl02$HiddenFieldFilterLevelId' => '[PathogenOut].[KategorieNz].[Pathogen1 Nz]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$RepeaterFilterLevel$ctl03$HiddenFieldFilterLevelId' => '[PathogenOut].[KategorieNz].[Pathogen2 Nz]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl03$RepeaterFilterLevel$ctl04$HiddenFieldFilterLevelId' => '[PathogenOut].[KategorieNz].[Pathogen3 Nz]',
            ],
            'p4' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl04$DropDownListFilterHierarchy' => '[ReportingDate].[WeekYear]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl04$RepeaterFilterLevel$ctl01$HiddenFieldFilterLevelId' => '[ReportingDate].[WeekYear].[WeekYear]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$RepeaterFilter$ctl04$RepeaterFilterLevel$ctl01$ListBoxFilterLevelMembers' => '[ReportingDate].[WeekYear].&[2022]',
            ],
            'rowcol' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$DropDownListRowHierarchy' => '[ReportingDate].[Week]',
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$DropDownListColHierarchy' => '[AlterPerson80].[AgeGroupName8]',
            ],
            'zip' => [
                'ctl00$ctl00$ContentPlaceHolderMain$ContentPlaceHolderAltGridFull$ButtonDownload' => 'ZIP herunterladen'
            ],
        ];

        $r = $this->curlPage('https://survstat.rki.de/', []);
        $r = $this->curlPage('https://survstat.rki.de/Content/Query/Create.aspx', []);

        $r = $this->curlPage('https://survstat.rki.de/Content/Query/Create.aspx', array_merge($r['params'], $drop1['rowcol'], $drop1['other'], $drop1['p1'], $drop1['p2'], $drop1['add3']));
        $r = $this->curlPage('https://survstat.rki.de/Content/Query/Create.aspx', array_merge($r['params'], $drop1['rowcol'], $drop1['other'], $drop1['p1'], $drop1['p2'], $drop1['p3'], $drop1['add4']));

        $r = $this->curlPage('https://survstat.rki.de/Content/Query/Create.aspx', array_merge($r['params'], $drop1['other'], $drop1['rowcol'], $drop1['p1'], $drop1['p2'], $drop1['p3'], $drop1['p4']));
        $r = $this->curlPage('https://survstat.rki.de/Content/Query/Create.aspx', array_merge($r['params'], $drop1['other'], $drop1['rowcol'], $drop1['p1'], $drop1['p2'], $drop1['p3'], $drop1['p4']));
        $r = $this->curlPage('https://survstat.rki.de/Content/Query/Create.aspx', array_merge($r['params'], $drop1['other'], $drop1['rowcol'], $drop1['p1'], $drop1['p2'], $drop1['p3'], $drop1['p4'], $drop1['zip']));

        exec('rm -rf /tmp/survstat2');
        mkdir('/tmp/survstat2');
        $fp = fopen('/tmp/survstat2/Data.zip', 'w');
        fputs($fp, $r['page']);
        echo "Unzippping\n";
        exec(' cd /tmp/survstat2; unzip Data.zip');
        echo "Unzipped\n";
        exec('iconv -f UCS-2 -t UTF-8 -c /tmp/survstat2/Data.csv > /tmp/survstat2/Datautf.csv');
    }

    public function cleanup() {
        //exec('rm -rf /tmp/survstat');
    }

    public function import() {


        $this->pdo->prepare('INSERT IGNORE INTO alter_rki_ap (`year`, `week`) VALUES (?, ?)')->execute([date('Y'), date('W')]);
        echo "\n";


        $fp = fopen('/tmp/survstat2/Datautf.csv', 'r');
        $cols = fgetcsv($fp, 20000, "\t", '"');
        $cols = fgetcsv($fp, 20000, "\t", '"');

        $xcols = [];
        for ($i=0; $i<=80; $i++) {
            $xcols[$i] = false;
        }
        foreach ($cols as $n => $v) {
            $v = explode('..', $v)[1];
            if (isset($xcols[$v])) {
                $xcols[$v] = $n;
            }
        }


        while (!feof($fp)) {
            $lraw = fgetcsv($fp, 20000, "\t", '"');

            foreach ($lraw as &$v) {
                $v = (trim($v) == '') ? 0 : $v;
                $v = 1 * (str_replace('"', '', $v));
            }

//print_r($lraw); exit();
            if ($lraw[0] == '') {
                continue;
            }
            $data = [2022, $lraw[0]];
            foreach($xcols as $xcolN => $xcol) {
                //echo $xcolN . ' ' . $xcol . "\n";
                if ($xcol === false) {
                    $data[] = '0';
                } else {
                    echo $xcolN; var_dump($xcol);
                    $data[] = $lraw[$xcol];
                }
            }
            $data[]= 0;



            $this->pdo->prepare("REPLACE INTO `alter_rki_ap` (`year`, `week`, `a0`, `a1`, `a2`, `a3`, `a4`, `a5`, `a6`, `a7`, `a8`, `a9`, `a10`, `a11`, `a12`, `a13`, `a14`, `a15`, `a16`, `a17`, `a18`, `a19`, `a20`, `a21`, `a22`, `a23`, `a24`, `a25`, `a26`, `a27`, `a28`, `a29`, `a30`, `a31`, `a32`, `a33`, `a34`, `a35`, `a36`, `a37`, `a38`, `a39`, `a40`, `a41`, `a42`, `a43`, `a44`, `a45`, `a46`, `a47`, `a48`, `a49`, `a50`, `a51`, `a52`, `a53`, `a54`, `a55`, `a56`, `a57`, `a58`, `a59`, `a60`, `a61`, `a62`, `a63`, `a64`, `a65`, `a66`, `a67`, `a68`, `a69`, `a70`, `a71`, `a72`, `a73`, `a74`, `a75`, `a76`, `a77`, `a78`, `a79`, `a80`, `unknown`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                ->execute($data);

        }

        $this->pdo->prepare('INSERT INTO scraping_updates (url, date) VALUES ("survstat_ap", now()) ON DUPLICATE KEY update date=now()')->execute([]);
    }

    private function parseParams($r) {
        preg_match_all('/type="hidden" name="(.*?)".*?value="(.*?)"/si', $r, $hidden);
        $params = [];
        foreach ($hidden[1] as $k => $v) {
            $params[$v] = $hidden[2][$k];
        }
        return $params;
    }

    private function curlPage($url, $postParameters) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParameters);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        $pageResponse = curl_exec($ch);
        curl_close($ch);
        $params = $this->parseParams($pageResponse);
        return ['params' => $params, 'page' => $pageResponse];
    }

}
