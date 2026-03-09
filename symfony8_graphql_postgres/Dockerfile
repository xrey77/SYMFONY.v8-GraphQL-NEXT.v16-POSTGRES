FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache libpq-dev bash curl openssl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Install Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/supercar
COPY . .

RUN composer install --no-scripts --no-interaction

# Generate self-signed certificate
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/server.key \
    -out /etc/ssl/certs/server.crt \
    -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost"

EXPOSE 8000 5134

# Bind to 0.0.0.0 to allow access from the host machine
CMD ["symfony", "server:start", "--port=8000", "--address=0.0.0.0"]


# FROM php:8.4-fpm-alpine

# RUN apk add --no-cache libpq-dev

# RUN docker-php-ext-install pdo pdo_pgsql

# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# WORKDIR /var/www/supercar

# COPY . .

# RUN composer install --no-scripts --no-interaction

# # Generate a self-signed certificate for local development
# RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
#     -keyout /etc/ssl/private/server.key \
#     -out /etc/ssl/certs/server.crt \
#     -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost"

# EXPOSE 8000 5134
# # CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
# CMD ["symfony", "server:start", "--port=8000", "--address=0.0.0.0"]





# FROM php:8.4-fpm-alpine

# RUN apk add --no-cache \
#     postgresql-dev \
#     libpq

# RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#     && docker-php-ext-install pdo pdo_pgsql

# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# # RUN apk add --no-cache bash curl
# # RUN curl -1sLf 'https://dl.cloudsmith.io' | bash \
# #     && apk add symfony-cli
# WORKDIR /var/www/html/supercar
# COPY . .

# RUN chown -R www-data:www-data /var/www/html/supercar
# RUN chown -R www-data:www-data /var/www/html/supercar/public/index.php

# RUN composer install --no-scripts --no-interaction
# EXPOSE 8000
# # CMD ["symfony", "server:start", "--port=8000", "--no-tls"]
# CMD ["symfony", "server:start", "--port=8000", "--no-tls", "--address=0.0.0.0"]

# EXPOSE 9000
# CMD ["php-fpm"]


# FROM php:8.4-fpm-alpine
# RUN apk add --no-cache libpq-dev
# RUN docker-php-ext-install pdo pdo_pgsql
# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
# WORKDIR /var/www/supercar
# COPY . .
# RUN composer install --no-scripts --no-interaction
# EXPOSE 9000
# CMD ["php-fpm"]




