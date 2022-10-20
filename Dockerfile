FROM trafex/php-nginx

USER root
RUN apk add --no-cache \
    php81-pecl-memcache \
    php81-pdo \
    php81-pdo_mysql \
    memcached



RUN mkdir /etc/nginx/conf.d
COPY ./config/nginx.conf /etc/nginx/conf.d/nginx.conf

WORKDIR /var/www
COPY . ./


RUN echo "[program:memcached]" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "command=memcached -u memcached" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "stdout_logfile=/dev/stdout" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "stdout_logfile_maxbytes=0" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "stderr_logfile=/dev/stderr" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "stderr_logfile_maxbytes=0" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "autorestart=false" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "startretries=0" >> /etc/supervisor/conf.d/supervisord.conf

#COPY ./config/nginx.conf /etc/nginx/conf.d/default.conf

USER nobody
