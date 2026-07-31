# -----------------------------
# Base PHP image
# -----------------------------
FROM php:8.4-fpm


# -----------------------------
# Arguments defined in docker-compose.yml
# -----------------------------
ARG user
ARG uid

RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::Retries 3;" >> /etc/apt/apt.conf.d/99custom

# -----------------------------
# System dependencies & PHP extensions
# -----------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        zip \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libpq-dev \
        openssl \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libxpm-dev \
        libwebp-dev \
        libicu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip \
        sockets \
        intl \
    && pecl install redis xdebug \
    && docker-php-ext-enable redis xdebug \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

# -----------------------------
# Image processing
# -----------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
        libavif-dev \
        imagemagick \
        libmagickwand-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
        --with-avif \
    && docker-php-ext-install gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

# -----------------------------
# OpenSSL tweaks (optional)
# -----------------------------
RUN sed -i "s/^\(MinProtocol[ ]*=\).*/\1TLSv1.0/g" /etc/ssl/openssl.cnf \
    && sed -i "s/^\(CipherString[ ]*=\).*/\1DEFAULT@SECLEVEL=1/g" /etc/ssl/openssl.cnf

# -----------------------------
# Composer
# -----------------------------
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# -----------------------------
# Custom PHP ini (optional)
# -----------------------------
ADD ./custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

# -----------------------------
# Xdebug configuration
# -----------------------------
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# -----------------------------
# Install Node.js & npm
# -----------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# -----------------------------
# System user
# -----------------------------
RUN useradd -m -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user /var/www

# -----------------------------
# Working directory
# -----------------------------
WORKDIR /var/www
USER $user

# -----------------------------
# Entrypoint for artisan / composer
# -----------------------------
CMD ["php-fpm"]
