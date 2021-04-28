const child = require('child_process');
const fetch = require('node-fetch');
const {MySQL} = require("mysql-promisify");
const fs = require('fs');
const crypto = require('crypto');
const config = JSON.parse(fs.readFileSync('../../config/config.prod.json', 'utf8'));
const TwitterClient = require('./lib/Twitter.js');
const screenshotConfig = require('./config/screenshots');
const dataIds = require('./config/dataIds');

const keys = config.twitter;
config.twitter.token = config.twitter.access_token_key;
config.twitter.token_secret = config.twitter.access_token_secret;

const timezone = 'UTC';
process.env.TZ = timezone;

let db = new MySQL(config.mysql);
let nowDateTime;
let nowDate;


const Twitter = new TwitterClient(config.twitter);
/*
Twitter.stream('#CoronaHH,@Corona_HH');
Twitter.onStreamData( async (obj) => {
    let match = obj.text.match(/^Seit gestern wurden [0-9]* weitere Neuinfektionen gemeldet. /);
    if (match !== null) {
        try {

            let media = await getMedia();
            let m1 = await Twitter.mediaUpload('/var/coronahh/twitterpic/' + media + '-neuinfektionen.png');
            let m2 = await Twitter.mediaUpload('/var/coronahh/twitterpic/' + media + '-todesfaelle.png');
            let tweet = await Twitter.tweet('Neuinfektionen und Todesfälle im Wochenvergleich https://coronahh.de/ @corona_hh #CoronaHH #Hamburg', [m1,m2], obj.id_str);
            await db.query({sql: 'UPDATE twitterpost SET replied=1 WHERE date=date(now())'});
        } catch (e) {
            console.log("Error: " + e.message);
        }
    }
    match = obj.text.match(/!test/);
    if (match !== null) {
        try {

            let media = await getLastMedia();
            let m1 = await Twitter.mediaUpload('/var/coronahh/twitterpic/' + media + '-neuinfektionen.png');
            let tweet = await Twitter.tweet('@' + obj.user.screen_name + ' ' + new Date().toDateString() + ' Hier sind die Neuinfektionen in Hamburg im Wochenvergleich:', [m1], obj.id_str);

        } catch (e) {
            console.log("Error: " + e.message);
        }
    }
    console.log('data', obj);
});
*/




check();
setInterval( check, 60000);

async function check() {


    nowDateTime = new Date().toISOString().split('.')[0].replaceAll(':', '-');
    nowDate = new Date().toISOString().split('T')[0];


    try {
        let page = await getHamburgDe();

        //let page = fs.readFileSync('/var/coronahh/hamburgde-2021-04-07T12-21-59.html', 'utf8');
        let updateCount = 0;
        updateCount += await processPage(page);
        updateCount += await processHospitalisierungen(page);
        updateCount += await processAlterGeschlecht(page);
        let chartChanges = await processCharts(page);

        if (updateCount + chartChanges > 0) {
            await updateTodesfaelle();
            console.log("restart memcache");
            child.execSync(`service memcached restart`);
        }

        if (chartChanges > 0) {
            let { results } = await db.query({sql: 'SELECT * FROM twitterpost WHERE date=date(now())'});
            if (!results[0] || results[0]['tweeted'] == 0) {

                let media = [];
                for (n in screenshotConfig) {
                    if (screenshotConfig[n].dailyTweet) {
                        let filename = new Date().toISOString().split('T')[0];
                        let dst = '/var/coronahh/twitterpic/' + filename + '-' + n + '.png'
                        screenshot(screenshotConfig[n], dst);
                        let sMedia = await Twitter.mediaUpload(dst);
                        media.push(sMedia);
                    }
                }

                mr = await db.query({
                    sql: 'REPLACE INTO twitterpost (date, media) VALUES (now(), :media)',
                    params: {media: media.join(',')}
                });

                let tweet = await Twitter.tweet("Neuinfektionen und Todesfälle im Wochenvergleich https://coronahh.de/ @corona_hh #CoronaHH #Hamburg", media);
                await db.query({sql: 'UPDATE twitterpost SET tweeted=1 WHERE date=date(now())'});
            } else {
                console.log('Already tweeted today');
            }
        } else {
            console.log('No changes');
        }
    } catch (e) {
        console.log("Quit: " + e.message);
    }


}


async function processPage(page) {
    let regex = /<ul class="nav-main__wrapper.*?>(.*?)<\/ul>/g;
    let sections = page.match(/<ul class='nav-main__wrapper'.*?>(.*?)<\/ul>/g);
    updateCount = 0;
    for (n in sections) {
        updateCount += await processSection(sections[n]);
    }
    return updateCount;
}

function screenshot(cfg, dst) {
    console.log('Screenshot ' + cfg.url + ' to ' + dst);
    r = child.execSync(`chromium-browser --no-sandbox --headless --disable-gpu --screenshot=/tmp/screen.png --window-size=1500,3024 https://coronahh.de${cfg.url}`);
    r = child.execSync(`convert /tmp/snap.chromium/tmp/screen.png -crop ${cfg.crop} ${dst}`);
}


