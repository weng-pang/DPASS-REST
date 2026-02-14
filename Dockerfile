FROM trafex/php-nginx:3.5.0

USER root
RUN apk add php83-pdo php83-pdo_mysql

USER nobody
WORKDIR /var/www/html

# Environment variables for database configuration
# These can be overridden at runtime using docker run -e or docker-compose
# DB_HOST: Database host address (default: 127.0.0.1)
# DB_NAME: Database name (default: dpass-lite)
# DB_USER: Database username (default: dpass-lite)
# DB_PASSWORD: Database password (default: dpass-lite)

COPY  --chown=nobody *.php .
COPY  --chown=nobody Controller/ ./Controller
COPY  --chown=nobody Model/ ./Model
COPY  --chown=nobody Slim ./Slim
COPY  --chown=nobody versions ./versions

EXPOSE 8080
