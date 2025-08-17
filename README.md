# **Gestión de Hoteles Decameron**

Este proyecto es un sistema de gestión de hoteles, desarrollado con un backend en **Laravel 12** y un frontend en **React con Vite**.

---

### **Funcionalidades Clave**

* **Autenticación de Usuarios:** Sistema de inicio de sesión seguro.
* **Gestión de Hoteles:** CRUD (Crear, Leer, Actualizar, Eliminar) completo para la administración de hoteles.
* **Configuración de Habitaciones:** Permite establecer los tipos de habitaciones y sus acomodaciones por hotel.

---

### **Guía de Despliegue Local**

Sigue estos pasos para poner la aplicación en marcha en tu máquina local.

#### **1. Configuración del Backend (Laravel)**

1.  Navega al directorio del backend:
    ```bash
    cd api_hotels_decameron_back
    ```
2.  Instala las dependencias de Composer:
    ```bash
    composer install
    ```
3.  Copia el archivo de variables de entorno de ejemplo y genera la clave de aplicación:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4.  Configura tus credenciales de base de datos en el archivo `.env`. Este proyecto usa PostgreSQL por defecto.
    ```bash
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=decameron_hotels
    DB_USERNAME=postgres
    DB_PASSWORD=tu_contraseña
    ```
5.  Ejecuta las migraciones de la base de datos y los seeders para llenar la base de datos con datos de prueba:
    ```bash
    php artisan migrate:fresh --seed
    ```
    > **Nota:** El comando `migrate:fresh` borra todas las tablas de la base de datos y las recrea. El flag `--seed` ejecuta los seeders de forma automática.

6.  Inicia el servidor de desarrollo de Laravel:
    ```bash
    php artisan serve
    ```

7.  Inicio de sesión (credenciales):
    ```bash
    Usuario y/o correo:  gerente@example.com
    contraseña: password

#### **2. Configuración del Frontend (React/Vite)**

1.  Abre una nueva terminal y navega al directorio del frontend:
    ```bash
    cd api_hotels_decameron_front
    ```
2.  Instala las dependencias de npm:
    ```bash
    npm install
    ```
3.  Inicia el servidor de desarrollo de Vite:
    ```bash
    npm run dev
    ```

Tu frontend estará disponible en `http://localhost:5173` y se conectará al backend que se ejecuta en `http://localhost:8000`.

---

### **Guía de Despliegue en la Nube (Producción)**

Esta guía asume que utilizarás **Vercel** para el frontend y **Render** para el backend, siguiendo las prácticas discutidas.

#### **1. Despliegue del Backend en Render**

1.  Conecta tu repositorio de GitHub `hoteles_decameron` a Render.
2.  Configura el servicio web con los siguientes parámetros:
    * **Root Directory:** `api_hotels_decameron_back`
    * **Build Command:** `composer install && php artisan migrate --force && php artisan db:seed`
    * **Start Command:** `php artisan serve --host 0.0.0.0 --port $PORT`
    * **Variables de Entorno:** Configura las variables de base de datos y seguridad (`APP_KEY`, `JWT_SECRET`, etc.) con los valores de producción.
3.  **Importante:** Después del primer despliegue exitoso, edita el **Build Command** para que quede solo como **`composer install`**. Esto evita que las migraciones y seeders se ejecuten en despliegues futuros, lo que podría causar pérdida de datos.

#### **2. Despliegue del Frontend en Vercel**

1.  Conecta tu repositorio de GitHub `hoteles_decameron` a Vercel.
2.  Configura el proyecto de la siguiente manera:
    * **Root Directory:** `api_hotels_decameron_front`
    * **Environment Variables:** Agrega una variable de entorno `VITE_REACT_APP_API_URL` con el valor de la URL pública de tu backend en Render.
3.  Vercel compilará y desplegará tu frontend automáticamente.

---

### **Créditos**

Desarrollado por Juan Felipe Alvarez Ruales.
