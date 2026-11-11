# ContactoApi - RESTful API

API RESTful construida con **Laravel 13** para la gestión segura de contactos personales. Implementa autenticación basada en tokens (**Laravel Sanctum**), aislamiento de datos por usuario, borrado lógico (*Soft Deletes*), manejo centralizado de respuestas de error JSON y pruebas automatizadas en **Pest / PHPUnit**.

---

## 🚀 Requisitos Previos

- PHP >= 8.2
- Composer
- MySQL >= 8.0

---

## 🛠️ Instalación y Configuración

1. **Clonar el repositorio e ingresar al proyecto:**
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd contacto-api
   
   composer install

   cp .env.example .env


   APP_NAME=ContactoApi
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contacto_api_db
DB_USERNAME=root
DB_PASSWORD=


php artisan key:generate

php artisan migrate:fresh --seed

php artisan test

vendor/bin/pint


