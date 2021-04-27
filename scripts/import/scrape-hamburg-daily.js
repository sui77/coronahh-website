const child = require('child_process');
const fetch = require('node-fetch');
const { MySQL } = require("mysql-promisify");
const fs = require('fs');
const crypto = require('crypto');
const config = JSON.parse(fs.readFileSync('../../config/config.json', 'utf8'));
const Twitter = require('twitter');

let screenshotConfig = {
    neuinfektionen: {
        name: 'neuinfektionen',
        url: '/neuinfektionen',
        crop: '1185x730+280+770',
    },
    todesfaelle: {
        name: 'todesfaelle',
        url: '/todesfaelle',
        crop: '1185x640+280+732',
    },
}

const url = 'https://www.hamburg.de/corona-zahlen';
const db = new MySQL(config.mysql);

( async() => {


    let now = new Date();
    console.log(now.getMinutes(), now.getHours() );
    nowTs = now.toISOString().split('T')[0];
    if (now.getHours() != 11 && now.getHours() != 12) {
        console.log('Its not time');
        process.exit();
    }

    const page = await fetch (url);
    let text = await page.text();

    text = text.replace(/.*?<\/head>/si, '');
    text = text.replace(/<div class="header__weather.*?\/div>/si, '');
    text = text.replace(/<span class="weather-temp".*?\/span>/si, '');
    text = text.replace(/<div class="container--bo-quicksearch">.*/si, '')

    let stand = text.match(/>\(Stand: (.*?)\)/);
    let tmps = stand[1].split('.');
    let standCurrent = tmps[2]+"-"+tmps[1]+"-"+tmps[0];
    console.log(standCurrent, nowTs);

    let todesf = text.match(/>Neue Todesfälle: <span class='dashboar_number'>(.*?)</);
console.log(todesf[1], todesf[1]*1);

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
        process.exit();
    }

    if (nowTs == standCurrent) {
        console.log('ndeaths');
        mr = await db.query({
            sql: 'INSERT INTO cases (date, deaths) VALUES (:date, :deaths) ON DUPLICATE KEY update deaths=:deaths',
            params: {
                date: nowTs,
                deaths: todesf[1]*1
            }
        })
    } else {
        console.log('no new deaths');
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


    console.log('extract_data');
    fs.writeFileSync('./data.json', "{}");
    r = child.execSync(`python3 /root/hamburg_corona_zahlen/extract_data.py` );
    console.log(r.toString());

    console.log('data2db');
    r = child.execSync(`php import-datajson.php`)
    console.log(r.toString());

    r = child.execSync(`service memcached restart`)


    for (n in screenshotConfig) {
        screenshot( screenshotConfig[n]);
    }

    const client = new Twitter(config.twitter);
    client.post("media/upload", {media: screenshotConfig.neuinfektionen.imageData}, function(error1, media1, response) {
        client.post("media/upload", {media: screenshotConfig.todesfaelle.imageData}, function(error2, media2, response) {
            if (error1 || error2) {
                console.log(error1, error2)
                process.exit();
            } else {
                console.log( media1.media_id_string +','+media2.media_id_string );
                const status = {
                    status: "Neuinfektionen und Todesfälle im Wochenvergleich https://coronahh.de/ @corona_hh #CoronaHH #Hamburg",
                    media_ids: media1.media_id_string +','+media2.media_id_string
                }

                client.post("statuses/update", status, function (error, tweet, response) {
                    if (error) {
                        console.log(error)
                        process.exit();
                    } else {
                        console.log("Successfully tweeted an image!")
                        process.exit();
                    }
                })
            }
        });
    })


})();


function mdate(d) {
    let x = d.split('.');
    return x[2] + '-' + x[1] + '-' + x[0];
}

function screenshot(cfg) {
    let filename = new Date().toISOString().split('T')[0];
    let dst = '/var/coronahh/twitterpic/' + filename + '-' + cfg.name + '.png'

    console.log('Screenshot ' + cfg.name + ' to ' + dst);

    r = child.execSync(`chromium-browser --no-sandbox --headless --disable-gpu --screenshot=/tmp/screen.png --window-size=1500,3024 https://coronahh.de${cfg.url}`);
    r = child.execSync(`convert /tmp/snap.chromium/tmp/screen.png -crop ${cfg.crop} ${dst}`);
    cfg.imageData = fs.readFileSync(dst);
}