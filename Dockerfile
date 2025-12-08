FROM php:8.2-apache

# Instalăm extensiile pentru MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Activăm mod_rewrite pentru Apache (util pentru framework-uri gen Laravel/Wordpress)
RUN a2enmod rewrite
