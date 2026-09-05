# 🚀 Beyours - Sistema de Administración

## 📋 Descripción
Sistema de administración completo construido con Laravel 12 y Vue 3.

## ✨ Características Principales
- ✅ **Autenticación Segura**: Login, Register, 2FA, Verificación Email
- ✅ **Roles y Permisos**: Control de acceso basado en roles (Super-Admin, Admin, Manager, User)
- ✅ **Panel de Administración**: Gestión de usuarios, roles y configuraciones
- ✅ **Modo Mantenimiento**: Activar/desactivar con control de IPs
- ✅ **Sistema de Backups**: Crear, descargar y eliminar backups
- ✅ **Auditoría**: Registro de todas las acciones del sistema
- ✅ **Notificaciones**: Alertas por email al Super-Admin
- ✅ **Dashboard**: Estadísticas del sistema en tiempo real

## 🛠️ Tecnologías
- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: Vue 3, Vite, Tailwind CSS
- **Base de Datos**: MySQL
- **Seguridad**: Sanctum, Spatie Permission, 2FA

## 📦 Requisitos
- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Node.js >= 18

## 🚀 Instalación Rápida
```bash
# 1. Clonar el repositorio
git clone [url-del-repositorio]

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar .env
cp .env.example .env

# 4. Generar key
php artisan key:generate

# 5. Ejecutar migraciones
php artisan migrate --seed

# 6. Compilar assets
npm run build

# 7. Iniciar servidor
php artisan serve