# API REST de Gestión de Tareas

API REST desarrollada en Laravel para la gestión de tareas personales con autenticación JWT, integración con servicios externos y notificaciones por email.

## Características

- ✅ Autenticación JWT con registro, login y logout
- ✅ CRUD completo de tareas con paginación
- ✅ Integración con OpenWeatherMap API para información del clima
- ✅ Sistema de notificaciones por email con Mailgun
- ✅ Validaciones de negocio robustas
- ✅ Respuestas JSON estructuradas

## Requisitos del Sistema

- PHP 7.4 o superior
- PostgreSQL 12+
- Composer
- Laravel 10+

## Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/RikardoBonilla/IMEXHS_test.git
cd IMEXHS_test
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar variables de entorno
```bash
cp .env.example .env
```

Editar el archivo `.env` con la siguiente configuración:

```env
# Aplicación
APP_NAME="Task Management API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=task_management
DB_USERNAME=postgres
DB_PASSWORD=tu_password

# JWT Configuration
JWT_TTL=60
JWT_REFRESH_TTL=20160

# OpenWeatherMap API
OPENWEATHER_API_KEY=tu_api_key_aqui

# Mailgun Configuration
MAILGUN_DOMAIN=tu_dominio_mailgun
MAILGUN_SECRET=tu_secret_mailgun
MAIL_MAILER=mailgun
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Generar claves de aplicación
```bash
php artisan key:generate
php artisan jwt:secret
```

### 5. Ejecutar migraciones
```bash
php artisan migrate
```

### 6. Iniciar el servidor
```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000`

## Configuración de Servicios Externos

### OpenWeatherMap API
1. Registrarse en [OpenWeatherMap](https://openweathermap.org/api)
2. Obtener API Key gratuita
3. Configurar `OPENWEATHER_API_KEY` en el archivo `.env`

### Mailgun
1. Registrarse en [Mailgun](https://www.mailgun.com/)
2. Configurar dominio
3. Obtener las credenciales y configurar en `.env`:
   - `MAILGUN_DOMAIN`
   - `MAILGUN_SECRET`

## Endpoints de la API

### Autenticación

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/register` | Registro de nuevo usuario |
| POST | `/api/login` | Iniciar sesión |
| POST | `/api/logout` | Cerrar sesión |
| GET | `/api/profile` | Obtener perfil del usuario |

### Tareas (Requieren autenticación)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/tasks` | Listar tareas (paginado) |
| POST | `/api/tasks` | Crear nueva tarea |
| GET | `/api/tasks/{id}` | Obtener tarea específica |
| PUT | `/api/tasks/{id}` | Actualizar tarea |
| DELETE | `/api/tasks/{id}` | Eliminar tarea |
| GET | `/api/tasks/{id}/weather` | Obtener clima para tarea |
| POST | `/api/tasks/{id}/send-reminder` | Enviar recordatorio por email |

## Ejemplos de Uso

### Registro de Usuario
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ricardo Bonilla",
    "email": "ricardoandresbonilla@gmail.com",
    "password": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ricardoandresbonilla@gmail.com",
    "password": "password123"
  }'
```

### Crear Tarea
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "title": "Revisar propuesta comercial",
    "description": "Análisis detallado del documento recibido",
    "status": "pending",
    "due_date": "2025-08-15"
  }'
```

### Obtener Clima para Tarea
```bash
curl -X GET http://localhost:8000/api/tasks/1/weather \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Estructura de Respuestas

### Usuario
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "ricardoandresbonilla@gmail.com",
    "created_at": "2025-08-15T10:30:00Z"
  }
}
```

### Tarea
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Revisar propuesta comercial",
    "description": "Análisis detallado del documento recibido",
    "status": "pending",
    "due_date": "2024-02-15",
    "user_id": 1,
    "created_at": "2025-08-15T10:30:00Z",
    "updated_at": "2025-08-15T12:00:00Z"
  }
}
```

## Validaciones

### Usuarios
- Email único en la base de datos
- Contraseña mínimo 8 caracteres con letras y números
- Nombre obligatorio, máximo 100 caracteres

### Tareas
- Título obligatorio, máximo 200 caracteres
- Descripción opcional, máximo 1000 caracteres
- Fecha de vencimiento no puede ser anterior a hoy
- Status: `pending`, `in_progress`, `completed`
- Los usuarios solo pueden ver/modificar sus propias tareas

## Testing

```bash
php artisan test
```

## Despliegue en Producción

1. Configurar servidor web (Nginx/Apache)
2. Configurar base de datos PostgreSQL
3. Configurar variables de entorno de producción
4. Ejecutar migraciones: `php artisan migrate --force`
5. Optimizar aplicación: `php artisan optimize`
6. Configurar HTTPS y certificados SSL
7. Configurar trabajos de cola si es necesario

## Licencia

Este proyecto es desarrollado por Ricardo Andres Bonilla Prada.