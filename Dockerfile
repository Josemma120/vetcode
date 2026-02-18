FROM php:8.1-apache

# Habilitar mod_rewrite para URLs (si fuera necesario)
RUN a2enmod rewrite

# Instalar extensiones necesarias para MySQL (mysqli)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar el código al contenedor
COPY . /var/www/html/

# Dar permisos a la carpeta de imágenes (para subida de archivos)
RUN chown -R www-data:www-data /var/www/html/Vista/imagenes \
    && chmod -R 755 /var/www/html/Vista/imagenes

# Configuraciones opcionales de PHP (Production)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Exponer puerto 80
EXPOSE 80
