# PHP CLI image aligned with docker-compose/app (same 8.4 + extensions as FPM).
# No FPM, Xdebug, Node, or Composer — workers use code from the mounted /var/www.

FROM php:8.4-cli-bookworm

ARG user
ARG uid

RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::Retries 3;" >> /etc/apt/apt.conf.d/99custom

RUN apt-get update && apt-get install -y --no-install-recommends \
        supervisor \
        curl \
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
    && pecl install redis \
    && docker-php-ext-enable redis \
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

RUN sed -i "s/^\(MinProtocol[ ]*=\).*/\1TLSv1.0/g" /etc/ssl/openssl.cnf \
    && sed -i "s/^\(CipherString[ ]*=\).*/\1DEFAULT@SECLEVEL=1/g" /etc/ssl/openssl.cnf

ADD ./custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

COPY supervisor.conf /etc/supervisor/supervisord.conf

RUN useradd -m -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /etc/supervisor/logs \
    && chown -R $user:$user /home/$user /var/www \
    && sed -i "s/{{RUN_AS_USER}}/$user/g" /etc/supervisor/supervisord.conf

WORKDIR /var/www

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
