📖 MANUAL DE USUARIO - BEYOURS
📋 Guía Completa para Administradores y Super-Admin
📌 1. INTRODUCCIÓN
1.1 ¿Qué es Beyours?
Beyours es un sistema de administración completo diseñado para gestionar usuarios, roles, configuraciones y mantenimiento del sistema.

1.2 Roles y Permisos
Rol	Descripción	Acceso
👑 Super-Admin	Control total del sistema	Todos los módulos
👨‍💼 Admin	Gestión de usuarios y configuraciones	Usuarios, Roles, Settings
📋 Manager	Visualización de datos	Usuarios (solo lectura)
👤 User	Acceso básico	Perfil y 2FA
🏠 2. DASHBOARD
2.1 Acceso
Inicia sesión con tus credenciales

Serás redirigido automáticamente al Dashboard

O ve a: http://tudominio.com/dashboard

2.2 Estadísticas del Sistema
📊 Tarjetas Rápidas
Usuarios: Total de usuarios registrados

Backups: Número de backups creados

Tamaño Backups: Espacio ocupado por backups

Logs: Tamaño de los archivos de log

📈 Secciones Detalladas
Usuarios: Activos, inactivos, nuevos hoy, última semana

Backups: Total, tamaño, últimas 24h, última semana

Sistema: PHP, Laravel, Base de Datos, espacio en disco

Seguridad: 2FA, IPs bloqueadas, IPs permitidas

2.3 Actualización
Haz clic en "Actualizar" para recargar estadísticas en tiempo real

🔐 3. AUTENTICACIÓN Y SEGURIDAD
3.1 Inicio de Sesión
Ve a http://tudominio.com/login

Ingresa tu email y contraseña

Si tienes 2FA activado, ingresa el código de 6 dígitos

3.2 Cerrar Sesión
Haz clic en tu nombre en la parte inferior del sidebar

Selecciona "Cerrar Sesión"

3.3 Recuperación de Contraseña
Ve a http://tudominio.com/password/forgot

Ingresa tu email

Revisa tu correo para el enlace de recuperación

3.4 2FA (Autenticación de Dos Factores)
Configurar 2FA
Ve a tu Perfil → Configurar 2FA

Escanea el código QR con Google Authenticator o Authy

Ingresa el código de verificación de 6 dígitos

¡Listo! Tu cuenta está protegida

Iniciar Sesión con 2FA
Ingresa email y contraseña

Abre tu aplicación de autenticación

Ingresa el código de 6 dígitos

🛠️ 4. MÓDULO DE MANTENIMIENTO
4.1 Acceso
Ve a: http://tudominio.com/admin/maintenance

Solo Super-Admin tiene acceso

4.2 Modo Mantenimiento
Activar
Ve al panel de mantenimiento

Haz clic en el toggle (botón ON/OFF)

El sistema mostrará: "🔒 Modo mantenimiento activado"

Desactivar
Haz clic en el toggle nuevamente

El sistema mostrará: "🔓 Modo mantenimiento desactivado"

Mensaje Personalizado
Escribe un mensaje en el campo "Mensaje de mantenimiento"

Presiona Enter o haz clic fuera del campo

El mensaje se guarda automáticamente

4.3 Gestión de IPs
IPs Permitidas (Whitelist)
¿Para qué sirven? IPs que pueden acceder durante el mantenimiento

Cómo agregar: Escribe la IP y presiona Enter

Cómo eliminar: Haz clic en ✕ sobre la IP

Borrar todas: Haz clic en 🗑️

IPs Bloqueadas (Blacklist)
¿Para qué sirven? IPs bloqueadas permanentemente

Cómo agregar: Escribe la IP y presiona Enter

Cómo eliminar: Haz clic en ✕ sobre la IP

Borrar todas: Haz clic en 🗑️

💾 5. BACKUPS
5.1 Acceso
Ve a: http://tudominio.com/admin/maintenance

Sección "💾 Backups"

5.2 Crear Backup
Haz clic en "Crear Backup"

Confirma la acción

Espera unos segundos mientras se genera

El backup aparecerá en la lista

5.3 Lista de Backups
Columna	Descripción
Archivo	Nombre del archivo (.zip)
Tipo	full (completo), database, files
Tamaño	Tamaño del archivo
Creado	Fecha y hora de creación
Acciones	⬇️ Descargar / 🗑️ Eliminar
5.4 Descargar Backup
Haz clic en el botón ⬇️

El archivo se descargará automáticamente

5.5 Eliminar Backup
Haz clic en el botón 🗑️

Confirma la acción

El backup se eliminará permanentemente

📋 6. AUDITORÍA
6.1 Acceso
Ve a: http://tudominio.com/admin/audit

Solo Super-Admin tiene acceso

6.2 Visualización
La auditoría muestra todas las acciones realizadas en el sistema:

Columna	Descripción
Usuario	Quién realizó la acción
Acción	Qué se hizo
Módulo	Módulo afectado
IP	Dirección IP del usuario
Fecha	Cuándo ocurrió
6.3 Filtros
Buscar: Por acción o descripción

Módulo: maintenance, backup, security

Acción: activó, desactivó, creó, eliminó, limpió

6.4 ¿Qué se Registra?
✅ Activación/Desactivación de mantenimiento

✅ Creación/Eliminación de backups

✅ Limpieza de logs y caché

✅ Cambios en IPs

