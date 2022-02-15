FROM nginx:1.21.6
WORKDIR /app
COPY . ./
COPY ./config/nginx.conf /etc/nginx/conf.d/default.conf
