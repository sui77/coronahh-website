FROM trafex/php-nginx

USER root
RUN apk add --no-cache \
    php81-pecl-memcache \
    php81-pdo \
    php81-pdo_mysql \
    memcached \
    curl \
    nano



#RUN mkdir /etc/nginx/conf.d
COPY ./config/nginx.conf /etc/nginx/conf.d/nginx.conf

WORKDIR /var/www
COPY . ./

ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.2.1/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=d7f4c0886eb85249ad05ed592902fa6865bb9d70

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

RUN cat config/supervisord.conf >> /etc/supervisor/conf.d/supervisord.conf


USER nobody