async function processCharts(page) {
    let regex = /myData = (.*?)var canvas/sg;
    let match = regex.exec(page);
    let updateCount = 0;
    while (match != null) {
        let labels = match[1].match(/labels : \[(.*?)\]/)[1].split(',');
        for (n in labels) {
            labels[n] = labels[n].replaceAll("'", '').split('.').reverse().join('-');
        }

        let regex2 = /label: '(.*?)'.*?data : \[(.*?)\]/sg;
        let match2 = regex2.exec(match[1])
        while (match2 != null) {
            let llabel = match2[1];
            let values = match2[2].split(',');
            if (llabel == 'SARS-CoV-2 Infektionen') {
                updateCount += await updateChartData('cases', labels, values, true);
            } else if (llabel == 'Erstimpfungen insgesamt') {
                updateCount += await updateChartData('vaccination-1st', labels, values, true);
            } else if (llabel == 'Zweitimpfungen insgesamt') {
                updateCount += await updateChartData('vaccination-2nd', labels, values, true);
            } else if (llabel == 'Tägliche Erstimpfungen') {
                updateCount += await updateChartData('vaccination-1st-daily', labels, values);
            } else if (llabel == 'Tägliche Zweitimpfungen') {
                updateCount += await updateChartData('vaccination-2nd-daily', labels, values);
            }
            match2 = regex2.exec(match[1])
        }
        match = regex.exec(page);
    }
    return updateCount;
}

async function updateChartData(column, dates, values, diff) {
    let updateCount = 0;
    let lastValue = 0;
    for (n in dates) {
        let value = values[n] * 1;
        if (diff) {
            value = value - lastValue;
            lastValue = values[n] * 1;
        }
        updateCount += await _insertCases(dates[n], column, value);
    }
    return updateCount;
}

async function processHospitalisierungen(page) {
    let section = page.match(/(<h3>Patienten in klinischer Behandlung<\/h3>.*?<\/div>)<div class="masonry-helper">/)[1];
    let stand = section.match(/([0-9]{2}\.[0-9]{2}\.2[0-9]{3})/)[0];
    stand = stand.split('.').reverse().join('-');

    let regex = /<tr>.*?<td.*?>(.*?)<\/td.*?<td.*?>(.*?)<\/td/g;
    let match = regex.exec(section);
    let updateCount = 0;
    while (match != null) {
        if (typeof dataIds[match[1]] != 'undefined') {
            let inserted = await _insertData(stand, dataIds[match[1]], match[2]);
            updateCount += inserted;
        }
        match = regex.exec(section);
    }

    if (updateCount > 0) {
        let sql = `REPLACE into hospitalisierungen_2 (
                
                select 
                'x' as csvid,
                date.date,
                stationaer.value as stationaer, 
                null as stationaerhh,
                
                normalstation.value as normalstation,
                normalstationhh.value as normalstationhh,
                (normalstation.value-normalstationhh.value) as normalstation_nichthh,
                
                
                intensivstation.value as intensivstation,
                intensivstationhh.value as intensivstationhh,
                (intensivstation.value-intensivstationhh.value) as intensivstation_nichthh,
                '-' as weekday
                
                 from 
                 (select distinct date FROM data) as date LEFT JOIN 
                (select date,value FROM data WHERE id_column = 33) as stationaer on date.date=stationaer.date LEFT JOIN 
                (select date,value FROM data WHERE id_column = 36) as intensivstation on date.date=intensivstation.date LEFT JOIN 
                (select date,value FROM data WHERE id_column = 37) as intensivstationhh on date.date=intensivstationhh.date LEFT JOIN 
                (select date,value FROM data WHERE id_column = 35) as normalstation on date.date=normalstation.date LEFT JOIN 
                (select date,value FROM data WHERE id_column = 38) as normalstationhh on date.date=normalstationhh.date
                
                where date.date > now() - interval 3 day
                
                
                )`;
        await db.query({sql: sql});
    }

    return updateCount;
}

