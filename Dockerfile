# Menggunakan PHP 8.3 FPM sebagai base image
FROM php:8.3-fpm

# Install dependencies sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Bersihkan cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer resmi dari image composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Salin file composer terlebih dahulu untuk optimalisasi cache Docker
COPY composer.json composer.lock ./

# Jalankan composer install otomatis saat proses build
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Salin seluruh sisa file proyek ke dalam container
COPY . .

# Berikan izin akses untuk storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]