✅ Intentos de acceso bloqueados

⚙️ 7. CONFIGURACIONES GLOBALES
7.1 Acceso
Ve a: http://tudominio.com/admin/settings/global

Super-Admin y Admin tienen acceso

7.2 Apariencia
Configuración	Descripción
Favicon	Icono de la pestaña del navegador
Logo	Logo del sistema en el sidebar
Color del sidebar	Color de fondo del menú lateral
Color del texto	Color del texto en el menú
Tema por defecto	light / dark
7.3 Seguridad
Configuración	Descripción
2FA obligatorio	Forzar autenticación de dos factores
Expiración de sesión	Minutos de inactividad antes de cerrar sesión
Intentos de login	Intentos fallidos antes de bloquear
Tiempo de bloqueo	Minutos bloqueado por intentos fallidos
7.4 Registro
Configuración	Descripción
Registro abierto	Permitir registro de nuevos usuarios
Rol por defecto	Rol asignado al registrarse
Política de contraseñas	Requisitos (mínimo, especiales, números)
Máximo de usuarios	Límite de usuarios en el sistema
7.5 Notificaciones
Configuración	Descripción
Notificaciones por email	Habilitar/deshabilitar emails
Email de sistema	Email desde donde se envían
📧 8. NOTIFICACIONES
8.1 ¿Cuándo Recibes Notificaciones?
Evento	Tipo	Destinatario
Activación/Desactivación mantenimiento	📧	Super-Admin
Creación de backup	📧	Super-Admin
Eliminación de backup	📧	Super-Admin
Limpieza de logs	📧	Super-Admin
Limpieza de caché	📧	Super-Admin
IP bloqueada intenta acceder	📧	Super-Admin
8.2 Configurar Email
Edita el archivo .env

Configura las variables de mail:

env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@beyours.com
MAIL_FROM_NAME="Beyours"
👤 9. PERFIL DE USUARIO
9.1 Acceso
Haz clic en tu nombre en el sidebar

Selecciona "Mi Perfil"

9.2 Funcionalidades
✅ Ver información personal

✅ Editar nombre y email

✅ Cambiar contraseña

✅ Subir foto de perfil

✅ Configurar 2FA

❓ 10. PREGUNTAS FRECUENTES (FAQ)
10.1 ¿Cómo recupero mi contraseña?
Ve a http://tudominio.com/password/forgot

Ingresa tu email

Revisa tu correo para el enlace de recuperación

Crea una nueva contraseña

10.2 ¿Qué hago si mi IP es bloqueada?
Contacta al Super-Admin para que te desbloquee o agrega tu IP a la lista de permitidas.

10.3 ¿Cómo sé si el sistema está en mantenimiento?
Verás el mensaje de mantenimiento al intentar acceder

Los usuarios normales no podrán acceder

Super-Admin tendrá acceso normal

10.4 ¿Cada cuánto se crean backups automáticos?
Actualmente los backups son manuales. Se puede configurar automático programando un cron job.

10.5 ¿Puedo cambiar mi rol?
No, los roles solo pueden ser asignados por Super-Admin.

10.6 ¿Cómo configuro 2FA?
Ve a tu perfil

Haz clic en "Configurar 2FA"

Escanea el código QR con Google Authenticator

Ingresa el código de verificación

📞 11. SOPORTE
11.1 Contacto
Email: soporte@beyours.com

Teléfono: +123 456 7890

Horario: Lunes a Viernes, 9:00 - 18:00

11.2 Reportar un Problema
Ve a /admin/audit para ver el historial

Revisa los logs del sistema en storage/logs/laravel.log

Contacta al equipo de soporte con la información

📋 12. GLOSARIO DE TÉRMINOS
Término	Definición
2FA	Autenticación de Dos Factores - Protección extra usando código de 6 dígitos
Backup	Copia de seguridad de la base de datos y archivos
Blacklist	Lista de IPs bloqueadas
Caché	Datos temporales para mejorar el rendimiento
Dashboard	Panel de control con estadísticas del sistema
Logs	Archivos que registran las actividades del sistema
Middleware	Capa de seguridad que procesa las peticiones
Roles	Conjunto de permisos asignados a usuarios
Sanctum	Sistema de autenticación de Laravel
Whitelist	Lista de IPs permitidas
📝 13. NOTAS FINALES
🔒 Seguridad
Mantén tu contraseña segura y cámbiala regularmente

Activa 2FA para mayor protección

No compartas tus credenciales

🔄 Actualizaciones
El sistema se actualiza periódicamente

Revisa el changelog para conocer las novedades

💡 Sugerencias
Realiza backups regularmente

Monitorea la auditoría para detectar actividades sospechosas

Mantén el sistema actualizado

📊 14. REFERENCIAS RÁPIDAS
Accesos Directos
Módulo	URL
Dashboard	/dashboard
Mantenimiento	/admin/maintenance
Auditoría	/admin/audit
Configuraciones	/admin/settings/global
Usuarios	/admin/users
Roles	/admin/roles
Perfil	/profile
Comandos Útiles
bash
# Limpiar caché
php artisan optimize:clear

# Crear backup
php artisan backup:create

# Ver logs
tail -f storage/logs/laravel.log

# Ver estado del sistema
php artisan about
📖 Manual creado para usuarios de Beyours
Versión: 1.0
Última actualización: Agosto 2026