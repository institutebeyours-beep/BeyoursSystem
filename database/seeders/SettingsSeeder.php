<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================== //
        // 1. APARIENCIA Y MARCA (públicas)           //
        // ========================================== //
        Setting::setValue('app_name', 'Beyours', 'string', 'appearance', 'Nombre de la aplicación', 'Nombre que aparece en el sistema', true);
        Setting::setValue('sidebar_color', '#1a202c', 'string', 'appearance', 'Color del sidebar', 'Color de fondo del menú lateral', true);
        Setting::setValue('sidebar_text_color', '#ffffff', 'string', 'appearance', 'Color del texto del sidebar', 'Color del texto en el menú lateral', true);
        Setting::setValue('sidebar_image', null, 'image', 'appearance', 'Imagen del sidebar', 'Imagen global en el menú lateral', true);
        Setting::setValue('default_theme', 'light', 'string', 'appearance', 'Tema por defecto', 'Tema para nuevos usuarios', true);
        Setting::setValue('primary_color', '#6366f1', 'string', 'appearance', 'Color primario', 'Color principal de la interfaz', true);
        Setting::setValue('logo', null, 'image', 'appearance', 'Logo', 'Logo de la empresa en el sidebar', true);
        Setting::setValue('favicon', null, 'image', 'appearance', 'Favicon', 'Icono de la pestaña del navegador', true);
        Setting::setValue('login_background', null, 'image', 'appearance', 'Fondo de login', 'Imagen de fondo de la página de login', true);

        // ========================================== //
        // 2. SEGURIDAD (solo admin)                  //
        // ========================================== //
        Setting::setValue('2fa_required', 1, 'boolean', 'security', '2FA obligatorio', 'Forzar autenticación de dos factores para todos', 1);
        Setting::setValue('session_timeout', 120, 'number', 'security', 'Expiración de sesión', 'Minutos de inactividad antes de cerrar sesión', false);
        Setting::setValue('max_login_attempts', 5, 'number', 'security', 'Intentos de login', 'Número de intentos antes de bloquear', false);
        Setting::setValue('login_block_time', 15, 'number', 'security', 'Tiempo de bloqueo', 'Minutos bloqueado por intentos fallidos', false);
        Setting::setValue('password_recovery', true, 'boolean', 'security', 'Recuperación de contraseña', 'Habilitar recuperación de contraseña', false);
        Setting::setValue('email_verification', true, 'boolean', 'security', 'Verificación de email', 'Obligatoria al registrarse', false);

        // ========================================== //
        // 3. REGISTRO Y USUARIOS (solo admin)        //
        // ========================================== //
        Setting::setValue('open_registration', true, 'boolean', 'registration', 'Registro abierto', 'Permitir registro de nuevos usuarios', false);
        Setting::setValue('default_role', 'user', 'string', 'registration', 'Rol por defecto', 'Rol asignado al registrarse', false);
        Setting::setValue('password_policy', 'min:8,special:true,numbers:true', 'string', 'registration', 'Política de contraseñas', 'Requisitos de contraseña', false);
        Setting::setValue('max_users', 1000, 'number', 'registration', 'Máximo de usuarios', 'Límite de usuarios en el sistema', false);
        Setting::setValue('users_per_page', 15, 'number', 'registration', 'Usuarios por página', 'Paginación en listados', false);

        // ========================================== //
        // 4. NOTIFICACIONES (solo admin)             //
        // ========================================== //
        Setting::setValue('email_notifications', true, 'boolean', 'notifications', 'Notificaciones por email', 'Habilitar/deshabilitar emails', false);
        Setting::setValue('system_email', 'noreply@beyours.com', 'string', 'notifications', 'Email de sistema', 'Email desde donde se envían', false);
        Setting::setValue('realtime_notifications', false, 'boolean', 'notifications', 'Notificaciones en tiempo real', 'WebSockets', false);
        Setting::setValue('password_reminder_days', 90, 'number', 'notifications', 'Recordatorio de contraseña', 'Días para recordar cambiar contraseña', false);

        // ========================================== //
        // 5. MANTENIMIENTO (solo super-admin)        //
        // ========================================== //
        Setting::setValue('maintenance_mode', false, 'boolean', 'maintenance', 'Modo mantenimiento', 'Poner el sistema en modo mantenimiento', false);
        Setting::setValue('maintenance_message', 'Sistema en mantenimiento. Por favor, vuelve más tarde.', 'string', 'maintenance', 'Mensaje de mantenimiento', 'Mensaje mostrado en modo mantenimiento', false);
        Setting::setValue('maintenance_allow_ips', '', 'string', 'maintenance', 'IPs permitidas', 'IPs que pueden acceder en modo mantenimiento (separadas por coma)', false);
        Setting::setValue('maintenance_block_ips', '', 'string', 'maintenance', 'IPs bloqueadas', 'IPs bloqueadas incluso en modo normal (separadas por coma)', false);
        Setting::setValue('auto_backup', false, 'boolean', 'maintenance', 'Backup automático', 'Realizar backup automático diario', false);
        Setting::setValue('backup_frequency', 'daily', 'string', 'maintenance', 'Frecuencia de backup', 'daily, weekly, monthly', false);
        Setting::setValue('backup_keep_days', 30, 'number', 'maintenance', 'Días a mantener', 'Días que se conservan los backups', false);
        Setting::setValue('log_retention_days', 30, 'number', 'maintenance', 'Retención de logs', 'Días que se conservan los logs', false);
        Setting::setValue('auto_clean_logs', true, 'boolean', 'maintenance', 'Limpiar logs automático', 'Eliminar logs antiguos automáticamente', false);

        $this->command->info('✅ Configuraciones globales creadas');
        $this->command->info('📋 Total: 32 configuraciones');
    }
}