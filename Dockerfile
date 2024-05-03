FROM trafex/php-nginx:3.5.0

USER root
RUN apk add php83-pdo php83-pdo_mysql

WORKDIR /var/www/html

COPY  --chown=nobody *.php .
COPY  --chown=nobody Controller/ ./Controller
COPY  --chown=nobody Model/ ./Model
COPY  --chown=nobody Slim ./Slim

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping || exit 1