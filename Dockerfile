# Dockerfile para Laravel - Produção
FROM php:8.2-fpm-alpine

# Argumentos de build
ARG USER_ID=1000
ARG GROUP_ID=1000

# Instalar dependências do sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    mysql-client \
    nodejs \
    npm \
    supervisor \
    tzdata

# Configurar e instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Instalar Redis extension (opcional, mas recomendado)
RUN apk add --no-cache autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf g++ make

# Configurar OPcache para produção
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Configurações PHP para produção
RUN echo "upload_max_filesize=50M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/memory.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário para a aplicação
RUN addgroup -g ${GROUP_ID} laravel \
    && adduser -u ${USER_ID} -G laravel -s /bin/sh -D laravel

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do composer primeiro (para cache de layers)
COPY composer.json composer.lock ./

# Instalar dependências PHP (sem dev)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copiar arquivos do npm
COPY package.json package-lock.json* ./

# Instalar TODAS as dependências Node (incluindo devDependencies para build)
RUN npm ci || npm install

# Copiar resto da aplicação
COPY . .

# Gerar autoload otimizado
RUN composer dump-autoload --optimize

# Build dos assets com Vite
RUN npm run build

# Criar diretórios necessários e ajustar permissões
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R laravel:laravel /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copiar configuração do supervisor
COPY docker/supervisord.conf /etc/supervisord.conf

# Expor porta do PHP-FPM
EXPOSE 9000

# Usuário padrão
USER laravel

# Script de entrada
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
USER root
RUN chmod +x /usr/local/bin/entrypoint.sh
USER laravel

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
