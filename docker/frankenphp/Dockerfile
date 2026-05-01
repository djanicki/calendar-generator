FROM dunglas/frankenphp:1-php8.4-bookworm

RUN apt-get update \
    && apt-get install --yes --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    intl \
    opcache \
    pdo_mysql

COPY --from=composer/composer:2-bin /composer /usr/bin/composer

WORKDIR /app

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV APP_SECRET=change-me-in-production
ENV DEFAULT_URI=http://localhost
ENV DATABASE_URL=mysql://app:app@database:3306/app?serverVersion=8.4.0&charset=utf8mb4
ENV MESSENGER_TRANSPORT_DSN=sync://
ENV MAILER_DSN=null://null
ENV SERVER_NAME=:80

COPY composer.json composer.lock symfony.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .
COPY docker/frankenphp/Caddyfile /etc/frankenphp/Caddyfile

RUN mkdir -p var/cache var/log \
    && composer run-script post-install-cmd \
    && php bin/console cache:warmup

EXPOSE 80
