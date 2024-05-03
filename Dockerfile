FROM trafex/php-nginx:3.5.0

USER root
RUN apk add php83-pdo php83-pdo_mysql

USER nobody
WORKDIR /var/www/html
COPY  --chown=nobody *.php .
COPY  --chown=nobody Controller/ ./Controller
COPY  --chown=nobody Model/ ./Model
COPY  --chown=nobody Slim ./Slim
