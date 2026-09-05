<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">📋 Plantillas Académicas</h1>
                    <p class="text-purple-100 mt-2">
                        Gestiona las plantillas para crear carreras rápidamente
                        <span v-if="!isAdmin" class="block text-sm text-purple-200 mt-1">
                            👤 Visualizando tus plantillas personalizadas y las predeterminadas
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <router-link 
                        v-if="canCreateTemplates"
                        to="/admin/templates/create"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        <span>➕</span> Nueva Plantilla
                    </router-link>
                    <button 
                        @click="loadTemplates"
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
                        @input="loadTemplates"
                    />
                </div>
                <div class="min-w-[150px]">
                    <select 
                        v-model="filters.education_type_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadTemplates"
                    >
                        <option value="">Todos los tipos</option>
                        <option 
                            v-for="type in educationTypes" 
                            :key="type.id" 
                            :value="type.id"
                        >
                            {{ type.name }}
                        </option>
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <select 
                        v-model="filters.is_default"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadTemplates"
                    >
                        <option value="">Todos</option>
                        <option value="1">⭐ Predeterminadas</option>
                        <option value="0">📝 Personalizadas</option>
                    </select>
                </div>
                <!-- ✅ NUEVO: Filtro por creador -->
                <div class="min-w-[150px]" v-if="isAdmin">
                    <select 
                        v-model="filters.created_by"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadTemplates"
                    >
                        <option value="">Todos los creadores</option>
                        <option value="me">Mis plantillas</option>
                        <option value="others">De otros</option>
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

        <!-- ✅ BADGE DE ROL EN LA TABLA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Semestres</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Créditos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Creador</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="template in templates" :key="template.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">
                                {{ template.code }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-900 dark:text-white font-medium">{{ template.name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ template.description }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ template.education_type?.name || 'Sin tipo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ template.semesters?.length || 0 }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ template.total_credits || 0 }}
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="template.is_default 
                                        ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' 
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'"
                                >
                                    {{ template.is_default ? '⭐ Predeterminada' : '📝 Personalizada' }}
                                </span>
                                <span 
                                    v-if="template.is_active"
                                    class="inline-flex items-center ml-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                >
                                    ✅ Activa
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ template.created_by === authUserId ? '👤 Tú' : template.creator?.name || 'Sistema' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <router-link 
                                        :to="`/admin/templates/${template.id}/preview`"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition"
                                        title="Vista previa"
                                    >
                                        👁️
                                    </router-link>
                                    <router-link 
                                        v-if="canEditTemplate(template)"
                                        :to="`/admin/templates/${template.id}/edit`"
                                        class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition"
                                        title="Editar"
                                    >
                                        ✏️
                                    </router-link>
                                    <button 
                                        @click="cloneTemplate(template)"
                                        class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 transition"
                                        title="Clonar"
                                    >
                                        📋
                                    </button>
                                    <button 
                                        @click="deleteTemplate(template.id)"
                                        v-if="canDeleteTemplate(template)"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
                                        title="Eliminar"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="templates.length === 0 && !loading">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>No hay plantillas disponibles</p>
                                <router-link 
                                    v-if="canCreateTemplates"
                                    to="/admin/templates/create"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm"
                                >
                                    Crear la primera plantilla
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="loading">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <span class="animate-spin inline-block">⟳</span> Cargando plantillas...
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

    <!-- MODAL: CLONAR PLANTILLA -->
    <div v-if="showCloneModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                📋 Clonar Plantilla
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre *
                    </label>
                    <input 
                        v-model="cloneForm.name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Ej: Mi Plantilla Personalizada"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Código *
                    </label>
                    <input 
                        v-model="cloneForm.code"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Ej: MI-PLANTILLA"
                    />
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        ⚠️ Se clonarán todos los semestres y asignaturas de la plantilla original.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    @click="closeCloneModal"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 transition"
                >
                    Cancelar
                </button>
                <button 
                    @click="executeClone"
                    :disabled="!cloneForm.name || !cloneForm.code || cloning"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
                >
                    <span v-if="cloning" class="animate-spin inline-block mr-2">⟳</span>
                    📋 Clonar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import Swal from 'sweetalert2';
import templatesApi from '@/api/academic/templates';

const router = useRouter();
const authStore = useAuthStore();

// =============================================
// COMPUTED - PERMISOS
// =============================================

const isAdmin = computed(() => {
    return authStore.isAdmin || authStore.isSuperAdmin;
});

const authUserId = computed(() => {
    return authStore.user?.id;
});

const canCreateTemplates = computed(() => {
    // Admin y Académico pueden crear plantillas
    return authStore.hasRole('admin') || 
           authStore.hasRole('super-admin') || 
           authStore.hasRole('academico');
});

const canEditTemplate = (template) => {
    // Admin puede editar todo, Académico solo sus propias plantillas
    if (authStore.hasRole('admin') || authStore.hasRole('super-admin')) {
        return true;
    }
    // Académico solo puede editar sus propias plantillas (no las predeterminadas)
    if (authStore.hasRole('academico')) {
        return template.created_by === authStore.user?.id && !template.is_default;
    }
    return false;
};

const canDeleteTemplate = (template) => {
    // Admin puede eliminar todo (excepto predeterminadas)
    if (authStore.hasRole('admin') || authStore.hasRole('super-admin')) {
        return !template.is_default;
    }
    // Académico solo puede eliminar sus propias plantillas (no predeterminadas)
    if (authStore.hasRole('academico')) {
        return template.created_by === authStore.user?.id && !template.is_default;
    }
    return false;
};

// =============================================
// STATE
// =============================================
const templates = ref([]);
const educationTypes = ref([]);
const loading = ref(false);
const cloning = ref(false);
const filters = ref({
    search: '',
    education_type_id: '',
    is_default: '',
    created_by: '',
});

const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0,
});

