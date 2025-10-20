FROM php:5.6-cli

LABEL maintainer="php-wtforms-dev"
LABEL description="Development environment for php-wtforms library"

ENV DEBIAN_FRONTEND=noninteractive

# Jessie is no longer active so we need to update apt to point to the archive
RUN echo "deb http://archive.debian.org/debian/ jessie main contrib non-free" > /etc/apt/sources.list && \
    echo "deb http://archive.debian.org/debian-security/ jessie/updates main contrib non-free" >> /etc/apt/sources.list && \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/90ignore-release-date

# Install system dependencies
RUN apt-get update && apt-get install -y --force-yes \
    git \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions needed for the library
RUN docker-php-ext-install zip

# Install Composer
COPY --from=composer:1.10 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better layer caching
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Set proper permissions
RUN chown -R www-data:www-data /app

# Switch to non-root user
USER www-data

# Default command runs tests
CMD ["./vendor/bin/phpunit"]
