FROM php:5.6-cli

# Debian Stretch is EOL - use archived repos
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's/security.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list

RUN apt-get update && apt-get install -y --allow-unauthenticated \
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
    xdebug-2.5.5

COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /workspace
