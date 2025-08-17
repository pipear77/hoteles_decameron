Gestión de Hoteles Decameron
Este proyecto es un sistema de gestión de hoteles, desarrollado con un backend en Laravel 12 y un frontend en React con Vite.

Funcionalidades Clave
Autenticación de Usuarios: Sistema de inicio de sesión seguro para proteger la aplicación.

Gestión de Hoteles: CRUD (Crear, Leer, Actualizar, Eliminar) completo para la administración de hoteles.

Gestión de Habitaciones: Permite configurar los tipos de habitaciones y sus acomodaciones por hotel.

Requisitos del Sistema
Para ejecutar este proyecto, necesitas tener instalados los siguientes componentes:

PHP: Versión 8.2 o superior.

Composer: Manejador de dependencias de PHP.

Node.js: Versión 18 o superior.

npm o Yarn: Manejador de paquetes de JavaScript.

Docker (opcional): Para un entorno de desarrollo más sencillo.

Guía de Despliegue Local
Sigue estos pasos para poner la aplicación en marcha en tu máquina local.

1. Configuración del Backend (Laravel)
Navega al directorio del backend:

Bash

cd api_hotels_decameron_back
Instala las dependencias de Composer:

Bash

composer install
Copia el archivo de variables de entorno de ejemplo y genera la clave de aplicación:

Bash

cp .env.example .env
php artisan key:generate
Configura tus credenciales de base de datos en el archivo .env. Este proyecto usa PostgreSQL por defecto.

Bash

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=decameron_hotels
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
Ejecuta las migraciones de la base de datos y los seeders para llenar la base de datos con datos de prueba:

Bash

php artisan migrate:fresh --seed
Nota: El comando migrate:fresh borra todas las tablas de la base de datos y las recrea. El flag --seed ejecuta los seeders de forma automática.

Inicia el servidor de desarrollo de Laravel:

Bash

php artisan serve
2. Configuración del Frontend (React/Vite)
Abre una nueva terminal y navega al directorio del frontend:

Bash

cd api_hotels_decameron_front
Instala las dependencias de npm:

Bash

npm install
Inicia el servidor de desarrollo de Vite:

Bash

npm run dev
Tu frontend estará disponible en http://localhost:5173 y se conectará al backend que se ejecuta en http://localhost:8000.

Guía de Despliegue en la Nube (Producción)
Esta guía asume que utilizarás Vercel para el frontend y Render para el backend, siguiendo las prácticas discutidas.

1. Despliegue del Backend en Render
Conecta tu repositorio de GitHub hoteles_decameron a Render.

Configura el servicio web con los siguientes parámetros:

Root Directory: api_hotels_decameron_back

Build Command: composer install && php artisan migrate --force && php artisan db:seed

Start Command: php artisan serve --host 0.0.0.0 --port $PORT

Variables de Entorno: Configura las variables de base de datos y seguridad (APP_KEY, JWT_SECRET, etc.) con los valores de producción.

Importante: Después del primer despliegue exitoso, edita el Build Command para que quede solo como composer install. Esto evita que las migraciones y seeders se ejecuten en despliegues futuros, lo que podría causar pérdida de datos.

2. Despliegue del Frontend en Vercel
Conecta tu repositorio de GitHub hoteles_decameron a Vercel.

Configura el proyecto de la siguiente manera:

Root Directory: api_hotels_decameron_front

Environment Variables: Agrega una variable de entorno VITE_REACT_APP_API_URL con el valor de la URL pública de tu backend en Render.

Vercel compilará y desplegará tu frontend automáticamente.

Créditos
Desarrollado por Juan Felipe Alvarez Ruales
