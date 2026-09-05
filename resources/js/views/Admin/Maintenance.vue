<!-- resources/js/views/Admin/Maintenance.vue -->
<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">🛠️ Mantenimiento del Sistema</h1>
                    <p class="text-red-100 mt-2">Gestiona el modo mantenimiento y el estado del sistema</p>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        @click="refreshStatus" 
                        class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                        :disabled="loading"
                    >
                        <span>🔄</span> 
                        {{ loading ? 'Cargando...' : 'Recargar' }}
                    </button>
                    <div class="bg-white/10 px-4 py-2 rounded-lg">
                        <span class="text-sm">Super-Admin</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ESTADO DEL SISTEMA -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Estado del sistema</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ status.maintenance_mode ? '🔒 En mantenimiento' : '✅ Operativo' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Uso de disco</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ status.disk_usage?.used || '0 B' }}
                            <span class="text-sm font-normal text-gray-500">
                                / {{ status.disk_usage?.total || '0 B' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Logs retenidos</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ status.log_retention_days || 30 }} días
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- INDICADOR DE ESTADO EN TIEMPO REAL -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span 
                        class="inline-block w-3 h-3 rounded-full"
                        :class="status.maintenance_mode ? 'bg-red-500 animate-pulse' : 'bg-green-500'"
                    ></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Estado actual: 
                        <strong :class="status.maintenance_mode ? 'text-red-600' : 'text-green-600'">
                            {{ status.maintenance_mode ? '🔒 MANTENIMIENTO ACTIVO' : '✅ SISTEMA OPERATIVO' }}
                        </strong>
                    </span>
                </div>
                <span class="text-xs text-gray-400">
                    Última actualización: {{ lastUpdate || 'Cargando...' }}
                </span>
            </div>
        </div>

        <!-- PANEL DE MANTENIMIENTO -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">🔧 Control de Mantenimiento</h2>

            <div class="space-y-6">
                <!-- Toggle Modo Mantenimiento -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <div class="flex-1 mb-3 sm:mb-0">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Modo mantenimiento</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ status.maintenance_mode ? '🔒 Sistema en mantenimiento' : '✅ Sistema operativo' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Solo Super-Admin puede acceder durante el mantenimiento
                        </p>
                    </div>
                    <button 
                        @click="toggleMaintenance"
                        :disabled="loading"
                        class="relative inline-flex items-center h-8 rounded-full w-16 transition-colors duration-300 focus:outline-none"
                        :class="[
                            loading ? 'opacity-50 cursor-not-allowed' : '',
                            status.maintenance_mode ? 'bg-red-600' : 'bg-green-600'
                        ]"
                    >
                        <span 
                            class="inline-block w-6 h-6 transform bg-white rounded-full transition-transform duration-300 shadow-md flex items-center justify-center"
                            :class="status.maintenance_mode ? 'translate-x-9' : 'translate-x-1'"
                        >
                            <span v-if="loading" class="text-[10px] text-gray-600 animate-spin">⟳</span>
                        </span>
                        <span class="absolute text-xs font-bold text-white">
                            {{ loading ? '⏳' : (status.maintenance_mode ? 'ON' : 'OFF') }}
                        </span>
                    </button>
                </div>

                <!-- Mensaje de Mantenimiento -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <div class="flex-1 mb-3 sm:mb-0">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Mensaje de mantenimiento</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Mensaje que verán los usuarios</p>
                    </div>
                    <div class="flex-1 sm:max-w-md">
                        <input 
                            v-model="maintenanceMessage"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Sistema en mantenimiento..."
                            @change="updateMessage"
                        />
                    </div>
                </div>

                <!-- IPs Permitidas -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3">
                        <div>
                            <p class="font-medium text-gray-700 dark:text-gray-300">✅ IPs permitidas</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">IPs que pueden acceder en modo mantenimiento</p>
                            <p class="text-xs text-gray-400">Super-Admin siempre tiene acceso</p>
                        </div>
                        <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-1 rounded-full mt-2 sm:mt-0">
                            {{ allowedIpsList.length }} IP{{ allowedIpsList.length !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    <!-- Lista de IPs -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span 
                            v-for="(ip, index) in allowedIpsList" 
                            :key="index"
                            class="inline-flex items-center gap-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1 text-sm"
                        >
                            <span class="text-gray-700 dark:text-gray-300">{{ ip }}</span>
                            <button 
                                @click="removeAllowedIp(index)"
                                class="text-red-400 hover:text-red-600 transition"
                                title="Eliminar IP"
                            >
                                ✕
                            </button>
                        </span>
                        <span v-if="allowedIpsList.length === 0" class="text-sm text-gray-400 italic">
                            Ninguna IP permitida
                        </span>
                    </div>

                    <!-- Agregar IP -->
                    <div class="flex gap-2">
                        <input 
                            v-model="newAllowedIp"
                            type="text"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="192.168.1.100"
                            @keyup.enter="addAllowedIp"
                        />
                        <button 
                            @click="addAllowedIp"
                            :disabled="!newAllowedIp || loading"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
                        >
                            Agregar
                        </button>
                        <button 
                            @click="clearAllowedIps"
                            :disabled="allowedIpsList.length === 0 || loading"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition disabled:opacity-50"
                            title="Borrar todas"
                        >
                            🗑️
                        </button>
                    </div>
                </div>

                <!-- IPs Bloqueadas -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3">
                        <div>
                            <p class="font-medium text-gray-700 dark:text-gray-300">🚫 IPs bloqueadas</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">IPs bloqueadas incluso en modo normal</p>
                            <p class="text-xs text-red-400">Estas IPs no podrán acceder al sistema</p>
                        </div>
                        <span class="text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-1 rounded-full mt-2 sm:mt-0">
                            {{ blockedIpsList.length }} IP{{ blockedIpsList.length !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    <!-- Lista de IPs -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span 
                            v-for="(ip, index) in blockedIpsList" 
                            :key="index"
                            class="inline-flex items-center gap-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1 text-sm"
                        >
                            <span class="text-gray-700 dark:text-gray-300">{{ ip }}</span>
                            <button 
                                @click="removeBlockedIp(index)"
                                class="text-red-400 hover:text-red-600 transition"
                                title="Eliminar IP"
                            >
                                ✕
                            </button>
                        </span>
                        <span v-if="blockedIpsList.length === 0" class="text-sm text-gray-400 italic">
                            Ninguna IP bloqueada
                        </span>
                    </div>

                    <!-- Agregar IP -->
                    <div class="flex gap-2">
                        <input 
                            v-model="newBlockedIp"
                            type="text"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="203.0.113.1"
                            @keyup.enter="addBlockedIp"
                        />
                        <button 
                            @click="addBlockedIp"
                            :disabled="!newBlockedIp || loading"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50"
                        >
                            Agregar
                        </button>
                        <button 
                            @click="clearBlockedIps"
                            :disabled="blockedIpsList.length === 0 || loading"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition disabled:opacity-50"
                            title="Borrar todas"
                        >
                            🗑️
                        </button>
                    </div>
                </div>
            </div>
        </div>
<!-- ========================================== -->
<!-- SECCIÓN DE BACKUPS                        -->
<!-- ========================================== -->
<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">💾 Backups</h2>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400">
                {{ backups.length }} archivo{{ backups.length !== 1 ? 's' : '' }}
                <span class="ml-2 text-gray-500">
                    ({{ backupDiskUsage }})
                </span>
            </span>
            <button 
                @click="createBackup"
                :disabled="loading"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 flex items-center gap-2"
            >
                <span>📤</span>
                Crear Backup
            </button>
        </div>
    </div>

    <!-- Debug temporal -->
    <div v-if="backups.length === 0" class="bg-yellow-100 dark:bg-yellow-900/20 p-2 mb-2 rounded text-xs">
        🔍 Debug: No hay backups. Total en BD: {{ backups.length }}
    </div>

    <!-- Lista de Backups -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Archivo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tamaño</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Creado</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr v-for="backup in backups" :key="backup.id || backup.filename" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                    <!-- ✅ CORREGIDO: backup.filename en lugar de backup.name -->
                    <td class="px-4 py-3 text-gray-900 dark:text-white font-mono text-xs">
                        {{ backup.filename || backup.name || 'Sin nombre' }}
                    </td>
                    <td class="px-4 py-3">
                        <span 
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                            :class="{
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': backup.type === 'full',
                                'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': backup.type === 'database',
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': backup.type === 'files',
                                'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400': !backup.type || backup.type === 'unknown'
                            }"
                        >
                            {{ backup.type || 'full' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                        {{ backup.size || '0 B' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                        <div>{{ backup.created_at }}</div>
                        <div class="text-[10px] text-gray-400">{{ backup.created_at_human || '' }}</div>
                    </td>
<td class="px-4 py-3 text-right">
    <div class="flex items-center justify-end gap-2">
        <!-- ✅ Botón de descarga con método -->
        <button 
            @click="downloadBackup(backup.filename)"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition"
            title="Descargar"
            :disabled="downloading"
        >
            <span v-if="downloading" class="inline-block animate-spin">⏳</span>
            <span v-else>⬇️</span>
        </button>
        <button 
            @click="deleteBackup(backup.filename)"
            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
            title="Eliminar"
        >
            🗑️
        </button>
    </div>
</td>
                </tr>
                <tr v-if="backups.length === 0">
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                        <div class="text-4xl mb-2">📭</div>
                        <p>No hay backups disponibles</p>
                        <p class="text-xs mt-1">Haz clic en "Crear Backup" para generar el primero</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
        <!-- ACCIONES RÁPIDAS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button 
                @click="clearCache"
                :disabled="loading"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105 disabled:opacity-50"
            >
                <div class="text-center">
                    <div class="text-3xl mb-2">🧹</div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Limpiar caché</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Reiniciar caché del sistema</p>
                </div>
            </button>

            <button 
                @click="cleanLogs"
                :disabled="loading"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105 disabled:opacity-50"
            >
                <div class="text-center">
                    <div class="text-3xl mb-2">📋</div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Limpiar logs</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Eliminar logs antiguos</p>
                </div>
            </button>

            <button 
                @click="viewSystemInfo"
                :disabled="loading"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105 disabled:opacity-50"
            >
                <div class="text-center">
                    <div class="text-3xl mb-2">📊</div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Información</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ver estado del sistema</p>
                </div>
            </button>
        </div>

        <!-- MODAL DE INFORMACIÓN DEL SISTEMA -->
        <div v-if="showSystemInfo" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">📊 Información del Sistema</h2>
                    <button @click="showSystemInfo = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        ✕
                    </button>
                </div>
                <div class="space-y-3">
                    <div v-for="(value, key) in systemInfo" :key="key" class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatLabel(key) }}</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ value }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

// =============================================
// STATE
// =============================================
const loading = ref(false);
const status = ref({
    maintenance_mode: false,
    maintenance_message: '',
    maintenance_allow_ips: '',
    maintenance_block_ips: '',
    disk_usage: {
        used: '0 B',
        total: '0 B',
        free: '0 B',
        percentage: 0
    },
    log_retention_days: 30
});
const maintenanceMessage = ref('');
const allowedIps = ref('');
const blockedIps = ref('');
const newAllowedIp = ref('');
const newBlockedIp = ref('');
const showSystemInfo = ref(false);
const systemInfo = ref({});
const lastUpdate = ref('');
const downloading = ref(false);

// =============================================
// STATE DE BACKUPS
// =============================================
const backups = ref([]);
const backupDiskUsage = ref('0 B');

// =============================================
// COMPUTED
// =============================================
const allowedIpsList = computed(() => {
    if (!allowedIps.value || allowedIps.value.trim() === '') return [];
    return allowedIps.value.split(',').map(ip => ip.trim()).filter(ip => ip !== '');
});

const blockedIpsList = computed(() => {
    if (!blockedIps.value || blockedIps.value.trim() === '') return [];
    return blockedIps.value.split(',').map(ip => ip.trim()).filter(ip => ip !== '');
});
const downloadBackup = async (filename) => {
    downloading.value = true;
    
    try {
        const token = localStorage.getItem('auth_token');
        
        // ✅ Obtener el archivo con fetch
        const response = await fetch(`/api/admin/maintenance/backup/download/${filename}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/zip',
            },
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Error al descargar');
        }
        
        // ✅ Obtener el blob y descargar
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Descarga iniciada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
    } catch (error) {
        console.error('❌ Error descargando:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.message || 'No se pudo descargar el backup',
        });
    } finally {
        downloading.value = false;
    }
};
// =============================================
// VALIDACIÓN DE IPs
// =============================================
const isValidIP = (ip) => {
    const ipv4Pattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    return ipv4Pattern.test(ip);
};

// =============================================
// FUNCIONES PRINCIPALES
// =============================================
const loadStatus = async () => {
    try {
        const response = await axios.get('/api/admin/maintenance/status');
        
        const data = response.data;
        data.maintenance_mode = data.maintenance_mode === true || data.maintenance_mode === '1' || data.maintenance_mode === 1;
        
        status.value = data;
        maintenanceMessage.value = data.maintenance_message || '';
        allowedIps.value = data.maintenance_allow_ips || '';
        blockedIps.value = data.maintenance_block_ips || '';
        lastUpdate.value = new Date().toLocaleTimeString();
        
        console.log('📊 Estado actualizado:', {
            maintenance_mode: status.value.maintenance_mode,
            allow_ips: allowedIps.value,
            block_ips: blockedIps.value,
        });
    } catch (error) {
        console.error('Error cargando estado:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar el estado del sistema',
        });
    }
};

const refreshStatus = async () => {
    loading.value = true;
    try {
        await loadStatus();
        Swal.fire({
            icon: 'success',
            title: '✅ Estado actualizado',
            text: `Modo mantenimiento: ${status.value.maintenance_mode ? 'ACTIVADO' : 'DESACTIVADO'}`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo actualizar el estado',
        });
    } finally {
        loading.value = false;
    }
};

const toggleMaintenance = async () => {
    loading.value = true;
    try {
        const newStatus = !status.value.maintenance_mode;
        await axios.post('/api/admin/maintenance/toggle', {
            enabled: newStatus,
            message: maintenanceMessage.value,
            allow_ips: allowedIps.value,
            block_ips: blockedIps.value,
        });
        
        await loadStatus();
        
        Swal.fire({
            icon: 'success',
            title: newStatus ? '🔒 Modo mantenimiento activado' : '🔓 Modo mantenimiento desactivado',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Error al cambiar el modo mantenimiento',
        });
    } finally {
        loading.value = false;
    }
};

const updateMessage = async () => {
    try {
        await axios.post('/api/admin/maintenance/toggle', {
            enabled: status.value.maintenance_mode,
            message: maintenanceMessage.value,
            allow_ips: allowedIps.value,
            block_ips: blockedIps.value,
        });
        await loadStatus();
        
        Swal.fire({
            icon: 'success',
            title: '✅ Mensaje actualizado',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo actualizar el mensaje',
        });
    }
};

// =============================================
// FUNCIONES PARA IPs - PERMITIDAS
// =============================================
const addAllowedIp = async () => {
    const ip = newAllowedIp.value.trim();
    
    if (!ip) return;
    
    if (!isValidIP(ip)) {
        Swal.fire({
            icon: 'error',
            title: '❌ IP inválida',
            text: `"${ip}" no es una dirección IP válida. Ejemplo: 192.168.1.100`,
        });
        return;
    }
    
    if (allowedIpsList.value.includes(ip)) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ IP duplicada',
            text: `"${ip}" ya está en la lista de IPs permitidas.`,
        });
        newAllowedIp.value = '';
        return;
    }
    
    const currentList = allowedIpsList.value;
    currentList.push(ip);
    allowedIps.value = currentList.join(', ');
    newAllowedIp.value = '';
    
    await saveIps();
};

const removeAllowedIp = async (index) => {
    const currentList = allowedIpsList.value;
    const removedIp = currentList[index];
    currentList.splice(index, 1);
    allowedIps.value = currentList.join(', ');
    
    await saveIps();
    
    Swal.fire({
        icon: 'info',
        title: '🗑️ IP eliminada',
        text: `"${removedIp}" eliminada de las IPs permitidas.`,
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

const clearAllowedIps = async () => {
    if (allowedIpsList.value.length === 0) return;
    
    const confirm = await Swal.fire({
        title: '¿Borrar todas las IPs permitidas?',
        text: `Se eliminarán ${allowedIpsList.value.length} IP${allowedIpsList.value.length > 1 ? 's' : ''} de la lista.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, borrar todas',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;
    
    allowedIps.value = '';
    await saveIps();
    
    Swal.fire({
        icon: 'success',
        title: '🗑️ IPs eliminadas',
        text: 'Todas las IPs permitidas han sido eliminadas.',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

// =============================================
// FUNCIONES PARA IPs - BLOQUEADAS
// =============================================
const addBlockedIp = async () => {
    const ip = newBlockedIp.value.trim();
    
    if (!ip) return;
    
    if (!isValidIP(ip)) {
        Swal.fire({
            icon: 'error',
            title: '❌ IP inválida',
            text: `"${ip}" no es una dirección IP válida. Ejemplo: 192.168.1.100`,
        });
        return;
    }
    
    if (blockedIpsList.value.includes(ip)) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ IP duplicada',
            text: `"${ip}" ya está en la lista de IPs bloqueadas.`,
        });
        newBlockedIp.value = '';
        return;
    }
    
    const currentList = blockedIpsList.value;
    currentList.push(ip);
    blockedIps.value = currentList.join(', ');
    newBlockedIp.value = '';
    
    await saveIps();
};

const removeBlockedIp = async (index) => {
    const currentList = blockedIpsList.value;
    const removedIp = currentList[index];
    currentList.splice(index, 1);
    blockedIps.value = currentList.join(', ');
    
    await saveIps();
    
    Swal.fire({
        icon: 'info',
        title: '🗑️ IP eliminada',
        text: `"${removedIp}" eliminada de las IPs bloqueadas.`,
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

const clearBlockedIps = async () => {
    if (blockedIpsList.value.length === 0) return;
    
    const confirm = await Swal.fire({
        title: '¿Borrar todas las IPs bloqueadas?',
        text: `Se eliminarán ${blockedIpsList.value.length} IP${blockedIpsList.value.length > 1 ? 's' : ''} de la lista.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, borrar todas',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;
    
    blockedIps.value = '';
    await saveIps();
    
    Swal.fire({
        icon: 'success',
        title: '🗑️ IPs eliminadas',
        text: 'Todas las IPs bloqueadas han sido eliminadas.',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

// =============================================
// FUNCIÓN PARA GUARDAR IPs EN EL SERVIDOR
// =============================================
const saveIps = async () => {
    try {
        await axios.post('/api/admin/maintenance/toggle', {
            enabled: status.value.maintenance_mode,
            message: maintenanceMessage.value,
            allow_ips: allowedIps.value,
            block_ips: blockedIps.value,
        });
        await loadStatus();
    } catch (error) {
        console.error('Error guardando IPs:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron guardar las IPs',
        });
    }
};

// =============================================
// OTRAS FUNCIONES
// =============================================
const clearCache = async () => {
    const confirm = await Swal.fire({
        title: '¿Limpiar caché?',
        text: 'Esto limpiará toda la caché del sistema',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;
    
    loading.value = true;
    try {
        await axios.post('/api/admin/maintenance/clear-cache');
        
        Swal.fire({
            icon: 'success',
            title: '✅ Caché limpiada',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo limpiar la caché',
        });
    } finally {
        loading.value = false;
    }
};

const cleanLogs = async () => {
    const confirm = await Swal.fire({
        title: '🧹 ¿Limpiar logs?',
        html: `
            <p class="text-left text-sm text-gray-600 dark:text-gray-400">
                Esto eliminará todos los archivos de log antiguos y 
                <strong>limpiará el contenido</strong> del archivo principal.
            </p>
            <p class="text-left text-xs text-yellow-500 mt-2">
                🛡️ El archivo <strong>laravel.log</strong> se conserva pero se limpia.
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, limpiar logs',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;
    
    loading.value = true;
    try {
        const response = await axios.post('/api/admin/maintenance/clean-logs');
        const result = response.data;
        
        let html = `
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Se eliminaron <strong>${result.deleted}</strong> archivos de log.
            </p>
            <p class="text-xs text-green-500 mt-2">
                ✅ Archivo laravel.log limpiado correctamente.
            </p>
        `;
        
        if (result.files && result.files.length > 0) {
            html += `
                <div class="mt-3 text-left">
                    <p class="text-xs font-medium text-gray-500">Archivos eliminados:</p>
                    <ul class="text-xs text-gray-400 list-disc list-inside max-h-24 overflow-y-auto">
                        ${result.files.map(f => `<li>${f}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        if (result.errors && result.errors.length > 0) {
            html += `
                <div class="mt-3 text-left">
                    <p class="text-xs font-medium text-red-500">⚠️ Errores:</p>
                    <ul class="text-xs text-red-400 list-disc list-inside">
                        ${result.errors.map(e => `<li>${e}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        Swal.fire({
            icon: 'success',
            title: '🧹 Logs limpiados',
            html: html,
            timer: 4000,
            showConfirmButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#6366f1',
        });
        
        await loadStatus();
        
    } catch (error) {
        console.error('Error limpiando logs:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo limpiar los logs',
        });
    } finally {
        loading.value = false;
    }
};

const viewSystemInfo = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/maintenance/system-info');
        systemInfo.value = response.data;
        showSystemInfo.value = true;
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo obtener la información del sistema',
        });
    } finally {
        loading.value = false;
    }
};

const formatLabel = (key) => {
    const labels = {
        php_version: 'PHP',
        laravel_version: 'Laravel',
        server_software: 'Servidor',
        memory_usage: 'Memoria usada',
        memory_limit: 'Límite de memoria',
        max_execution_time: 'Tiempo máximo',
        upload_max_filesize: 'Tamaño máximo subida',
        disk_free_space: 'Espacio libre',
        disk_total_space: 'Espacio total',
        database_driver: 'Base de datos',
        database_name: 'Nombre BD',
        app_env: 'Entorno',
        app_debug: 'Debug',
    };
    return labels[key] || key;
};

// =============================================
// FUNCIONES DE BACKUPS
// =============================================

/**
 * Cargar lista de backups
 */
const loadBackups = async () => {
    try {
        console.log('🔄 Cargando backups...');
        const response = await axios.get('/api/admin/maintenance/backups');
        
        console.log('✅ Respuesta:', response.data);
        console.log('📋 Backups:', response.data.backups);
        
        // ✅ Asignar directamente
        backups.value = response.data.backups || [];
        
        console.log('✅ backups.value asignado:', backups.value.length);
        
    } catch (error) {
        console.error('❌ Error:', error);
        backups.value = [];
    }
};
/**
 * Crear backup
 */
const createBackup = async () => {
    const confirm = await Swal.fire({
        title: '📤 ¿Crear backup?',
        html: `
            <p class="text-left text-sm text-gray-600 dark:text-gray-400">
                Se creará un backup completo del sistema incluyendo:
            </p>
            <ul class="text-left text-xs text-gray-500 dark:text-gray-400 list-disc list-inside mt-2">
                <li>🗄️ Base de datos completa</li>
                <li>📁 Archivos del sistema</li>
                <li>⚙️ Configuraciones</li>
            </ul>
            <p class="text-left text-xs text-yellow-500 mt-3">
                ⏱️ Esto puede tomar unos segundos...
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '✅ Crear Backup',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;
    
    loading.value = true;
    try {
        Swal.fire({
            title: '⏳ Creando backup...',
            text: 'Por favor espera, esto puede tomar unos segundos.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const response = await axios.post('/api/admin/maintenance/backup');
        
        // ✅ Recargar la lista de backups
        await loadBackups();
        
        Swal.fire({
            icon: 'success',
            title: '✅ Backup creado',
            html: `
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Archivo: <strong class="font-mono text-xs">${response.data.file}</strong>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tamaño: <strong>${response.data.size}</strong>
                </p>
            `,
            timer: 3000,
            showConfirmButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#6366f1',
        });
        
    } catch (error) {
        console.error('❌ Error creando backup:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo crear el backup',
        });
    } finally {
        loading.value = false;
    }
};

/**
 * Eliminar backup
 */
const deleteBackup = async (filename) => {
    const confirm = await Swal.fire({
        title: '🗑️ ¿Eliminar backup?',
        html: `
            <p class="text-sm text-gray-600 dark:text-gray-400">
                ¿Estás seguro de eliminar el backup?
            </p>
            <p class="text-xs font-mono text-gray-500 mt-2">
                ${filename}
            </p>
            <p class="text-xs text-red-500 mt-2">
                ⚠️ Esta acción no se puede deshacer.
            </p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;
    
    loading.value = true;
    try {
        await axios.delete(`/api/admin/maintenance/backup/${filename}`);
        
        // ✅ Recargar la lista de backups
        await loadBackups();
        
        Swal.fire({
            icon: 'success',
            title: '🗑️ Backup eliminado',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
    } catch (error) {
        console.error('❌ Error eliminando backup:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar el backup',
        });
    } finally {
        loading.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadStatus();
    loadBackups();
});
</script>