// Modal clonar
const showCloneModal = ref(false);
const cloneForm = ref({
    id: null,
    name: '',
    code: '',
});

// =============================================
// FUNCIONES
// =============================================

const loadEducationTypes = async () => {
    try {
        const response = await axios.get('/api/admin/education-types/all');
        educationTypes.value = response.data.types || [];
    } catch (error) {
        console.error('Error cargando tipos de enseñanza:', error);
    }
};

const loadTemplates = async () => {
    try {
        loading.value = true;
        
        // ✅ Construir params solo con valores válidos
        const params = {
            page: pagination.value.current_page,
        };
        
        // ✅ Solo agregar filtros si tienen valor
        if (filters.value.search) {
            params.search = filters.value.search;
        }
        
        if (filters.value.education_type_id) {
            params.education_type_id = filters.value.education_type_id;
        }
        
        if (filters.value.is_default !== '') {
            params.is_default = filters.value.is_default;
        }
        
        // ✅ Solo admin puede filtrar por creador
        if (isAdmin.value && filters.value.created_by) {
            params.created_by = filters.value.created_by;
        }
        
        console.log('📤 Enviando params:', params);
        
        const response = await templatesApi.getAll(params);
        
        templates.value = response.data.templates.data || [];
        pagination.value = {
            current_page: response.data.templates.current_page,
            last_page: response.data.templates.last_page,
            per_page: response.data.templates.per_page,
            total: response.data.templates.total,
            from: response.data.templates.from,
            to: response.data.templates.to,
        };
    } catch (error) {
        console.error('Error cargando plantillas:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las plantillas'
        });
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = {
        search: '',
        education_type_id: '',
        is_default: '',  // ✅ Valor vacío para "Todos"
        created_by: '',
    };
    pagination.value.current_page = 1;
    loadTemplates();
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadTemplates();
};



const cloneTemplate = (template) => {
    cloneForm.value = {
        id: template.id,
        name: `${template.name} (clonado)`,
        code: `${template.code}-CLONE`,
    };
    showCloneModal.value = true;
};

const closeCloneModal = () => {
    showCloneModal.value = false;
    cloneForm.value = {
        id: null,
        name: '',
        code: '',
    };
};

const executeClone = async () => {
    if (!cloneForm.value.name || !cloneForm.value.code) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Nombre y código son obligatorios'
        });
        return;
    }

    try {
        cloning.value = true;
        await templatesApi.clone(cloneForm.value.id, {
            name: cloneForm.value.name,
            code: cloneForm.value.code,
        });

        Swal.fire({
            icon: 'success',
            title: '✅ Plantilla clonada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        closeCloneModal();
        await loadTemplates();
    } catch (error) {
        console.error('Error clonando plantilla:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo clonar la plantilla'
        });
    } finally {
        cloning.value = false;
    }
};

const deleteTemplate = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar plantilla?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirm.isConfirmed) return;

    try {
        await templatesApi.delete(id);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Plantilla eliminada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        await loadTemplates();
    } catch (error) {
        console.error('Error eliminando plantilla:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar la plantilla'
        });
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadEducationTypes();
    loadTemplates();
});
</script>