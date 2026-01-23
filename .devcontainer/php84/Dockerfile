FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN --mount=type=bind,from=mlocati/php-extension-installer:latest,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
    install-php-extensions \
    mbstring \
    xml \
    curl \
    zip \
    pdo_sqlite \
    xdebug

WORKDIR /workspace

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
