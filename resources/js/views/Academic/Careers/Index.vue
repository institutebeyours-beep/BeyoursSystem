<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">🎓 Carreras</h1>
                    <p class="text-emerald-100 mt-2">
                        Gestiona las carreras y programas académicos
                        <span v-if="selectedEducationType" class="block text-sm text-emerald-200 mt-1">
                            📌 {{ getEducationTypeName(selectedEducationType) }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <router-link 
                        to="/academic/careers/create"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        ➕ Nueva Carrera
                    </router-link>
                    <router-link 
                        to="/academic/careers/create-from-template"
                        class="bg-purple-500/30 hover:bg-purple-500/40 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        🚀 Desde Plantilla
                    </router-link>
                    <button 
                        @click="loadCareers"
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
                <!-- ✅ PRIMERO: Tipo de Enseñanza (Obligatorio) -->
                <div class="min-w-[200px] flex-1">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        🏛️ Tipo de Enseñanza *
                    </label>
                    <select 
                        v-model="selectedEducationType"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="onEducationTypeChange"
                    >
                        <option value="">Selecciona un tipo</option>
                        <option 
                            v-for="type in educationTypes" 
                            :key="type.id" 
                            :value="type.id"
                        >
                            {{ type.name }}
                        </option>
                    </select>
                </div>

                <!-- Búsqueda -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        🔍 Buscar
                    </label>
                    <input 
                        v-model="filters.search"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Buscar por nombre o código..."
                        @input="loadCareers"
                        :disabled="!selectedEducationType"
                    />
                </div>

                <!-- Estado -->
                <div class="min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        📊 Estado
                    </label>
                    <select 
                        v-model="filters.is_active"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadCareers"
                        :disabled="!selectedEducationType"
                    >
                        <option value="">Todos</option>
                        <option value="1">✅ Activas</option>
                        <option value="0">⬜ Inactivas</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button 
                        @click="resetFilters"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                        :disabled="!selectedEducationType"
                    >
                        🔄 Limpiar
                    </button>
                </div>
            </div>

            <!-- Mensaje si no hay tipo seleccionado -->
            <div v-if="!selectedEducationType" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    ⚠️ Selecciona un tipo de enseñanza para ver sus carreras
                </p>
            </div>
        </div>

        <!-- TABLA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Créditos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Semestres</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cursos</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="career in careers" :key="career.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">
                                {{ career.code }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-900 dark:text-white font-medium">{{ career.name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ career.description }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ career.education_type?.name || 'Sin tipo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ career.total_credits || 0 }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ career.duration_semesters || 0 }}
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="career.is_active 
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'"
                                >
                                    {{ career.is_active ? '✅ Activa' : '⬜ Inactiva' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                    {{ career.courses_count || 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <router-link 
                                        :to="`/academic/careers/${career.id}`"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition"
                                        title="Ver"
                                    >
                                        👁️
                                    </router-link>
                                    <router-link 
                                        :to="`/academic/careers/${career.id}/edit`"
                                        class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition"
                                        title="Editar"
                                    >
                                        ✏️
                                    </router-link>
                                    <button 
                                        @click="deleteCareer(career.id)"
                                        v-if="canDelete(career)"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
                                        title="Eliminar"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="careers.length === 0 && !loading && selectedEducationType">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">🎓</div>
                                <p>No hay carreras para este tipo de enseñanza</p>
                                <router-link 
                                    to="/academic/careers/create"
                                    class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm"
                                >
                                    Crear la primera carrera
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="!selectedEducationType && !loading">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">🏛️</div>
                                <p>Selecciona un tipo de enseñanza para ver sus carreras</p>
                            </td>
                        </tr>
                        <tr v-if="loading">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <span class="animate-spin inline-block">⟳</span> Cargando carreras...
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
                        :disabled="pagination.current_page <= 1 || !selectedEducationType"
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        ◀
                    </button>
                    <span class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ pagination.current_page }} / {{ pagination.last_page }}
                    </span>
                    <button 
                        @click="changePage(pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page || !selectedEducationType"
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
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import Swal from 'sweetalert2';
import careersApi from '@/api/academic/careers';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// =============================================
// STATE
// =============================================
const careers = ref([]);
const educationTypes = ref([]);
const selectedEducationType = ref('');
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
// COMPUTED
// =============================================

const canDelete = (career) => {
    if (authStore.hasRole('admin') || authStore.hasRole('super-admin')) {
        return true;
    }
    return false;
};

const getEducationTypeName = (id) => {
    const type = educationTypes.value.find(t => t.id === id);
    return type ? type.name : '';
};

// =============================================
// FUNCIONES
// =============================================

const loadEducationTypes = async () => {
    try {
        // ✅ Endpoint público para todos los usuarios autenticados
        const response = await axios.get('/api/education-types/public');
        educationTypes.value = response.data.types || [];
        console.log('✅ Tipos de enseñanza cargados:', educationTypes.value.length);
    } catch (error) {
        console.error('Error cargando tipos de enseñanza desde endpoint público:', error);
        educationTypes.value = [];
        
        // ✅ Fallback: si el usuario es admin, intentar con el endpoint admin
        if (authStore.isAdmin || authStore.isSuperAdmin) {
            try {
                const response = await axios.get('/api/admin/education-types/all');
                educationTypes.value = response.data.types || [];
            } catch (e) {
                console.error('Error cargando tipos desde admin:', e);
            }
        }
    }
};

const loadCareers = async () => {
    if (!selectedEducationType.value) {
        careers.value = [];
        pagination.value.total = 0;
        return;
    }

    try {
        loading.value = true;
        
        const params = {
            page: pagination.value.current_page,
            education_type_id: selectedEducationType.value,
        };
        
        if (filters.value.search) {
            params.search = filters.value.search;
        }
        
        if (filters.value.is_active !== '') {
            params.active = filters.value.is_active;
        }
        
        const response = await careersApi.getAll(params);
        
        careers.value = response.data.careers.data || [];
        pagination.value = {
            current_page: response.data.careers.current_page,
            last_page: response.data.careers.last_page,
            per_page: response.data.careers.per_page,
            total: response.data.careers.total,
            from: response.data.careers.from,
            to: response.data.careers.to,
        };
    } catch (error) {
        console.error('Error cargando carreras:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las carreras'
        });
    } finally {
        loading.value = false;
    }
};

const onEducationTypeChange = () => {
    pagination.value.current_page = 1;
    filters.value.search = '';
    loadCareers();
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadCareers();
};

const resetFilters = () => {
    filters.value = {
        search: '',
        is_active: '',
    };
    pagination.value.current_page = 1;
    loadCareers();
};

const deleteCareer = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar carrera?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirm.isConfirmed) return;

    try {
        await careersApi.delete(id);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Carrera eliminada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        await loadCareers();
    } catch (error) {
        console.error('Error eliminando carrera:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar la carrera'
        });
    }
};

// =============================================
// WATCH
// =============================================

watch(() => route.query.education_type_id, (newVal) => {
    if (newVal) {
        selectedEducationType.value = parseInt(newVal);
        loadCareers();
    }
});

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadEducationTypes();
    
    if (route.query.education_type_id) {
        selectedEducationType.value = parseInt(route.query.education_type_id);
        loadCareers();
    }
});
</script>