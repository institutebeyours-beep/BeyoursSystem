<template>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">📖 Asignaturas</h1>
                    <p class="text-emerald-100 mt-2">Gestiona las asignaturas académicas</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                >
                    <span>➕</span> Nueva Asignatura
                </button>
            </div>
        </div>

        <!-- BÚSQUEDA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input 
                        v-model="filters.search"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Buscar por nombre o código..."
                        @input="loadSubjects"
                    />
                </div>
                <button 
                    @click="resetFilters"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                >
                    🔄 Limpiar
                </button>
                <button 
                    @click="loadSubjects"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-2"
                >
                    <span v-if="loading" class="animate-spin">⟳</span>
                    <span v-else>🔄</span> Actualizar
                </button>
            </div>
        </div>

        <!-- TABLA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Créditos</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cursos</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="subject in subjects" :key="subject.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                        <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">{{ subject.code }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ subject.name }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ subject.credits }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                :class="subject.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'">
                                {{ subject.is_active ? '✅ Activo' : '⬜ Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ subject.courses_count || 0 }} cursos
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEditModal(subject)" 
                                        class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition"
                                        title="Editar">✏️</button>
                                <button @click="deleteSubject(subject.id)" 
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
                                        title="Eliminar">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="subjects.length === 0 && !loading">
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                            <div class="text-4xl mb-2">📭</div>
                            <p>No hay asignaturas creadas</p>
                            <button @click="openCreateModal" class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm">
                                Crear la primera asignatura
                            </button>
                        </td>
                    </tr>
                    <tr v-if="loading">
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                            <span class="animate-spin inline-block">⟳</span> Cargando asignaturas...
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- PAGINACIÓN -->
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Mostrando {{ pagination.from || 0 }} - {{ pagination.to || 0 }} de {{ pagination.total || 0 }}
                </span>
                <div class="flex gap-2">
                    <button @click="changePage(pagination.current_page - 1)" 
                            :disabled="pagination.current_page <= 1"
                            class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">◀</button>
                    <span class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ pagination.current_page }} / {{ pagination.last_page }}
                    </span>
                    <button @click="changePage(pagination.current_page + 1)" 
                            :disabled="pagination.current_page >= pagination.last_page"
                            class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">▶</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: CREAR/EDITAR ASIGNATURA -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                {{ isEditing ? '✏️ Editar Asignatura' : '➕ Nueva Asignatura' }}
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                    <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Ej: Matemáticas I" />
                    <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código *</label>
                    <input v-model="form.code" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Ej: MAT101" />
                    <p v-if="errors.code" class="text-red-500 text-sm mt-1">{{ errors.code }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Créditos</label>
                    <input v-model.number="form.credits" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="0" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                    <textarea v-model="form.description" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Descripción de la asignatura"></textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" v-model="form.is_active" class="w-4 h-4 text-emerald-600 border-gray-300 rounded" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">Activo</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button @click="closeModal" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 transition">Cancelar</button>
                <button @click="saveSubject" :disabled="saving" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50">
                    <span v-if="saving" class="animate-spin inline-block mr-2">⟳</span>
                    💾 Guardar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import subjectsApi from '@/api/academic/subjects';

// =============================================
// STATE
// =============================================
const subjects = ref([]);
const loading = ref(false);
const saving = ref(false);
const filters = ref({ search: '' });
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0,
});

// Modal
const showModal = ref(false);
const isEditing = ref(false);
const form = ref({
    id: null,
    name: '',
    code: '',
    credits: 0,
    description: '',
    is_active: true,
});
const errors = ref({});

// =============================================
// FUNCIONES
// =============================================
const loadSubjects = async () => {
    try {
        loading.value = true;
        const response = await subjectsApi.getAll({
            page: pagination.value.current_page,
            search: filters.value.search,
        });
        
        subjects.value = response.data.data || [];
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to,
        };
    } catch (error) {
        console.error('Error cargando asignaturas:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las asignaturas'
        });
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadSubjects();
};

const resetFilters = () => {
    filters.value.search = '';
    pagination.value.current_page = 1;
    loadSubjects();
};

const openCreateModal = () => {
    isEditing.value = false;
    form.value = {
        id: null,
        name: '',
        code: '',
        credits: 0,
        description: '',
        is_active: true,
    };
    errors.value = {};
    showModal.value = true;
};

const openEditModal = (subject) => {
    isEditing.value = true;
    form.value = { ...subject };
    errors.value = {};
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveSubject = async () => {
    if (!form.value.name || !form.value.code) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Nombre y código son obligatorios'
        });
        return;
    }

    try {
        saving.value = true;
        errors.value = {};

        if (isEditing.value) {
            await subjectsApi.update(form.value.id, form.value);
            Swal.fire({
                icon: 'success',
                title: '✅ Asignatura actualizada',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            await subjectsApi.create(form.value);
            Swal.fire({
                icon: 'success',
                title: '✅ Asignatura creada',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        closeModal();
        
        // ✅ RECARGAR LA LISTA AUTOMÁTICAMENTE
        await loadSubjects();

    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'No se pudo guardar la asignatura'
            });
        }
    } finally {
        saving.value = false;
    }
};

const deleteSubject = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar asignatura?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirm.isConfirmed) return;

    try {
        await subjectsApi.delete(id);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Asignatura eliminada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        // ✅ RECARGAR LA LISTA AUTOMÁTICAMENTE
        await loadSubjects();

    } catch (error) {
        console.error('Error eliminando asignatura:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar la asignatura'
        });
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadSubjects();
});
</script>