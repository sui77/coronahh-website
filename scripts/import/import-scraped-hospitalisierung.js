const {MySQL} = require("mysql-promisify");
const fs = require('fs');
const config = JSON.parse(fs.readFileSync('../../config/config.json', 'utf8'));

const db = new MySQL(config.mysql);


let cols = {
    33: 'stationaer',
    34: 'sationaerhh',
    35: 'normalstation',
    36: 'intensivstation',
    37: 'intensivstationhh',
    38: 'normalstationhh',
};

(async () => {

    for (col in cols) {

        const sql = 'SELECT * FROM `data` WHERE id_column=:col ORDER BY date';

        let {results} = await db.query({
            sql: sql,
            params: {
                col: col
            }
        });

        let lastValue = 0;
        for (n in results) {
            console.log(results[n]);
            let value = results[n]['value'] - lastValue;
            lastValue = results[n]['value'];
            value = lastValue;
            await db.query({
                sql: `INSERT INTO hospitalisierungen (date, ${cols[col]}) VALUES (:date, :value) ON DUPLICATE KEY update ${cols[col]}=:value`,
                params: {
                    date: results[n]['date'],
                    value: value
                }
            });
        }
    }

    process.exit();
})();