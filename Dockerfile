FROM php:8.2-fpm

# 安裝系統依賴
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm

# 清理 apt 快取
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 安裝 PHP 擴展
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 獲取最新的 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 設定工作目錄
WORKDIR /var/www

# 複製 composer.json 和 composer.lock
COPY composer*.json ./

# 設定權限
RUN chown -R www-data:www-data /var/www

# 複製專案文件
COPY . /var/www

# 安裝 PHP 依賴
RUN composer install

# 設定正確的權限
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 暴露端口
EXPOSE 8000

# 啟動 PHP 服務器
CMD php artisan serve --host=0.0.0.0 --port=8000 