# 🛠️ Guía de Administración - Beyours

## 👑 Super-Admin

### Gestión de Usuarios
1. Ve a `/admin/users`
2. **Crear**: Haz clic en "Crear Usuario"
3. **Editar**: Haz clic en ✏️ sobre el usuario
4. **Eliminar**: Haz clic en 🗑️ sobre el usuario
5. **Activar/Desactivar**: Toggle de estado

### Gestión de Roles
1. Ve a `/admin/roles`
2. **Crear**: Haz clic en "Crear Rol"
3. **Editar**: Haz clic en ✏️ sobre el rol
4. **Eliminar**: Haz clic en 🗑️ sobre el rol
5. **Permisos**: Asignar permisos al rol

### Configuraciones Globales
1. Ve a `/admin/settings/global`
2. Modifica las configuraciones según necesites
3. Guarda los cambios

---

## 🔧 Mantenimiento del Sistema

### Modo Mantenimiento
1. Ve a `/admin/maintenance`
2. Activa el toggle
3. Configura el mensaje
4. Gestiona IPs permitidas/bloqueadas

### Backups
1. Ve a `/admin/maintenance`
2. Crea un backup
3. Lista, descarga o elimina backups

### Logs y Caché
1. Ve a `/admin/maintenance`
2. Limpia la caché
3. Limpia los logs

### Auditoría
1. Ve a `/admin/audit`
2. Revisa las actividades
3. Filtra por usuario, módulo o acción

---

## 📊 Monitoreo del Sistema

### Dashboard
1. Ve a `/dashboard`
2. Revisa estadísticas:
   - Usuarios
   - Backups
   - Sistema
   - Seguridad

### Alertas
- Recibirás notificaciones por email cuando:
  - Se active/desactive mantenimiento
  - Se cree o elimine un backup
  - Se limpien logs o caché

---

## 🔐 Seguridad

### 2FA
- Obligatorio para Super-Admin
- Configuración en el perfil

### IPs Bloqueadas
- Agregar IPs maliciosas
- Las IPs bloqueadas no pueden acceder

### IPs Permitidas
- Acceso durante mantenimiento
- Super-Admin siempre tiene acceso

---

## 🚨 Troubleshooting

### Error 503 (Mantenimiento)
1. Ve a `/admin/maintenance`
2. Desactiva el modo mantenimiento
3. Si no puedes, usa Tinker:
   ```bash
   php artisan tinker
   App\Helpers\SettingsHelper::set('maintenance_mode', false);