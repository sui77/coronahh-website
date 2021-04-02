const fetch = require('node-fetch');
const { MySQL } = require("mysql-promisify");
const fs = require('fs');
const crypto = require('crypto');
const config = JSON.parse(fs.readFileSync('../../config/config.json', 'utf8'));

const url = 'https://www.hamburg.de/corona-zahlen';
const db = new MySQL(config.mysql);

( async() => {
    const page = await fetch (url);
    let text = await page.text();

    text = text.replace(/.*?<\/head>/si, '');
    text = text.replace(/<div class="header__weather.*?\/div>/si, '');
    text = text.replace(/<span class="weather-temp".*?\/span>/si, '');

    const sha1 = crypto.createHash('sha1');
    sha1.update(text);
    const sha1String = sha1.digest('hex')

    let { results } = await db.query({
       sql: 'SELECT * FROM scraping_updates WHERE url=:url',
       params: {
           url: url
       }
    });

    if (results[0].sha1 == sha1String) {
        console.log('No changes');
      //  process.exit();
    }

    const today = new Date().toISOString().split('.')[0].replaceAll(':', '-');
    fs.writeFileSync(`/var/coronahh/hamburgde-${today}.html`, text);
    console.log( sha1String + ' ' + today);


    mr = await db.query({
        sql: 'INSERT INTO scraping_updates (url, sha1, date) VALUES (:url, :sha1, now()) ON DUPLICATE KEY update sha1=:sha1, date=now()',
        params: {
            url: url,
            sha1: sha1String
        }
    })


    let match = text.match(/myData = (.*?)var canvas/si);
    let inzidenzen = match[1];
    text = text.replace(match[1], '');

    match = text.match(/myData = (\{.*?)var canvas/si);
    let impfungen = match[1];


    // Inzidenzen
    eval( 'data = ' + inzidenzen + ';');
    let labels = data.labels;
    for (n in data.datasets) {
        if (data.datasets[n].label == 'SARS-CoV-2 7-Tagesinzidenz pro 100.000 Einwohner') {
            inzidenzen = data.datasets[n].data;
        } else if (data.datasets[n].label == 'SARS-CoV-2 Infektionen') {
            infektionen = data.datasets[n].data;
        }
    }

    let lastCases = 0;
    for (n in labels) {
        let cases = infektionen[n]-lastCases;
        if (cases < 0 ) { cases = 0; }
        mr = await db.query({
            sql: 'INSERT INTO cases (date, cases) VALUES (:date, :cases) ON DUPLICATE KEY update cases=:cases',
            params: {
                date: mdate(labels[n]),
                cases: cases
            }
        })
        lastCases = infektionen[n];
    }


    // impfungen
    eval( 'data = ' + impfungen + ';');
    labels = data.labels;
    for (n in data.datasets) {
        if (data.datasets[n].label == 'Erstimpfungen insgesamt') {
            erstimpfungen = data.datasets[n].data;
        } else if (data.datasets[n].label == 'Zweitimpfungen insgesamt') {
            zweitimpfungen = data.datasets[n].data;
        }
    }

    let lastErst = 0;
    let lastZweit = 0;
    for (n in labels) {
        let erst = erstimpfungen[n]-lastErst;
        let zweit = zweitimpfungen[n]-lastZweit;
        lastErst = erstimpfungen[n];
        lastZweit = zweitimpfungen[n];
        mr = await db.query({
            sql: 'INSERT INTO cases (date, `vaccination-1st`, `vaccination-2nd`) VALUES (:date, :erstimpfung, :zweitimpfung) ON DUPLICATE KEY update `vaccination-1st`=:erstimpfung, `vaccination-2nd`=:zweitimpfung',
            params: {
                date: mdate(labels[n]),
                erstimpfung: erst,
                zweitimpfung: zweit,
            }
        })
    }

    process.exit();
})();


function mdate(d) {
    let x = d.split('.');
    return x[2] + '-' + x[1] + '-' + x[0];
}