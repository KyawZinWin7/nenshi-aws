# FROM php:8.2-fpm-alpine


# # Install system dependencies
# RUN apk add --no-cache \
#     libzip-dev \
#     zip \
#     unzip \
#     nodejs \
#     npm \
#     && docker-php-ext-install zip pdo_mysql pcntl bcmath

# # Install Composer

# ADD docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# RUN mkdir -p /var/www/html && chown -R www-data:www-data /var/www/html
# WORKDIR /var/www/html

# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer



FROM php:8.2-fpm-alpine

# Install system dependencies + gd build deps
RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        gd \
        zip \
        pdo_mysql \
        pcntl \
        bcmath \
    && apk del $PHPIZE_DEPS

# Add custom php-fpm config
ADD docker/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer
   