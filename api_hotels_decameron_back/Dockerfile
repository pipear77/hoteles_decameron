# Usa una imagen base oficial de PHP con Apache
FROM php:8.2-apache

# Habilita la reescritura de URL de Apache
RUN a2enmod rewrite

# Instala las dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos de la aplicación
COPY . .

# Expone el puerto (opcional si usas Apache)
EXPOSE 80

# Comando para iniciar la aplicación (Apache lo hace automáticamente, pero es buena práctica)
CMD ["apache2-foreground"]
