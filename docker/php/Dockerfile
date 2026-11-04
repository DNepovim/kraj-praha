FROM php:8.5-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
		libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
		libzip-dev libicu-dev libxml2-dev libxslt1-dev libmagickwand-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install -j"$(nproc)" bcmath exif gd intl mysqli pdo_mysql soap xsl zip \
	&& (pecl install imagick && docker-php-ext-enable imagick || true) \
	&& rm -rf /var/lib/apt/lists/*
