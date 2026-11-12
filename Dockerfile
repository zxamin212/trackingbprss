# Menggunakan PHP 8.3 FPM sebagai base image
FROM php:8.3-fpm

# Install dependencies sistem, pkg-config, dan libzip-dev
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    pkg-config

# Bersihkan cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP (termasuk zip)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer resmi dari image composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www


# 1. Salin HANYA file konfigurasi composer terlebih dahulu
COPY composer.json composer.lock ./

# 2. Jalankan composer install tanpa skrip agar tidak mencari file artisan yang belum ada
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 3. Salin seluruh sisa file proyek ke dalam container (termasuk artisan)

# Jalankan composer install otomatis saat proses build
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Salin seluruh sisa file proyek ke dalam container
COPY . .

# 4. Jalankan script post-autoload-dump sekarang karena file artisan sudah tersedia
RUN composer run-script post-autoload-dump

# Berikan izin akses untuk storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
