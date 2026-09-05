<template>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">🏛️ Tipos de Enseñanza</h1>
                    <p class="text-indigo-100 mt-2">Gestiona los tipos de enseñanza del sistema educativo</p>
                </div>
                <div class="flex items-center gap-3">
                    <router-link 
                        to="/admin/education-types/create"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        <span>➕</span> Nuevo Tipo
                    </router-link>
                    <button 
                        @click="loadTypes"
                        :disabled="loading"
                        class="bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg transition text-sm"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        <span v-else>🔄</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input 
                        v-model="filters.search"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Buscar por nombre o código..."
                        @input="loadTypes"
                    />
                </div>
                <div class="min-w-[150px]">
                    <select 
                        v-model="filters.is_active"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadTypes"
                    >
                        <option value="">Todos los estados</option>
                        <option value="1">✅ Activos</option>
                        <option value="0">⬜ Inactivos</option>
                    </select>
                </div>
                <button 
                    @click="resetFilters"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                >
                    🔄 Limpiar
                </button>
            </div>
        </div>

        <!-- TABLA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descripción</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Orden</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Carreras</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(type, index) in types" :key="type.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ index + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">
                                {{ type.code }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ type.name }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-sm max-w-xs truncate">
                                {{ type.description || 'Sin descripción' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                {{ type.sort_order || 0 }}
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="type.is_active 
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'"
                                >
                                    {{ type.is_active ? '✅ Activo' : '⬜ Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ type.careers_count || 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <router-link 
                                        :to="`/admin/education-types/${type.id}/edit`"
                                        class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition"
                                        title="Editar"
                                    >
                                        ✏️
                                    </router-link>
                                    <button 
                                        @click="deleteType(type.id)"
                                        :disabled="type.careers_count > 0"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        :title="type.careers_count > 0 ? 'Tiene carreras asociadas' : 'Eliminar'"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="types.length === 0 && !loading">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">🏛️</div>
                                <p>No hay tipos de enseñanza creados</p>
                                <router-link 
                                    to="/admin/education-types/create"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm"
                                >
                                    Crear el primer tipo
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="loading">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <span class="animate-spin inline-block">⟳</span> Cargando tipos...
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
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import educationTypesApi from '@/api/admin/educationTypes';

const router = useRouter();

// =============================================
// STATE
// =============================================
const types = ref([]);
const loading = ref(false);
const filters = ref({
    search: '',
    is_active: '',
});

const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0,
});

// =============================================
// FUNCIONES
// =============================================

const loadTypes = async () => {
    try {
        loading.value = true;
        
        // ✅ Construir params correctamente
        const params = {
            page: pagination.value.current_page,
        };
        
        if (filters.value.search) {
            params.search = filters.value.search;
        }
        
        if (filters.value.is_active !== '') {
            params.active = filters.value.is_active;
        }
        
        console.log('📤 Enviando params:', params);
        
        const response = await educationTypesApi.getAll(params);
        
        console.log('📥 Respuesta:', response.data);
        
        // ✅ Manejar la respuesta
        if (response.data && response.data.types) {
            const typesData = response.data.types;
            types.value = typesData.data || [];
            pagination.value = {
                current_page: typesData.current_page || 1,
                last_page: typesData.last_page || 1,
                per_page: typesData.per_page || 15,
                total: typesData.total || 0,
                from: typesData.from || 0,
                to: typesData.to || 0,
            };
        } else {
            types.value = [];
        }
        
    } catch (error) {
        console.error('Error cargando tipos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'No se pudieron cargar los tipos de enseñanza'
        });
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadTypes();
};

const resetFilters = () => {
    filters.value = {
        search: '',
        is_active: '',
    };
    pagination.value.current_page = 1;
    loadTypes();
};

const deleteType = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar tipo de enseñanza?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirm.isConfirmed) return;

    try {
        await educationTypesApi.delete(id);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Tipo eliminado',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        await loadTypes();
    } catch (error) {
        console.error('Error eliminando tipo:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar el tipo'
        });
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadTypes();
});
</script>