<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">📊 Dashboard del Sistema</h1>
            <p class="text-blue-100 mt-2">Estadísticas y monitoreo del sistema</p>
        </div>

        <!-- ESTADÍSTICAS RÁPIDAS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-2xl">
                        👥
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Usuarios</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users?.total || 0 }}</p>
                        <p class="text-xs text-green-600 dark:text-green-400">+{{ stats.users?.new_today || 0 }} hoy</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-2xl">
                        💾
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Backups</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.backups?.total || 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Último: {{ stats.backups?.last?.created_at || 'Ninguno' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-2xl">
                        📦
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tamaño Backups</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.backups?.total_size || '0 B' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ stats.backups?.total || 0 }} archivos</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-2xl">
                        📁
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Logs</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.system?.logs_size || '0 B' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Espacio en disco</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICAS Y DETALLES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Usuarios -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">👥 Usuarios</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Total</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.users?.total || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-green-600 dark:text-green-400">✅ Activos</span>
                        <span class="font-bold text-green-600 dark:text-green-400">{{ stats.users?.active || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-red-600 dark:text-red-400">❌ Inactivos</span>
                        <span class="font-bold text-red-600 dark:text-red-400">{{ stats.users?.inactive || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-blue-600 dark:text-blue-400">🆕 Nuevos hoy</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ stats.users?.new_today || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">📈 Esta semana</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.users?.new_this_week || 0 }}</span>
                    </div>
                    <div v-if="stats.users?.last_registered" class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Último registro</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ stats.users.last_registered.name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ stats.users.last_registered.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Backups -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">💾 Backups</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Total</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.backups?.total || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Tamaño total</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.backups?.total_size || '0 B' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-blue-600 dark:text-blue-400">📊 Últimas 24h</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ stats.activity?.backups_last_24h || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-purple-600 dark:text-purple-400">📈 Última semana</span>
                        <span class="font-bold text-purple-600 dark:text-purple-400">{{ stats.activity?.backups_last_week || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">⏱️ Última actividad</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.activity?.last_activity || 'Ninguna' }}</span>
                    </div>
                    <div v-if="stats.backups?.last" class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Último backup</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ stats.backups.last.filename }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ stats.backups.last.size }} - {{ stats.backups.last.created_at }}</p>
                    </div>
                </div>
            </div>

            <!-- Sistema -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚙️ Sistema</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">PHP</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.system?.php_version || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Laravel</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.system?.laravel_version || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Base de Datos</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.system?.database_size || '0 B' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Espacio usado</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.system?.disk_usage?.used || '0 B' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Espacio libre</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ stats.system?.disk_usage?.free || '0 B' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Modo mantenimiento</span>
                        <span 
                            class="font-bold px-2 py-1 rounded-full text-xs"
                            :class="stats.security?.maintenance_mode ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'"
                        >
                            {{ stats.security?.maintenance_mode ? '🔒 ACTIVO' : '✅ DESACTIVADO' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Seguridad -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🔐 Seguridad</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">2FA</span>
                        <span 
                            class="font-bold px-2 py-1 rounded-full text-xs"
                            :class="stats.security?.['2fa_required'] ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'"
                        >
                            {{ stats.security?.['2fa_required'] ? '✅ OBLIGATORIO' : '⬜ DESACTIVADO' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">IPs bloqueadas</span>
                        <span class="font-bold text-gray-900 dark:text-white font-mono text-xs">
                            {{ stats.security?.blocked_ips || 'Ninguna' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">IPs permitidas</span>
                        <span class="font-bold text-gray-900 dark:text-white font-mono text-xs">
                            {{ stats.security?.allowed_ips || 'Ninguna' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Servidor</span>
                        <span class="font-bold text-gray-900 dark:text-white text-xs">{{ stats.system?.server_software || 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTÓN DE RECARGA -->
        <div class="mt-6 flex justify-end">
            <button 
                @click="loadStats"
                :disabled="loading"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 flex items-center gap-2"
            >
                <span v-if="loading" class="animate-spin">⟳</span>
                <span v-else>🔄</span>
                {{ loading ? 'Cargando...' : 'Actualizar' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// =============================================
// STATE
// =============================================
const stats = ref({});
const loading = ref(false);

// =============================================
// FUNCIONES
// =============================================
const loadStats = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/dashboard/stats');
        stats.value = response.data;
        console.log('📊 Estadísticas cargadas:', stats.value);
    } catch (error) {
        console.error('❌ Error cargando estadísticas:', error);
    } finally {
        loading.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadStats();
});
</script>