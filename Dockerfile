FROM php:8.2-apache

WORKDIR /var/www/html

# ============================
# Dependências do sistema
# ============================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# ============================
# Extensões PHP
# ============================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        gd

# ============================
# Apache: habilitar mod_rewrite
# ============================
RUN a2enmod rewrite

# ============================
# Ajustar DocumentRoot para /public
# ============================
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# ============================
# Composer (oficial)
# ============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================
# Copia o projeto
# ============================
COPY . .

# ============================
# Instala dependências PHP
# ============================
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# ============================
# Permissões (Laravel/Lumen)
# ============================
RUN mkdir -p \
    storage/logs \
    storage/framework/cache \
    storage/framework/views \
    && chown -R www-data:www-data storage \
    && chmod -R 775 storage storage/logs storage/framework/cache storage/framework/views storage/app

# ============================
# Expor porta
# ============================
EXPOSE 80

# ============================
# Start Apache
# ============================
CMD ["apache2-foreground"]