async function processAlterGeschlecht(page) {
    let section = page.match(/(<h3>Verteilung der Infizierten nach Alter und Geschlecht<\/h3>.*?<\/div>)<div class="masonry-helper">/)[1];
    let stand = section.match(/Stand: (.*?);/)[1];
    let monate = {
        'Januar': 'January',
        'Februar': 'February',
        'März': 'March',
        'Mai': 'May',
        'Juni': 'June',
        'Juli': 'July',
        'Oktober': 'October',
        'Dezember': 'December'
    }
    for (n in monate) {
        stand = stand.replace(n, monate[n]);
    }
    try {
        stand = new Date(Date.parse(stand.split(',')[0] + " 12:00:00")).toISOString().split("T")[0];
    } catch (e) {
        console.log('Could not parse date');
        return;
    }

    let regex = /<tr>.*?<td.*?>(.*?)<\/td.*?<td.*?>(.*?)<\/td.*?<td.*?>(.*?)<\/td/g;
    let match = regex.exec(section);
    let updateCount = 0;
    while (match != null) {
        if (typeof dataIds[match[1] + ' männlich'] != 'undefined') {
            let inserted = await _insertData(stand, dataIds[match[1] + ' männlich'], match[2]);
            updateCount += inserted;
        }
        if (typeof dataIds[match[1] + ' weiblich'] != 'undefined') {
            let inserted = await _insertData(stand, dataIds[match[1] + ' weiblich'], match[3]);
            updateCount += inserted;
        }
        match = regex.exec(section);
    }

    let unbekannt = page.match(/bei ([0-9]*) Fällen/);
    if (unbekannt !== null) {
        let inserted = await _insertData(stand, dataIds['alter-unbekannt'], unbekannt[1]);
        updateCount += inserted;
    }
    return updateCount;
}

async function updateTodesfaelle() {
    let {results} = await db.query({
        sql: 'SELECT * FROM data WHERE id_column=58 ORDER BY date DESC LIMIT 3'
    });
    for (n in results) {
        await db.query({
            sql: 'UPDATE cases SET deaths=:t WHERE date=:date',
            params: {
                t: results[n]['value'],
                date: results[n].date.toISOString().split('T')[0]
            }
        });
    }
}

async function processSection(section) {

    let headline = (section.match(/teaser-headline deashboard_h'>(.*?)</)[1]).trim();
    let stand = (section.match(/\(Stand: (.*?)\)/)[1]).trim().split('.').reverse().join('-');

    let regex = /nav_text_align'>(.*?):.*?<span class='dashboar_number'>(.*?)<\/span>/g;
    let match = regex.exec(section);
    let updateCount = 0;
    while (match != null) {
        if (typeof dataIds[match[1]] != 'undefined') {
            let inserted = await _insertData(stand, dataIds[match[1]], match[2]);
            updateCount += inserted;
        }
        match = regex.exec(section);
    }
    return updateCount;
}

async function _insertData(date, id_column, value) {
    mr = await db.query({
        sql: 'INSERT IGNORE INTO data (date, id_column, value) VALUES (:date, :id_column, :value)',
        params: {
            date: date,
            id_column: id_column,
            value: value,
        }
    });
    return mr.results.affectedRows;
}

async function _insertCases(date, column, value) {
    let {results} = await db.query({
        sql: 'SELECT `' + column + '` AS value FROM cases WHERE date=:date',
        params: {date: date}
    });
    if (results[0] && results[0]['value'] == value) {
        return 0;
    }
    console.log(date, column, value);
    mr = await db.query({
        sql: 'INSERT INTO cases (`date`, `' + column + '`) VALUES (:date, :value) ON DUPLICATE KEY update `' + column + '`=:value',
        params: {
            date: date,
            value: value,
        }
    });
    return mr.results.affectedRows;
}

async function getHamburgDe() {
    let url = 'https://www.hamburg.de/corona-zahlen';

    const page = await fetch(url);
    let text = await page.text();

    text = text.replace(/.*?<\/head>/si, '');
    text = text.replace(/<div class="header__weather.*?\/div>/si, '');
    text = text.replace(/<span class="weather-temp".*?\/span>/si, '');
    text = text.replace(/<div class="container--bo-quicksearch">.*/si, '');

    let sha1 = crypto.createHash('sha1').update(text).digest('hex');

    let {results} = await db.query({
        sql: 'SELECT * FROM scraping_updates WHERE url=:url',
        params: {
            url: url
        }
    });

    if (results[0] && results[0].sha1 == sha1) {
        throw new Error(`No Changes at ${url}.`);
    }

    mr = await db.query({
        sql: 'INSERT INTO scraping_updates (url, sha1, date) VALUES (:url, :sha1, now()) ON DUPLICATE KEY update sha1=:sha1, date=now()',
        params: {
            url: url,
            sha1: sha1
        }
    });

    const today = new Date().toISOString().split('.')[0].replaceAll(':', '-');
    fs.writeFileSync(`/var/coronahh/hamburgde-${nowDateTime}.html`, text);
    log(`Write source to /var/coronahh/hamburgde-${nowDateTime}.html`);
    return text;
}

function log(msg) {
    console.log(msg);
}



async function getMedia() {
    let { results } = await db.query({sql: 'SELECT *, now() as n  FROM twitterpost WHERE date=date(now())'});
    if (results[0] && results[0]['replied'] == 0) {
        return results[0]['date'].toISOString().split('T')[0];
    }
    throw new Error('Not today again');
}

async function getLastMedia() {
    let { results } = await db.query({sql: 'SELECT *  FROM twitterpost ORDER BY date DESC LIMIT 1'});
    if (results[0]) {
        return results[0]['date'].toISOString().split('T')[0];
    }
    throw new Error('Not today again');
}