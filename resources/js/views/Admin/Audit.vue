<!-- resources/js/views/Admin/Audit.vue -->
<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">📋 Auditoría del Sistema</h1>
            <p class="text-purple-100 mt-2">Registro de todas las acciones realizadas en el sistema</p>
        </div>

        <!-- FILTROS -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Buscar</label>
                    <input 
                        v-model="filters.search"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Buscar..."
                        @input="loadAudit"
                    />
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Módulo</label>
                    <select 
                        v-model="filters.module"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadAudit"
                    >
                        <option value="">Todos</option>
                        <option value="maintenance">Mantenimiento</option>
                        <option value="backup">Backups</option>
                        <option value="security">Seguridad</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Acción</label>
                    <select 
                        v-model="filters.action"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadAudit"
                    >
                        <option value="">Todas</option>
                        <option value="activó">Activó</option>
                        <option value="desactivó">Desactivó</option>
                        <option value="creó">Creó</option>
                        <option value="eliminó">Eliminó</option>
                        <option value="limpió">Limpió</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button 
                        @click="resetFilters"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                    >
                        🔄 Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- TABLA DE AUDITORÍA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Usuario</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acción</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Módulo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">IP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ log.causer?.name || 'Sistema' }}
                                <div class="text-xs text-gray-400">{{ log.causer?.email || '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getActionClass(log.description)"
                                >
                                    {{ log.description }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getModuleClass(log.properties?.module)"
                                >
                                    {{ log.properties?.module || 'General' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs font-mono">
                                {{ log.properties?.ip || '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                <div>{{ formatDate(log.created_at) }}</div>
                                <div class="text-[10px] text-gray-400">{{ formatTimeAgo(log.created_at) }}</div>
                            </td>
                        </tr>
                        <tr v-if="logs.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>No hay registros de auditoría</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- PAGINACIÓN -->
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Mostrando {{ pagination.from || 0 }} - {{ pagination.to || 0 }} de {{ pagination.total || 0 }}
                </span>
                <div class="flex gap-2">
                    <button 
                        @click="changePage(pagination.current_page - 1)"
                        :disabled="pagination.current_page <= 1"
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        ◀
                    </button>
                    <span class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ pagination.current_page }} / {{ pagination.last_page }}
                    </span>
                    <button 
                        @click="changePage(pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        ▶
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import moment from 'moment';

// =============================================
// STATE
// =============================================
const logs = ref([]);
const loading = ref(false);
const filters = ref({
    search: '',
    module: '',
    action: '',
});
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
    from: 0,
    to: 0,
});

// =============================================
// FUNCIONES
// =============================================
const loadAudit = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/audit', {
            params: {
                page: pagination.value.current_page,
                search: filters.value.search,
                module: filters.value.module,
                action: filters.value.action,
            }
        });
        
        logs.value = response.data.data || [];
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to,
        };
    } catch (error) {
        console.error('Error cargando auditoría:', error);
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadAudit();
};

const resetFilters = () => {
    filters.value = { search: '', module: '', action: '' };
    pagination.value.current_page = 1;
    loadAudit();
};

const getActionClass = (description) => {
    if (!description) return 'bg-gray-100 text-gray-700';
    if (description.includes('activó')) return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
    if (description.includes('desactivó')) return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
    if (description.includes('creó')) return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
    if (description.includes('eliminó')) return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
    if (description.includes('limpió')) return 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400';
    return 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
};

const getModuleClass = (module) => {
    if (!module) return 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
    if (module === 'maintenance') return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400';
    if (module === 'backup') return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
    if (module === 'security') return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
    return 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
};

const formatDate = (date) => {
    if (!date) return '-';
    return moment(date).format('DD/MM/YYYY HH:mm:ss');
};

const formatTimeAgo = (date) => {
    if (!date) return '-';
    return moment(date).fromNow();
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadAudit();
});
</script>