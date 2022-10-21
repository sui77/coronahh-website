<?php

$config = include ( dirname(__FILE__) . '/../../config/config.php');
$importer = new Importer($config);
$importer->import();

class Importer {

    protected $pdo;

    public function __construct($config) {
        $this->pdo = new PDO('mysql:host=' . $config['mysql']['host'] . ';dbname=' . $config['mysql']['database'], $config['mysql']['user'], $config['mysql']['password']);
    }


    public function import() {

        $data = $this->pdo->query('SELECT sha1 AS last FROM scraping_updates WHERE url="divi"')->fetch();
        $last = $data['last']*1;

        $current = $last+1;
        do {
            echo "Trying $current...\n";
            $data = fopen('https://datawrapper.dwcdn.net/dRfTF/' . $current . '/dataset.csv', 'r');
            if (!$data) {
                $current--;
            } else {
                $current++;
            }
        } while ($data);

        if ($last == $current) {
            echo "No updates\n";
            exit();
        }
        $data = $this->pdo->query('UPDATE scraping_updates SET sha1=" + ($current*1) + "WHERE url="divi"');


        $data = fopen('https://datawrapper.dwcdn.net/dRfTF/' . $current . '/dataset.csv', 'r');

        //$data = fopen('data:application/octet-stream;charset=utf-8,%EF%BB%BFdate%2CBelegte%20Betten%2CFreie%20Betten%2CNotfallreserve%0A2020-03-20T12%3A15%3A00%2B01%3A00%2C576%2C2866%2C0%0A2020-03-21T12%3A15%3A00%2B01%3A00%2C2828%2C3554%2C0%0A2020-03-22T12%3A15%3A00%2B01%3A00%2C3255%2C3712%2C0%0A2020-03-23T12%3A15%3A00%2B01%3A00%2C3778%2C4050%2C0%0A2020-03-24T12%3A15%3A00%2B01%3A00%2C4856%2C4712%2C0%0A2020-03-25T12%3A15%3A00%2B01%3A00%2C5471%2C5081%2C0%0A2020-03-26T12%3A15%3A00%2B01%3A00%2C5960%2C5159%2C0%0A2020-03-27T12%3A15%3A00%2B01%3A00%2C6274%2C5389%2C0%0A2020-03-28T12%3A15%3A00%2B01%3A00%2C6738%2C5411%2C0%0A2020-03-29T12%3A15%3A00%2B02%3A00%2C6724%2C5496%2C0%0A2020-03-30T12%3A15%3A00%2B02%3A00%2C7069%2C5626%2C0%0A2020-03-31T12%3A15%3A00%2B02%3A00%2C8392%2C6227%2C0%0A2020-04-01T12%3A15%3A00%2B02%3A00%2C9345%2C7094%2C0%0A2020-04-02T12%3A15%3A00%2B02%3A00%2C10662%2C8017%2C0%0A2020-04-03T12%3A15%3A00%2B02%3A00%2C11708%2C8429%2C0%0A2020-04-04T12%3A15%3A00%2B02%3A00%2C11904%2C8689%2C0%0A2020-04-05T12%3A15%3A00%2B02%3A00%2C11931%2C8805%2C0%0A2020-04-06T12%3A15%3A00%2B02%3A00%2C11985%2C8809%2C0%0A2020-04-07T12%3A15%3A00%2B02%3A00%2C11927%2C8806%2C0%0A2020-04-08T12%3A15%3A00%2B02%3A00%2C12515%2C8967%2C0%0A2020-04-09T12%3A15%3A00%2B02%3A00%2C13118%2C9007%2C0%0A2020-04-10T12%3A15%3A00%2B02%3A00%2C12881%2C8990%2C0%0A2020-04-11T12%3A15%3A00%2B02%3A00%2C12462%2C9023%2C0%0A2020-04-12T12%3A15%3A00%2B02%3A00%2C12541%2C9168%2C0%0A2020-04-13T12%3A15%3A00%2B02%3A00%2C12831%2C9216%2C0%0A2020-04-14T12%3A15%3A00%2B02%3A00%2C13123%2C9443%2C0%0A2020-04-15T12%3A15%3A00%2B02%3A00%2C14119%2C9919%2C0%0A2020-04-16T12%3A15%3A00%2B02%3A00%2C15673%2C11208%2C0%0A2020-04-17T12%3A15%3A00%2B02%3A00%2C16835%2C11455%2C0%0A2020-04-18T12%3A15%3A00%2B02%3A00%2C17311%2C11605%2C0%0A2020-04-19T12%3A15%3A00%2B02%3A00%2C16930%2C12031%2C0%0A2020-04-20T12%3A15%3A00%2B02%3A00%2C17244%2C11992%2C0%0A2020-04-21T12%3A15%3A00%2B02%3A00%2C18163%2C11870%2C0%0A2020-04-22T12%3A15%3A00%2B02%3A00%2C18540%2C11900%2C0%0A2020-04-23T12%3A15%3A00%2B02%3A00%2C18701%2C11942%2C0%0A2020-04-24T12%3A15%3A00%2B02%3A00%2C18879%2C12009%2C0%0A2020-04-25T12%3A15%3A00%2B02%3A00%2C18738%2C12017%2C0%0A2020-04-26T12%3A15%3A00%2B02%3A00%2C18254%2C12420%2C0%0A2020-04-27T12%3A15%3A00%2B02%3A00%2C18436%2C12259%2C0%0A2020-04-28T12%3A15%3A00%2B02%3A00%2C18948%2C11931%2C0%0A2020-04-29T12%3A15%3A00%2B02%3A00%2C19179%2C11886%2C0%0A2020-04-30T12%3A15%3A00%2B02%3A00%2C19130%2C11924%2C0%0A2020-05-01T12%3A15%3A00%2B02%3A00%2C19193%2C11999%2C0%0A2020-05-02T12%3A15%3A00%2B02%3A00%2C18554%2C12595%2C0%0A2020-05-03T12%3A15%3A00%2B02%3A00%2C18681%2C12458%2C0%0A2020-05-04T12%3A15%3A00%2B02%3A00%2C18594%2C12396%2C0%0A2020-05-05T12%3A15%3A00%2B02%3A00%2C19170%2C11812%2C0%0A2020-05-06T12%3A15%3A00%2B02%3A00%2C19535%2C11542%2C0%0A2020-05-07T12%3A15%3A00%2B02%3A00%2C19511%2C11506%2C0%0A2020-05-08T12%3A15%3A00%2B02%3A00%2C19459%2C11368%2C0%0A2020-05-09T12%3A15%3A00%2B02%3A00%2C19262%2C11578%2C0%0A2020-05-10T12%3A15%3A00%2B02%3A00%2C18709%2C11994%2C0%0A2020-05-11T12%3A15%3A00%2B02%3A00%2C18866%2C11839%2C0%0A2020-05-12T12%3A15%3A00%2B02%3A00%2C19766%2C11609%2C0%0A2020-05-13T12%3A15%3A00%2B02%3A00%2C19705%2C11280%2C0%0A2020-05-14T12%3A15%3A00%2B02%3A00%2C19637%2C11438%2C0%0A2020-05-15T12%3A15%3A00%2B02%3A00%2C19965%2C11118%2C0%0A2020-05-16T12%3A15%3A00%2B02%3A00%2C19603%2C11314%2C0%0A2020-05-17T12%3A15%3A00%2B02%3A00%2C19086%2C11885%2C0%0A2020-05-18T12%3A15%3A00%2B02%3A00%2C19044%2C11841%2C0%0A2020-05-19T12%3A15%3A00%2B02%3A00%2C19621%2C11100%2C0%0A2020-05-20T12%3A15%3A00%2B02%3A00%2C19852%2C11043%2C0%0A2020-05-21T12%3A15%3A00%2B02%3A00%2C19825%2C11064%2C0%0A2020-05-22T12%3A15%3A00%2B02%3A00%2C19266%2C11607%2C0%0A2020-05-23T12%3A15%3A00%2B02%3A00%2C19453%2C11596%2C0%0A2020-05-24T12%3A15%3A00%2B02%3A00%2C18969%2C11945%2C0%0A2020-05-25T12%3A15%3A00%2B02%3A00%2C19024%2C11980%2C0%0A2020-05-26T12%3A15%3A00%2B02%3A00%2C19559%2C11325%2C0%0A2020-05-27T12%3A15%3A00%2B02%3A00%2C19822%2C10984%2C0%0A2020-05-28T12%3A15%3A00%2B02%3A00%2C19919%2C10846%2C0%0A2020-05-29T12%3A15%3A00%2B02%3A00%2C19847%2C10847%2C0%0A2020-05-30T12%3A15%3A00%2B02%3A00%2C19752%2C10961%2C0%0A2020-05-31T12%3A15%3A00%2B02%3A00%2C19087%2C11557%2C0%0A2020-06-01T12%3A15%3A00%2B02%3A00%2C19022%2C11628%2C0%0A2020-06-02T12%3A15%3A00%2B02%3A00%2C19091%2C11528%2C0%0A2020-06-03T12%3A15%3A00%2B02%3A00%2C19789%2C10895%2C0%0A2020-06-04T12%3A15%3A00%2B02%3A00%2C20109%2C10550%2C0%0A2020-06-05T12%3A15%3A00%2B02%3A00%2C20188%2C10455%2C0%0A2020-06-06T12%3A15%3A00%2B02%3A00%2C19913%2C10701%2C0%0A2020-06-07T12%3A15%3A00%2B02%3A00%2C19325%2C11254%2C0%0A2020-06-08T12%3A15%3A00%2B02%3A00%2C19179%2C11397%2C0%0A2020-06-09T12%3A15%3A00%2B02%3A00%2C20019%2C10644%2C0%0A2020-06-10T12%3A15%3A00%2B02%3A00%2C20167%2C10529%2C0%0A2020-06-11T12%3A15%3A00%2B02%3A00%2C20093%2C10572%2C0%0A2020-06-12T12%3A15%3A00%2B02%3A00%2C19624%2C10996%2C0%0A2020-06-13T12%3A15%3A00%2B02%3A00%2C19567%2C10971%2C0%0A2020-06-14T12%3A15%3A00%2B02%3A00%2C19182%2C11328%2C0%0A2020-06-15T12%3A15%3A00%2B02%3A00%2C19268%2C11348%2C0%0A2020-06-16T12%3A15%3A00%2B02%3A00%2C19987%2C10669%2C0%0A2020-06-17T12%3A15%3A00%2B02%3A00%2C20269%2C10404%2C0%0A2020-06-18T12%3A15%3A00%2B02%3A00%2C20397%2C10377%2C0%0A2020-06-19T12%3A15%3A00%2B02%3A00%2C20490%2C10274%2C0%0A2020-06-20T12%3A15%3A00%2B02%3A00%2C20176%2C10523%2C0%0A2020-06-21T12%3A15%3A00%2B02%3A00%2C19608%2C11080%2C0%0A2020-06-22T12%3A15%3A00%2B02%3A00%2C19626%2C11246%2C0%0A2020-06-23T12%3A15%3A00%2B02%3A00%2C20248%2C10614%2C0%0A2020-06-24T12%3A15%3A00%2B02%3A00%2C20330%2C10594%2C0%0A2020-06-25T12%3A15%3A00%2B02%3A00%2C20326%2C10550%2C0%0A2020-06-26T12%3A15%3A00%2B02%3A00%2C20472%2C10339%2C0%0A2020-06-27T12%3A15%3A00%2B02%3A00%2C20264%2C10541%2C0%0A2020-06-28T12%3A15%3A00%2B02%3A00%2C19761%2C11019%2C0%0A2020-06-29T12%3A15%3A00%2B02%3A00%2C19702%2C11156%2C0%0A2020-06-30T12%3A15%3A00%2B02%3A00%2C20275%2C10633%2C0%0A2020-07-01T12%3A15%3A00%2B02%3A00%2C20659%2C10330%2C0%0A2020-07-02T12%3A15%3A00%2B02%3A00%2C20756%2C10222%2C0%0A2020-07-03T12%3A15%3A00%2B02%3A00%2C20540%2C10149%2C0%0A2020-07-04T12%3A15%3A00%2B02%3A00%2C20176%2C10627%2C0%0A2020-07-05T12%3A15%3A00%2B02%3A00%2C19621%2C11121%2C0%0A2020-07-06T12%3A15%3A00%2B02%3A00%2C19666%2C11183%2C0%0A2020-07-07T12%3A15%3A00%2B02%3A00%2C20214%2C10721%2C0%0A2020-07-08T12%3A15%3A00%2B02%3A00%2C20433%2C10499%2C0%0A2020-07-09T12%3A15%3A00%2B02%3A00%2C20498%2C10391%2C0%0A2020-07-10T12%3A15%3A00%2B02%3A00%2C20591%2C10279%2C0%0A2020-07-11T12%3A15%3A00%2B02%3A00%2C20324%2C10503%2C0%0A2020-07-12T12%3A15%3A00%2B02%3A00%2C19717%2C10952%2C0%0A2020-07-13T12%3A15%3A00%2B02%3A00%2C19586%2C11159%2C0%0A2020-07-14T12%3A15%3A00%2B02%3A00%2C20305%2C10505%2C0%0A2020-07-15T12%3A15%3A00%2B02%3A00%2C20694%2C10142%2C0%0A2020-07-16T12%3A15%3A00%2B02%3A00%2C20734%2C10134%2C0%0A2020-07-17T12%3A15%3A00%2B02%3A00%2C20792%2C10136%2C0%0A2020-07-18T12%3A15%3A00%2B02%3A00%2C20403%2C10399%2C0%0A2020-07-19T12%3A15%3A00%2B02%3A00%2C19924%2C10818%2C0%0A2020-07-20T12%3A15%3A00%2B02%3A00%2C20065%2C10856%2C0%0A2020-07-21T12%3A15%3A00%2B02%3A00%2C20399%2C10468%2C0%0A2020-07-22T12%3A15%3A00%2B02%3A00%2C20541%2C10235%2C0%0A2020-07-23T12%3A15%3A00%2B02%3A00%2C20454%2C10249%2C0%0A2020-07-24T12%3A15%3A00%2B02%3A00%2C20502%2C10113%2C0%0A2020-07-25T12%3A15%3A00%2B02%3A00%2C20210%2C10279%2C0%0A2020-07-26T12%3A15%3A00%2B02%3A00%2C19721%2C10741%2C0%0A2020-07-27T12%3A15%3A00%2B02%3A00%2C19608%2C10854%2C0%0A2020-07-28T12%3A15%3A00%2B02%3A00%2C20179%2C10330%2C0%0A2020-07-29T12%3A15%3A00%2B02%3A00%2C20303%2C10123%2C0%0A2020-07-30T12%3A15%3A00%2B02%3A00%2C20081%2C10233%2C0%0A2020-07-31T12%3A15%3A00%2B02%3A00%2C20120%2C10150%2C0%0A2020-08-01T12%3A15%3A00%2B02%3A00%2C19824%2C10317%2C0%0A2020-08-02T12%3A15%3A00%2B02%3A00%2C19344%2C10758%2C0%0A2020-08-03T12%3A15%3A00%2B02%3A00%2C19318%2C10858%2C0%0A2020-08-04T12%3A15%3A00%2B02%3A00%2C19800%2C8636%2C11762%0A2020-08-05T12%3A15%3A00%2B02%3A00%2C20057%2C8236%2C12206%0A2020-08-06T12%3A15%3A00%2B02%3A00%2C19982%2C8195%2C12182%0A2020-08-07T12%3A15%3A00%2B02%3A00%2C20015%2C8138%2C12129%0A2020-08-08T12%3A15%3A00%2B02%3A00%2C19760%2C8271%2C12141%0A2020-08-09T12%3A15%3A00%2B02%3A00%2C19324%2C8684%2C12137%0A2020-08-10T12%3A15%3A00%2B02%3A00%2C19354%2C8610%2C12124%0A2020-08-11T12%3A15%3A00%2B02%3A00%2C19988%2C7959%2C12150%0A2020-08-12T12%3A15%3A00%2B02%3A00%2C20110%2C7849%2C12087%0A2020-08-13T12%3A15%3A00%2B02%3A00%2C20186%2C7662%2C12135%0A2020-08-14T12%3A15%3A00%2B02%3A00%2C20269%2C7619%2C12027%0A2020-08-15T12%3A15%3A00%2B02%3A00%2C19932%2C7844%2C12069%0A2020-08-16T12%3A15%3A00%2B02%3A00%2C19436%2C8319%2C12051%0A2020-08-17T12%3A15%3A00%2B02%3A00%2C19431%2C8408%2C12014%0A2020-08-18T12%3A15%3A00%2B02%3A00%2C19955%2C7968%2C12003%0A2020-08-19T12%3A15%3A00%2B02%3A00%2C20088%2C7850%2C11971%0A2020-08-20T12%3A15%3A00%2B02%3A00%2C20226%2C7793%2C11985%0A2020-08-21T12%3A15%3A00%2B02%3A00%2C20245%2C7762%2C12047%0A2020-08-22T12%3A15%3A00%2B02%3A00%2C19795%2C8060%2C12005%0A2020-08-23T12%3A15%3A00%2B02%3A00%2C19330%2C8481%2C12017%0A2020-08-24T12%3A15%3A00%2B02%3A00%2C19192%2C8714%2C12025%0A2020-08-25T12%3A15%3A00%2B02%3A00%2C19821%2C8184%2C11915%0A2020-08-26T12%3A15%3A00%2B02%3A00%2C20020%2C8005%2C11905%0A2020-08-27T12%3A15%3A00%2B02%3A00%2C19903%2C8095%2C11889%0A2020-08-28T12%3A15%3A00%2B02%3A00%2C19915%2C8005%2C11884%0A2020-08-29T12%3A15%3A00%2B02%3A00%2C19830%2C7999%2C11893%0A2020-08-30T12%3A15%3A00%2B02%3A00%2C19325%2C8449%2C11905%0A2020-08-31T12%3A15%3A00%2B02%3A00%2C19284%2C8572%2C11891%0A2020-09-01T12%3A15%3A00%2B02%3A00%2C19880%2C8072%2C11845%0A2020-09-02T12%3A15%3A00%2B02%3A00%2C20014%2C7968%2C11868%0A2020-09-03T12%3A15%3A00%2B02%3A00%2C20026%2C7947%2C11854%0A2020-09-04T12%3A15%3A00%2B02%3A00%2C19972%2C7976%2C11845%0A2020-09-05T12%3A15%3A00%2B02%3A00%2C19814%2C7990%2C11980%0A2020-09-06T12%3A15%3A00%2B02%3A00%2C19252%2C8521%2C12000%0A2020-09-07T12%3A15%3A00%2B02%3A00%2C19336%2C8510%2C11827%0A2020-09-08T12%3A15%3A00%2B02%3A00%2C20001%2C7890%2C11836%0A2020-09-09T12%3A15%3A00%2B02%3A00%2C20108%2C7787%2C11823%0A2020-09-10T12%3A15%3A00%2B02%3A00%2C20259%2C7709%2C11828%0A2020-09-11T12%3A15%3A00%2B02%3A00%2C20340%2C7603%2C11830%0A2020-09-12T12%3A15%3A00%2B02%3A00%2C19980%2C7830%2C11878%0A2020-09-13T12%3A15%3A00%2B02%3A00%2C19497%2C8280%2C11830%0A2020-09-14T12%3A15%3A00%2B02%3A00%2C19373%2C8494%2C11797%0A2020-09-15T12%3A15%3A00%2B02%3A00%2C20085%2C7812%2C11825%0A2020-09-16T12%3A15%3A00%2B02%3A00%2C20363%2C7627%2C11820%0A2020-09-17T12%3A15%3A00%2B02%3A00%2C20491%2C7525%2C11769%0A2020-09-18T12%3A15%3A00%2B02%3A00%2C20302%2C7633%2C11791%0A2020-09-19T12%3A15%3A00%2B02%3A00%2C19860%2C7956%2C11811%0A2020-09-20T12%3A15%3A00%2B02%3A00%2C19430%2C8348%2C11832%0A2020-09-21T12%3A15%3A00%2B02%3A00%2C19384%2C8478%2C11805%0A2020-09-22T12%3A15%3A00%2B02%3A00%2C19981%2C7902%2C11744%0A2020-09-23T12%3A15%3A00%2B02%3A00%2C20116%2C7742%2C11763%0A2020-09-24T12%3A15%3A00%2B02%3A00%2C20110%2C7739%2C11743%0A2020-09-25T12%3A15%3A00%2B02%3A00%2C20183%2C7659%2C11780%0A2020-09-26T12%3A15%3A00%2B02%3A00%2C19946%2C7770%2C11751%0A2020-09-27T12%3A15%3A00%2B02%3A00%2C19337%2C8339%2C11738%0A2020-09-28T12%3A15%3A00%2B02%3A00%2C19326%2C8531%2C11661%0A2020-09-29T12%3A15%3A00%2B02%3A00%2C20041%2C7922%2C11618%0A2020-09-30T12%3A15%3A00%2B02%3A00%2C20202%2C7720%2C11712%0A2020-10-01T12%3A15%3A00%2B02%3A00%2C20057%2C7705%2C11689%0A2020-10-02T12%3A15%3A00%2B02%3A00%2C20019%2C7680%2C11678%0A2020-10-03T12%3A15%3A00%2B02%3A00%2C19743%2C7808%2C11733%0A2020-10-04T12%3A15%3A00%2B02%3A00%2C19235%2C8252%2C11728%0A2020-10-05T12%3A15%3A00%2B02%3A00%2C19141%2C8424%2C11694%0A2020-10-06T12%3A15%3A00%2B02%3A00%2C19732%2C7858%2C11667%0A2020-10-07T12%3A15%3A00%2B02%3A00%2C20060%2C7485%2C11685%0A2020-10-08T12%3A15%3A00%2B02%3A00%2C20028%2C7485%2C11744%0A2020-10-09T12%3A15%3A00%2B02%3A00%2C19973%2C7541%2C11747%0A2020-10-10T12%3A15%3A00%2B02%3A00%2C19679%2C7819%2C11722%0A2020-10-11T12%3A15%3A00%2B02%3A00%2C19134%2C8301%2C11732%0A2020-10-12T12%3A15%3A00%2B02%3A00%2C19164%2C8295%2C11752%0A2020-10-13T12%3A15%3A00%2B02%3A00%2C19577%2C7922%2C11746%0A2020-10-14T12%3A15%3A00%2B02%3A00%2C19736%2C7695%2C11848%0A2020-10-15T12%3A15%3A00%2B02%3A00%2C19758%2C7683%2C11909%0A2020-10-16T12%3A15%3A00%2B02%3A00%2C19800%2C7629%2C11898%0A2020-10-17T12%3A15%3A00%2B02%3A00%2C19518%2C7771%2C11882%0A2020-10-18T12%3A15%3A00%2B02%3A00%2C18969%2C8291%2C11875%0A2020-10-19T12%3A15%3A00%2B02%3A00%2C19030%2C8369%2C11925%0A2020-10-20T12%3A15%3A00%2B02%3A00%2C19566%2C7859%2C11888%0A2020-10-21T12%3A15%3A00%2B02%3A00%2C19698%2C7472%2C12069%0A2020-10-22T12%3A15%3A00%2B02%3A00%2C19746%2C7188%2C12213%0A2020-10-23T12%3A15%3A00%2B02%3A00%2C19999%2C6768%2C12230%0A2020-10-24T12%3A15%3A00%2B02%3A00%2C19700%2C6903%2C12275%0A2020-10-25T12%3A15%3A00%2B01%3A00%2C19196%2C7354%2C12241%0A2020-10-26T12%3A15%3A00%2B01%3A00%2C19313%2C7370%2C12217%0A2020-10-27T12%3A15%3A00%2B01%3A00%2C19969%2C6686%2C12269%0A2020-10-28T12%3A15%3A00%2B01%3A00%2C20056%2C6533%2C12271%0A2020-10-29T12%3A15%3A00%2B01%3A00%2C20054%2C6542%2C12278%0A2020-10-30T12%3A15%3A00%2B01%3A00%2C19922%2C6567%2C12268%0A2020-10-31T12%3A15%3A00%2B01%3A00%2C19559%2C6765%2C12317%0A2020-11-01T12%3A15%3A00%2B01%3A00%2C19206%2C7020%2C12325%0A2020-11-02T12%3A15%3A00%2B01%3A00%2C19192%2C6910%2C12401%0A2020-11-03T12%3A15%3A00%2B01%3A00%2C19762%2C6268%2C12283%0A2020-11-04T12%3A15%3A00%2B01%3A00%2C19952%2C6097%2C12285%0A2020-11-05T12%3A15%3A00%2B01%3A00%2C19989%2C5921%2C12275%0A2020-11-06T12%3A15%3A00%2B01%3A00%2C19785%2C6045%2C12149%0A2020-11-07T12%3A15%3A00%2B01%3A00%2C19548%2C6060%2C12117%0A2020-11-08T12%3A15%3A00%2B01%3A00%2C19046%2C6481%2C12087%0A2020-11-09T12%3A15%3A00%2B01%3A00%2C19272%2C6434%2C12001%0A2020-11-10T12%3A15%3A00%2B01%3A00%2C19828%2C5829%2C12069%0A2020-11-11T12%3A15%3A00%2B01%3A00%2C20054%2C5604%2C11979%0A2020-11-12T12%3A15%3A00%2B01%3A00%2C20192%2C5472%2C11960%0A2020-11-13T12%3A15%3A00%2B01%3A00%2C20241%2C5268%2C11943%0A2020-11-14T12%3A15%3A00%2B01%3A00%2C19947%2C5402%2C11921%0A2020-11-15T12%3A15%3A00%2B01%3A00%2C19575%2C5718%2C11864%0A2020-11-16T12%3A15%3A00%2B01%3A00%2C19664%2C5796%2C11798%0A2020-11-17T12%3A15%3A00%2B01%3A00%2C20150%2C5362%2C11741%0A2020-11-18T12%3A15%3A00%2B01%3A00%2C20337%2C5122%2C11716%0A2020-11-19T12%3A15%3A00%2B01%3A00%2C20214%2C5170%2C11634%0A2020-11-20T12%3A15%3A00%2B01%3A00%2C20301%2C5031%2C11680%0A2020-11-21T12%3A15%3A00%2B01%3A00%2C20003%2C5120%2C11720%0A2020-11-22T12%3A15%3A00%2B01%3A00%2C19568%2C5532%2C11738%0A2020-11-23T12%3A15%3A00%2B01%3A00%2C19593%2C5530%2C11659%0A2020-11-24T12%3A15%3A00%2B01%3A00%2C20198%2C4921%2C11586%0A2020-11-25T12%3A15%3A00%2B01%3A00%2C20315%2C4754%2C11598%0A2020-11-26T12%3A15%3A00%2B01%3A00%2C20390%2C4603%2C11572%0A2020-11-27T12%3A15%3A00%2B01%3A00%2C20552%2C4364%2C11578%0A2020-11-28T12%3A15%3A00%2B01%3A00%2C20174%2C4573%2C11581%0A2020-11-29T12%3A15%3A00%2B01%3A00%2C19790%2C4908%2C11578%0A2020-11-30T12%3A15%3A00%2B01%3A00%2C19878%2C4943%2C11526%0A2020-12-01T12%3A15%3A00%2B01%3A00%2C20416%2C4463%2C11370%0A2020-12-02T12%3A15%3A00%2B01%3A00%2C20507%2C4320%2C11364%0A2020-12-03T12%3A15%3A00%2B01%3A00%2C20512%2C4291%2C11344%0A2020-12-04T12%3A15%3A00%2B01%3A00%2C20613%2C4110%2C11311%0A2020-12-05T12%3A15%3A00%2B01%3A00%2C20323%2C4168%2C11338%0A2020-12-06T12%3A15%3A00%2B01%3A00%2C19890%2C4571%2C11344%0A2020-12-07T12%3A15%3A00%2B01%3A00%2C20072%2C4490%2C11294%0A2020-12-08T12%3A15%3A00%2B01%3A00%2C20527%2C3989%2C11180%0A2020-12-09T12%3A15%3A00%2B01%3A00%2C20679%2C3895%2C11133%0A2020-12-10T12%3A15%3A00%2B01%3A00%2C20728%2C3798%2C11121%0A2020-12-11T12%3A15%3A00%2B01%3A00%2C20662%2C3725%2C11038%0A2020-12-12T12%3A15%3A00%2B01%3A00%2C20336%2C3948%2C11072%0A2020-12-13T12%3A15%3A00%2B01%3A00%2C19988%2C4241%2C11095%0A2020-12-14T12%3A15%3A00%2B01%3A00%2C20240%2C4108%2C11071%0A2020-12-15T12%3A15%3A00%2B01%3A00%2C20659%2C3731%2C10992%0A2020-12-16T12%3A15%3A00%2B01%3A00%2C20723%2C3589%2C10811%0A2020-12-17T12%3A15%3A00%2B01%3A00%2C20655%2C3685%2C10777%0A2020-12-18T12%3A15%3A00%2B01%3A00%2C20661%2C3636%2C10715%0A2020-12-19T12%3A15%3A00%2B01%3A00%2C20244%2C3830%2C10696%0A2020-12-20T12%3A15%3A00%2B01%3A00%2C19856%2C4096%2C10721%0A2020-12-21T12%3A15%3A00%2B01%3A00%2C20156%2C3912%2C10671%0A2020-12-22T12%3A15%3A00%2B01%3A00%2C20242%2C3812%2C10643%0A2020-12-23T12%3A15%3A00%2B01%3A00%2C20091%2C3889%2C10622%0A2020-12-24T12%3A15%3A00%2B01%3A00%2C19745%2C4060%2C10572%0A2020-12-25T12%3A15%3A00%2B01%3A00%2C19454%2C4285%2C10599%0A2020-12-26T12%3A15%3A00%2B01%3A00%2C19521%2C4234%2C10590%0A2020-12-27T12%3A15%3A00%2B01%3A00%2C19607%2C4151%2C10586%0A2020-12-28T12%3A15%3A00%2B01%3A00%2C19863%2C4029%2C10552%0A2020-12-29T12%3A15%3A00%2B01%3A00%2C20219%2C3745%2C10518%0A2020-12-30T12%3A15%3A00%2B01%3A00%2C20147%2C3759%2C10490%0A2020-12-31T12%3A15%3A00%2B01%3A00%2C20010%2C3791%2C10471%0A2021-01-01T12%3A15%3A00%2B01%3A00%2C19835%2C3873%2C10487%0A2021-01-02T12%3A15%3A00%2B01%3A00%2C19938%2C3818%2C10458%0A2021-01-03T12%3A15%3A00%2B01%3A00%2C20057%2C3723%2C10440%0A2021-01-04T12%3A15%3A00%2B01%3A00%2C20182%2C3756%2C10374%0A2021-01-05T12%3A15%3A00%2B01%3A00%2C20517%2C3584%2C10337%0A2021-01-06T12%3A15%3A00%2B01%3A00%2C20640%2C3507%2C10310%0A2021-01-07T12%3A15%3A00%2B01%3A00%2C20613%2C3548%2C10327%0A2021-01-08T12%3A15%3A00%2B01%3A00%2C20701%2C3449%2C10315%0A2021-01-09T12%3A15%3A00%2B01%3A00%2C20441%2C3603%2C10332%0A2021-01-10T12%3A15%3A00%2B01%3A00%2C19948%2C3997%2C10382%0A2021-01-11T12%3A15%3A00%2B01%3A00%2C20148%2C3877%2C10331%0A2021-01-12T12%3A15%3A00%2B01%3A00%2C20578%2C3630%2C10291%0A2021-01-13T12%3A15%3A00%2B01%3A00%2C20688%2C3527%2C10257%0A2021-01-14T12%3A15%3A00%2B01%3A00%2C20726%2C3459%2C10275%0A2021-01-15T12%3A15%3A00%2B01%3A00%2C20703%2C3472%2C10252%0A2021-01-16T12%3A15%3A00%2B01%3A00%2C20494%2C3593%2C10329%0A2021-01-17T12%3A15%3A00%2B01%3A00%2C20033%2C4008%2C10322%0A2021-01-18T12%3A15%3A00%2B01%3A00%2C20242%2C3918%2C10284%0A2021-01-19T12%3A15%3A00%2B01%3A00%2C20688%2C3524%2C10231%0A2021-01-20T12%3A15%3A00%2B01%3A00%2C20836%2C3425%2C10216%0A2021-01-21T12%3A15%3A00%2B01%3A00%2C20899%2C3359%2C10233%0A2021-01-22T12%3A15%3A00%2B01%3A00%2C20943%2C3290%2C10245%0A2021-01-23T12%3A15%3A00%2B01%3A00%2C20547%2C3526%2C10313%0A2021-01-24T12%3A15%3A00%2B01%3A00%2C20158%2C3857%2C10352%0A2021-01-25T12%3A15%3A00%2B01%3A00%2C20246%2C3884%2C10352%0A2021-01-26T12%3A15%3A00%2B01%3A00%2C20673%2C3536%2C10323%0A2021-01-27T12%3A15%3A00%2B01%3A00%2C20858%2C3316%2C10313%0A2021-01-28T12%3A15%3A00%2B01%3A00%2C20710%2C3450%2C10337%0A2021-01-29T12%3A15%3A00%2B01%3A00%2C20726%2C3424%2C10334%0A2021-01-30T12%3A15%3A00%2B01%3A00%2C20403%2C3635%2C10399%0A2021-01-31T12%3A15%3A00%2B01%3A00%2C20055%2C3937%2C10412%0A2021-02-01T12%3A15%3A00%2B01%3A00%2C20275%2C3826%2C10402%0A2021-02-02T12%3A15%3A00%2B01%3A00%2C20624%2C3578%2C10377%0A2021-02-03T12%3A15%3A00%2B01%3A00%2C20898%2C3382%2C10341%0A2021-02-04T12%3A15%3A00%2B01%3A00%2C20813%2C3437%2C10328%0A2021-02-05T12%3A15%3A00%2B01%3A00%2C20746%2C3453%2C10335%0A2021-02-06T12%3A15%3A00%2B01%3A00%2C20369%2C3696%2C10367%0A2021-02-07T12%3A15%3A00%2B01%3A00%2C19868%2C4126%2C10398%0A2021-02-08T12%3A15%3A00%2B01%3A00%2C20117%2C3935%2C10380%0A2021-02-09T12%3A15%3A00%2B01%3A00%2C20484%2C3682%2C10363%0A2021-02-10T12%3A15%3A00%2B01%3A00%2C20565%2C3620%2C10335%0A2021-02-11T12%3A15%3A00%2B01%3A00%2C20496%2C3669%2C10351%0A2021-02-12T12%3A15%3A00%2B01%3A00%2C20422%2C3739%2C10320%0A2021-02-13T12%3A15%3A00%2B01%3A00%2C20144%2C3838%2C10354%0A2021-02-14T12%3A15%3A00%2B01%3A00%2C19689%2C4175%2C10389%0A2021-02-15T12%3A15%3A00%2B01%3A00%2C19921%2C4056%2C10360%0A2021-02-16T12%3A15%3A00%2B01%3A00%2C20410%2C3691%2C10377%0A2021-02-17T12%3A15%3A00%2B01%3A00%2C20464%2C3635%2C10344%0A2021-02-18T12%3A15%3A00%2B01%3A00%2C20429%2C3673%2C10346%0A2021-02-19T12%3A15%3A00%2B01%3A00%2C20412%2C3602%2C10380%0A2021-02-20T12%3A15%3A00%2B01%3A00%2C20169%2C3719%2C10386%0A2021-02-21T12%3A15%3A00%2B01%3A00%2C19746%2C4053%2C10391%0A2021-02-22T12%3A15%3A00%2B01%3A00%2C19963%2C4013%2C10395%0A2021-02-23T12%3A15%3A00%2B01%3A00%2C20408%2C3679%2C10384%0A2021-02-24T12%3A15%3A00%2B01%3A00%2C20553%2C3546%2C10380%0A2021-02-25T12%3A15%3A00%2B01%3A00%2C20546%2C3567%2C10377%0A2021-02-26T12%3A15%3A00%2B01%3A00%2C20435%2C3621%2C10370%0A2021-02-27T12%3A15%3A00%2B01%3A00%2C20059%2C3839%2C10382%0A2021-02-28T12%3A15%3A00%2B01%3A00%2C19606%2C4207%2C10469%0A2021-03-01T12%3A15%3A00%2B01%3A00%2C19831%2C4155%2C10453%0A2021-03-02T12%3A15%3A00%2B01%3A00%2C20435%2C3699%2C10418%0A2021-03-03T12%3A15%3A00%2B01%3A00%2C20418%2C3732%2C10414%0A2021-03-04T12%3A15%3A00%2B01%3A00%2C20397%2C3722%2C10440%0A2021-03-05T12%3A15%3A00%2B01%3A00%2C20404%2C3678%2C10448%0A2021-03-06T12%3A15%3A00%2B01%3A00%2C20021%2C3948%2C10495%0A2021-03-07T12%3A15%3A00%2B01%3A00%2C19626%2C4276%2C10522%0A2021-03-08T12%3A15%3A00%2B01%3A00%2C19853%2C4180%2C10442%0A2021-03-09T12%3A15%3A00%2B01%3A00%2C20288%2C3888%2C10428%0A2021-03-10T12%3A15%3A00%2B01%3A00%2C20512%2C3670%2C10417%0A2021-03-11T12%3A15%3A00%2B01%3A00%2C20538%2C3639%2C10427%0A2021-03-12T12%3A15%3A00%2B01%3A00%2C20544%2C3594%2C10410%0A2021-03-13T12%3A15%3A00%2B01%3A00%2C20147%2C3823%2C10444%0A2021-03-14T12%3A15%3A00%2B01%3A00%2C19717%2C4142%2C10472%0A2021-03-15T12%3A15%3A00%2B01%3A00%2C19882%2C4119%2C10437%0A2021-03-16T12%3A15%3A00%2B01%3A00%2C20426%2C3673%2C10434%0A2021-03-17T12%3A15%3A00%2B01%3A00%2C20544%2C3568%2C10419%0A2021-03-18T12%3A15%3A00%2B01%3A00%2C20566%2C3508%2C10425%0A2021-03-19T12%3A15%3A00%2B01%3A00%2C20535%2C3469%2C10430%0A2021-03-20T12%3A15%3A00%2B01%3A00%2C20120%2C3713%2C10482%0A2021-03-21T12%3A15%3A00%2B01%3A00%2C19665%2C4114%2C10500%0A2021-03-22T12%3A15%3A00%2B01%3A00%2C20007%2C3958%2C10472%0A2021-03-23T12%3A15%3A00%2B01%3A00%2C20548%2C3501%2C10431%0A2021-03-24T12%3A15%3A00%2B01%3A00%2C20701%2C3397%2C10433%0A2021-03-25T12%3A15%3A00%2B01%3A00%2C20698%2C3377%2C10404%0A2021-03-26T12%3A15%3A00%2B01%3A00%2C20729%2C3278%2C10442%0A2021-03-27T12%3A15%3A00%2B01%3A00%2C20458%2C3395%2C10460%0A2021-03-28T12%3A15%3A00%2B02%3A00%2C19903%2C3849%2C10466%0A2021-03-29T12%3A15%3A00%2B02%3A00%2C20078%2C3845%2C10452%0A2021-03-30T12%3A15%3A00%2B02%3A00%2C20524%2C3498%2C10436%0A2021-03-31T12%3A15%3A00%2B02%3A00%2C20690%2C3298%2C10423%0A2021-04-01T12%3A15%3A00%2B02%3A00%2C20573%2C3318%2C10430%0A2021-04-02T12%3A15%3A00%2B02%3A00%2C20261%2C3530%2C10466%0A2021-04-03T12%3A15%3A00%2B02%3A00%2C19822%2C3870%2C10499%0A2021-04-04T12%3A15%3A00%2B02%3A00%2C19770%2C3901%2C10490%0A2021-04-05T12%3A15%3A00%2B02%3A00%2C19853%2C3771%2C10454%0A2021-04-06T12%3A15%3A00%2B02%3A00%2C20336%2C3417%2C10419%0A2021-04-07T12%3A15%3A00%2B02%3A00%2C20770%2C3101%2C10435%0A2021-04-08T12%3A15%3A00%2B02%3A00%2C20876%2C2983%2C10389%0A2021-04-09T12%3A15%3A00%2B02%3A00%2C20890%2C2912%2C10397%0A2021-04-10T12%3A15%3A00%2B02%3A00%2C20592%2C3110%2C10405%0A2021-04-11T12%3A15%3A00%2B02%3A00%2C20211%2C3423%2C10412%0A2021-04-12T12%3A15%3A00%2B02%3A00%2C20505%2C3265%2C10363%0A2021-04-13T12%3A15%3A00%2B02%3A00%2C21007%2C2897%2C10323%0A2021-04-14T12%3A15%3A00%2B02%3A00%2C21052%2C2863%2C10304%0A2021-04-15T12%3A15%3A00%2B02%3A00%2C20948%2C2960%2C10278%0A2021-04-16T12%3A15%3A00%2B02%3A00%2C20988%2C2898%2C10232%0A2021-04-17T12%3A15%3A00%2B02%3A00%2C20625%2C3111%2C10252%0A2021-04-18T12%3A15%3A00%2B02%3A00%2C20215%2C3387%2C10259%0A2021-04-19T12%3A15%3A00%2B02%3A00%2C20463%2C3343%2C10219%0A2021-04-20T12%3A15%3A00%2B02%3A00%2C20973%2C2953%2C10167%0A2021-04-21T12%3A15%3A00%2B02%3A00%2C21091%2C2818%2C10194%0A2021-04-22T12%3A15%3A00%2B02%3A00%2C21109%2C2832%2C10152%0A2021-04-23T12%3A15%3A00%2B02%3A00%2C21073%2C2828%2C10140%0A2021-04-24T12%3A15%3A00%2B02%3A00%2C20788%2C2979%2C10156%0A2021-04-25T12%3A15%3A00%2B02%3A00%2C20494%2C3219%2C10149%0A2021-04-26T12%3A15%3A00%2B02%3A00%2C20715%2C3150%2C10089%0A2021-04-27T12%3A15%3A00%2B02%3A00%2C21076%2C2835%2C9992%0A2021-04-28T12%3A15%3A00%2B02%3A00%2C21199%2C2725%2C9976%0A2021-04-29T12%3A15%3A00%2B02%3A00%2C21208%2C2750%2C10016%0A2021-04-30T12%3A15%3A00%2B02%3A00%2C21265%2C2693%2C10051%0A2021-05-01T12%3A15%3A00%2B02%3A00%2C20841%2C2885%2C10040%0A2021-05-02T12%3A15%3A00%2B02%3A00%2C20526%2C3120%2C10081%0A2021-05-03T12%3A15%3A00%2B02%3A00%2C20667%2C3143%2C10049%0A2021-05-04T12%3A15%3A00%2B02%3A00%2C21001%2C2834%2C10070%0A2021-05-05T12%3A15%3A00%2B02%3A00%2C21124%2C2708%2C10079%0A2021-05-06T12%3A15%3A00%2B02%3A00%2C20948%2C2838%2C10074%0A2021-05-07T12%3A15%3A00%2B02%3A00%2C20951%2C2756%2C10084%0A2021-05-08T12%3A15%3A00%2B02%3A00%2C20506%2C3032%2C10163%0A2021-05-09T12%3A15%3A00%2B02%3A00%2C20146%2C3299%2C10159%0A2021-05-10T12%3A15%3A00%2B02%3A00%2C20510%2C3058%2C10138%0A2021-05-11T12%3A15%3A00%2B02%3A00%2C20820%2C2813%2C10116%0A2021-05-12T12%3A15%3A00%2B02%3A00%2C20905%2C2771%2C10092%0A2021-05-13T12%3A15%3A00%2B02%3A00%2C20513%2C2996%2C10112%0A2021-05-14T12%3A15%3A00%2B02%3A00%2C20085%2C3357%2C10110%0A2021-05-15T12%3A15%3A00%2B02%3A00%2C20045%2C3307%2C10150%0A2021-05-16T12%3A15%3A00%2B02%3A00%2C19774%2C3499%2C10182%0A2021-05-17T12%3A15%3A00%2B02%3A00%2C20008%2C3434%2C10150%0A2021-05-18T12%3A15%3A00%2B02%3A00%2C20449%2C3057%2C10141%0A2021-05-19T12%3A15%3A00%2B02%3A00%2C20527%2C2957%2C10100%0A2021-05-20T12%3A15%3A00%2B02%3A00%2C20462%2C3001%2C10145%0A2021-05-21T12%3A15%3A00%2B02%3A00%2C20359%2C3033%2C10119%0A2021-05-22T12%3A15%3A00%2B02%3A00%2C20010%2C3251%2C10164%0A2021-05-23T12%3A15%3A00%2B02%3A00%2C19484%2C3686%2C10188%0A2021-05-24T12%3A15%3A00%2B02%3A00%2C19346%2C3808%2C10214%0A2021-05-25T12%3A15%3A00%2B02%3A00%2C19591%2C3701%2C10188%0A2021-05-26T12%3A15%3A00%2B02%3A00%2C20041%2C3315%2C10171%0A2021-05-27T12%3A15%3A00%2B02%3A00%2C19999%2C3329%2C10182%0A2021-05-28T12%3A15%3A00%2B02%3A00%2C19825%2C3436%2C10169%0A2021-05-29T12%3A15%3A00%2B02%3A00%2C19480%2C3634%2C10209%0A2021-05-30T12%3A15%3A00%2B02%3A00%2C19133%2C3894%2C10209%0A2021-05-31T12%3A15%3A00%2B02%3A00%2C19371%2C3817%2C10203%0A2021-06-01T12%3A15%3A00%2B02%3A00%2C19897%2C3531%2C10161%0A2021-06-02T12%3A15%3A00%2B02%3A00%2C19908%2C3382%2C10172%0A2021-06-03T12%3A15%3A00%2B02%3A00%2C19736%2C3503%2C10175%0A2021-06-04T12%3A15%3A00%2B02%3A00%2C19516%2C3680%2C10171%0A2021-06-05T12%3A15%3A00%2B02%3A00%2C19360%2C3731%2C10239%0A2021-06-06T12%3A15%3A00%2B02%3A00%2C19040%2C3972%2C10235%0A2021-06-07T12%3A15%3A00%2B02%3A00%2C19178%2C3988%2C10176%0A2021-06-08T12%3A15%3A00%2B02%3A00%2C19721%2C3536%2C10133%0A2021-06-09T12%3A15%3A00%2B02%3A00%2C19877%2C3449%2C10095%0A2021-06-10T12%3A15%3A00%2B02%3A00%2C19819%2C3480%2C10140%0A2021-06-11T12%3A15%3A00%2B02%3A00%2C19728%2C3509%2C10174%0A2021-06-12T12%3A15%3A00%2B02%3A00%2C19498%2C3624%2C10228%0A2021-06-13T12%3A15%3A00%2B02%3A00%2C19043%2C3929%2C10260%0A2021-06-14T12%3A15%3A00%2B02%3A00%2C19288%2C3894%2C10239%0A2021-06-15T12%3A15%3A00%2B02%3A00%2C19676%2C3609%2C10221%0A2021-06-16T12%3A15%3A00%2B02%3A00%2C19735%2C3538%2C10221%0A2021-06-17T12%3A15%3A00%2B02%3A00%2C19680%2C3568%2C10243%0A2021-06-18T12%3A15%3A00%2B02%3A00%2C19738%2C3480%2C10224%0A2021-06-19T12%3A15%3A00%2B02%3A00%2C19404%2C3689%2C10212%0A2021-06-20T12%3A15%3A00%2B02%3A00%2C18967%2C4003%2C10255%0A2021-06-21T12%3A15%3A00%2B02%3A00%2C19268%2C3834%2C10236%0A2021-06-22T12%3A15%3A00%2B02%3A00%2C19735%2C3441%2C10209%0A2021-06-23T12%3A15%3A00%2B02%3A00%2C19846%2C3365%2C10251%0A2021-06-24T12%3A15%3A00%2B02%3A00%2C19736%2C3461%2C10279%0A2021-06-25T12%3A15%3A00%2B02%3A00%2C19689%2C3458%2C10292%0A2021-06-26T12%3A15%3A00%2B02%3A00%2C19238%2C3693%2C10343%0A2021-06-27T12%3A15%3A00%2B02%3A00%2C18893%2C3969%2C10374%0A2021-06-28T12%3A15%3A00%2B02%3A00%2C19075%2C3986%2C10340%0A2021-06-29T12%3A15%3A00%2B02%3A00%2C19542%2C3622%2C10315%0A2021-06-30T12%3A15%3A00%2B02%3A00%2C19589%2C3566%2C10315%0A2021-07-01T12%3A15%3A00%2B02%3A00%2C19407%2C3706%2C10349%0A2021-07-02T12%3A15%3A00%2B02%3A00%2C19284%2C3619%2C10312%0A2021-07-03T12%3A15%3A00%2B02%3A00%2C18878%2C3855%2C10337%0A2021-07-04T12%3A15%3A00%2B02%3A00%2C18570%2C4082%2C10360%0A2021-07-05T12%3A15%3A00%2B02%3A00%2C18750%2C4137%2C10354%0A2021-07-06T12%3A15%3A00%2B02%3A00%2C19242%2C3754%2C10317%0A2021-07-07T12%3A15%3A00%2B02%3A00%2C19366%2C3638%2C10311%0A2021-07-08T12%3A15%3A00%2B02%3A00%2C19285%2C3725%2C10317%0A2021-07-09T12%3A15%3A00%2B02%3A00%2C19268%2C3612%2C10320%0A2021-07-10T12%3A15%3A00%2B02%3A00%2C18814%2C3863%2C10334%0A2021-07-11T12%3A15%3A00%2B02%3A00%2C18405%2C4144%2C10353%0A2021-07-12T12%3A15%3A00%2B02%3A00%2C18599%2C4205%2C10313%0A2021-07-13T12%3A15%3A00%2B02%3A00%2C19156%2C3667%2C10290%0A2021-07-14T12%3A15%3A00%2B02%3A00%2C19260%2C3586%2C10288%0A2021-07-15T12%3A15%3A00%2B02%3A00%2C19253%2C3558%2C10262%0A2021-07-16T12%3A15%3A00%2B02%3A00%2C19090%2C3532%2C10284%0A2021-07-17T12%3A15%3A00%2B02%3A00%2C18777%2C3716%2C10342%0A2021-07-18T12%3A15%3A00%2B02%3A00%2C18372%2C4054%2C10358%0A2021-07-19T12%3A15%3A00%2B02%3A00%2C18575%2C4099%2C10328%0A2021-07-20T12%3A15%3A00%2B02%3A00%2C19100%2C3628%2C10286%0A2021-07-21T12%3A15%3A00%2B02%3A00%2C19138%2C3550%2C10235%0A2021-07-22T12%3A15%3A00%2B02%3A00%2C19021%2C3655%2C10212%0A2021-07-23T12%3A15%3A00%2B02%3A00%2C19031%2C3553%2C10205%0A2021-07-24T12%3A15%3A00%2B02%3A00%2C18749%2C3733%2C10262%0A2021-07-25T12%3A15%3A00%2B02%3A00%2C18361%2C4009%2C10274%0A2021-07-26T12%3A15%3A00%2B02%3A00%2C18582%2C4003%2C10275%0A2021-07-27T12%3A15%3A00%2B02%3A00%2C19009%2C3669%2C10233%0A2021-07-28T12%3A15%3A00%2B02%3A00%2C19218%2C3433%2C10231%0A2021-07-29T12%3A15%3A00%2B02%3A00%2C19175%2C3522%2C10256%0A2021-07-30T12%3A15%3A00%2B02%3A00%2C19026%2C3513%2C10280%0A2021-07-31T12%3A15%3A00%2B02%3A00%2C18722%2C3667%2C10304%0A2021-08-01T12%3A15%3A00%2B02%3A00%2C18342%2C3881%2C10336%0A2021-08-02T12%3A15%3A00%2B02%3A00%2C18427%2C4043%2C10337%0A2021-08-03T12%3A15%3A00%2B02%3A00%2C18904%2C3693%2C10311%0A2021-08-04T12%3A15%3A00%2B02%3A00%2C18990%2C3656%2C10308%0A2021-08-05T12%3A15%3A00%2B02%3A00%2C18938%2C3677%2C10251%0A2021-08-06T12%3A15%3A00%2B02%3A00%2C19022%2C3510%2C10252%0A2021-08-07T12%3A15%3A00%2B02%3A00%2C18772%2C3571%2C10281%0A2021-08-08T12%3A15%3A00%2B02%3A00%2C18463%2C3802%2C10314%0A2021-08-09T12%3A15%3A00%2B02%3A00%2C18535%2C3954%2C10249%0A2021-08-10T12%3A15%3A00%2B02%3A00%2C18860%2C3762%2C10242%0A2021-08-11T12%3A15%3A00%2B02%3A00%2C19013%2C3627%2C10221%0A2021-08-12T12%3A15%3A00%2B02%3A00%2C18929%2C3698%2C10219%0A2021-08-13T12%3A15%3A00%2B02%3A00%2C18982%2C3607%2C10187%0A2021-08-14T12%3A15%3A00%2B02%3A00%2C18683%2C3763%2C10238%0A2021-08-15T12%3A15%3A00%2B02%3A00%2C18373%2C3990%2C10253%0A2021-08-16T12%3A15%3A00%2B02%3A00%2C18591%2C3926%2C10248%0A2021-08-17T12%3A15%3A00%2B02%3A00%2C18805%2C3728%2C10196%0A2021-08-18T12%3A15%3A00%2B02%3A00%2C18922%2C3645%2C10218%0A2021-08-19T12%3A15%3A00%2B02%3A00%2C18943%2C3600%2C10206%0A2021-08-20T12%3A15%3A00%2B02%3A00%2C18966%2C3500%2C10236%0A2021-08-21T12%3A15%3A00%2B02%3A00%2C18550%2C3693%2C10295%0A2021-08-22T12%3A15%3A00%2B02%3A00%2C18218%2C3925%2C10309%0A2021-08-23T12%3A15%3A00%2B02%3A00%2C18468%2C3934%2C10293%0A2021-08-24T12%3A15%3A00%2B02%3A00%2C18907%2C3525%2C10290%0A2021-08-25T12%3A15%3A00%2B02%3A00%2C18891%2C3585%2C10302%0A2021-08-26T12%3A15%3A00%2B02%3A00%2C18994%2C3480%2C10284%0A2021-08-27T12%3A15%3A00%2B02%3A00%2C18930%2C3474%2C10310%0A2021-08-28T12%3A15%3A00%2B02%3A00%2C18573%2C3585%2C10377%0A2021-08-29T12%3A15%3A00%2B02%3A00%2C18298%2C3767%2C10409%0A2021-08-30T12%3A15%3A00%2B02%3A00%2C18493%2C3837%2C10347%0A2021-08-31T12%3A15%3A00%2B02%3A00%2C18893%2C3545%2C10280%0A2021-09-01T12%3A15%3A00%2B02%3A00%2C19140%2C3402%2C10266%0A2021-09-02T12%3A15%3A00%2B02%3A00%2C19246%2C3426%2C10367%0A2021-09-03T12%3A15%3A00%2B02%3A00%2C19279%2C3354%2C10676%0A2021-09-04T12%3A15%3A00%2B02%3A00%2C18978%2C3368%2C10406%0A2021-09-05T12%3A15%3A00%2B02%3A00%2C18738%2C3564%2C10449%0A2021-09-06T12%3A15%3A00%2B02%3A00%2C18919%2C3640%2C10389%0A2021-09-07T12%3A15%3A00%2B02%3A00%2C19341%2C3291%2C10351%0A2021-09-08T12%3A15%3A00%2B02%3A00%2C19494%2C3174%2C10321%0A2021-09-09T12%3A15%3A00%2B02%3A00%2C19474%2C3151%2C10320%0A2021-09-10T12%3A15%3A00%2B02%3A00%2C19511%2C3076%2C10349%0A2021-09-11T12%3A15%3A00%2B02%3A00%2C19179%2C3212%2C10406%0A2021-09-12T12%3A15%3A00%2B02%3A00%2C18863%2C3452%2C10416%0A2021-09-13T12%3A15%3A00%2B02%3A00%2C19003%2C3557%2C10358%0A2021-09-14T12%3A15%3A00%2B02%3A00%2C19512%2C3161%2C10338%0A2021-09-15T12%3A15%3A00%2B02%3A00%2C19578%2C3061%2C10316%0A2021-09-16T12%3A15%3A00%2B02%3A00%2C19619%2C2990%2C10317%0A2021-09-17T12%3A15%3A00%2B02%3A00%2C19443%2C3101%2C10324%0A2021-09-18T12%3A15%3A00%2B02%3A00%2C18996%2C3332%2C10360%0A2021-09-19T12%3A15%3A00%2B02%3A00%2C18661%2C3546%2C10363%0A2021-09-20T12%3A15%3A00%2B02%3A00%2C18785%2C3646%2C10376%0A2021-09-21T12%3A15%3A00%2B02%3A00%2C19244%2C3337%2C10318%0A2021-09-22T12%3A15%3A00%2B02%3A00%2C19416%2C3150%2C10326%0A2021-09-23T12%3A15%3A00%2B02%3A00%2C19364%2C3200%2C10305%0A2021-09-24T12%3A15%3A00%2B02%3A00%2C19306%2C3197%2C10311%0A2021-09-25T12%3A15%3A00%2B02%3A00%2C18997%2C3418%2C10352%0A2021-09-26T12%3A15%3A00%2B02%3A00%2C18728%2C3483%2C10376%0A2021-09-27T12%3A15%3A00%2B02%3A00%2C18870%2C3545%2C10349%0A2021-09-28T12%3A15%3A00%2B02%3A00%2C19339%2C3235%2C10312%0A2021-09-29T12%3A15%3A00%2B02%3A00%2C19488%2C3133%2C10299%0A2021-09-30T12%3A15%3A00%2B02%3A00%2C19369%2C3085%2C10416%0A2021-10-01T12%3A15%3A00%2B02%3A00%2C19231%2C3096%2C10416%0A2021-10-02T12%3A15%3A00%2B02%3A00%2C18862%2C3221%2C10478%0A2021-10-03T12%3A15%3A00%2B02%3A00%2C18546%2C3471%2C10494%0A2021-10-04T12%3A15%3A00%2B02%3A00%2C18713%2C3583%2C10437%0A2021-10-05T12%3A15%3A00%2B02%3A00%2C19215%2C3155%2C10442%0A2021-10-06T12%3A15%3A00%2B02%3A00%2C19347%2C3085%2C10434%0A2021-10-07T12%3A15%3A00%2B02%3A00%2C19391%2C3042%2C10408%0A2021-10-08T12%3A15%3A00%2B02%3A00%2C19309%2C3033%2C10418%0A2021-10-09T12%3A15%3A00%2B02%3A00%2C18944%2C3161%2C10479%0A2021-10-10T12%3A15%3A00%2B02%3A00%2C18611%2C3434%2C10491%0A2021-10-11T12%3A15%3A00%2B02%3A00%2C18777%2C3402%2C10470%0A2021-10-12T12%3A15%3A00%2B02%3A00%2C19258%2C3078%2C10462%0A2021-10-13T12%3A15%3A00%2B02%3A00%2C19395%2C2912%2C10473%0A2021-10-14T12%3A15%3A00%2B02%3A00%2C19239%2C3000%2C10469%0A2021-10-15T12%3A15%3A00%2B02%3A00%2C19153%2C2968%2C10462%0A2021-10-16T12%3A15%3A00%2B02%3A00%2C18785%2C3170%2C10509%0A2021-10-17T12%3A15%3A00%2B02%3A00%2C18548%2C3296%2C10503%0A2021-10-18T12%3A15%3A00%2B02%3A00%2C18770%2C3345%2C10466%0A2021-10-19T12%3A15%3A00%2B02%3A00%2C19234%2C2959%2C10447%0A2021-10-20T12%3A15%3A00%2B02%3A00%2C19340%2C2861%2C10457%0A2021-10-21T12%3A15%3A00%2B02%3A00%2C19447%2C2717%2C10460%0A2021-10-22T12%3A15%3A00%2B02%3A00%2C19390%2C2689%2C10485%0A2021-10-23T12%3A15%3A00%2B02%3A00%2C19015%2C2890%2C10512%0A2021-10-24T12%3A15%3A00%2B02%3A00%2C18700%2C3066%2C10495%0A2021-10-25T12%3A15%3A00%2B02%3A00%2C19017%2C3042%2C10513%0A2021-10-26T12%3A15%3A00%2B02%3A00%2C19356%2C2787%2C10476%0A2021-10-27T12%3A15%3A00%2B02%3A00%2C19561%2C2532%2C10451%0A2021-10-28T12%3A15%3A00%2B02%3A00%2C19436%2C2605%2C10475%0A2021-10-29T12%3A15%3A00%2B02%3A00%2C19384%2C2572%2C10404%0A2021-10-30T12%3A15%3A00%2B02%3A00%2C19091%2C2715%2C10438%0A2021-10-31T12%3A15%3A00%2B01%3A00%2C18815%2C2931%2C10454%0A2021-11-01T12%3A15%3A00%2B01%3A00%2C18981%2C2951%2C10417%0A2021-11-02T12%3A15%3A00%2B01%3A00%2C19420%2C2722%2C10385%0A2021-11-03T12%3A15%3A00%2B01%3A00%2C19728%2C2553%2C10320%0A2021-11-04T12%3A15%3A00%2B01%3A00%2C19797%2C2503%2C10308%0A2021-11-05T12%3A15%3A00%2B01%3A00%2C19682%2C2461%2C10179%0A2021-11-06T12%3A15%3A00%2B01%3A00%2C19285%2C2663%2C10147%0A2021-11-07T12%3A15%3A00%2B01%3A00%2C19080%2C2801%2C10150', 'r');
        while ($l = fgetcsv($data, 4000, ",", '"')) {
            $date = substr($l[0], 0, 10);
            if (strlen($date) == 10) {
                $this->pdo->prepare('INSERT IGNORE INTO divi_intensivbetten (date, belegt, frei, notfall) VALUES (:date, :belegt, :frei, :notfall)')
                    ->execute(['date' => $date, 'belegt' => $l[1], 'frei' => $l[2], 'notfall' => $l[3]]);
            }
            print_r($l);
        }
    }

}