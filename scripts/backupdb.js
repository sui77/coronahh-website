const child = require('child_process');
const fs = require('fs');
var AWS = require('aws-sdk');
AWS.config.update({region: 'eu-central-1'});
const s3 = new AWS.S3();

const config = JSON.parse(fs.readFileSync('../config/config.json', 'utf8'));

const today = new Date().toISOString().split('T')[0]
console.log(today);


child.execSync(`mysqldump ${config.mysql.database} > /tmp/${config.mysql.database+today}.sql` );
child.execSync(`gzip /tmp/${config.mysql.database+today}.sql` );
s3copy(`/tmp/${config.mysql.database+today}.sql.gz`, `coronahh/${config.mysql.database+today}.sql.gz`);

function s3copy(src, dst) {

    let stream = fs.createReadStream(src);
    s3.upload({Bucket: config.s3.bucket, Key: dst, Body: stream}, function (err, data) {
        if (err) {
            console.log(err);
            //reject(err);
        }
        if (data) {
            //resolve(data.Location);
        }
    });
}