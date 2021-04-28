const TwitterStream = require('twitter-stream-api');
const TwitterApi = require('twitter');
const fs = require('fs');

class Twitter {

    streamData = [];

    constructor(keys) {
        keys.token = keys.access_token_key;
        keys.token_secret = keys.access_token_secret;
        this.keys = keys;
        this.client = new TwitterApi(keys);
    }

    onStreamData(func) {
        this.streamData.push(func);
    }

    stream(keywords) {
        const TwitterStreamClient = new TwitterStream(this.keys, false);
        TwitterStreamClient.stream('statuses/filter', {
            track: keywords
        });

        TwitterStreamClient.on('data', async (obj) => {
            obj = JSON.parse(obj.toString());
            for (let n in this.streamData) {
                this.streamData[n](obj);
            }
        });
    }

    tweet(msg, media, reply) {
        return new Promise((resolve, reject) => {
            const status = {
                status: msg,
            }
            if (typeof media != 'undefined' && media.length > 0) {
                status.media_ids = media.join(',');
            }
            if (typeof reply != 'undefined') {
                status.in_reply_to_status_id = reply;
            }
            console.log("Tweeting ", status);
            this.client.post("statuses/update", status, function (error, tweet, response) {
                if (error) {
                    reject(error);
                } else {
                    resolve(tweet);
                }
            })
        });
    }

    mediaUpload(dst) {
        let imageData = fs.readFileSync(dst);
        return new Promise((resolve, reject) => {
            this.client.post("media/upload", {media: imageData}, function (error, media, response) {
                if (error) {
                    reject(error);
                } else {
                    resolve(media.media_id_string);
                }
            });
        })
    }
}

module.exports = Twitter;