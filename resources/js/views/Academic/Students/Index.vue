<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">👨‍🎓 Estudiantes</h1>
                    <p class="text-blue-100 mt-2">Gestiona los estudiantes del sistema</p>
                </div>
                <div class="flex items-center gap-3">
                    <router-link 
                        to="/academic/students/create"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        <span>➕</span> Nuevo Estudiante
                    </router-link>
                    <button 
                        @click="loadStudents"
                        :disabled="loading"
                        class="bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg transition text-sm"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        <span v-else>🔄</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- FILTROS Y BÚSQUEDA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input 
                        v-model="filters.search"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Buscar por nombre, email o código..."
                        @input="loadStudents"
                    />
                </div>
                <div class="min-w-[150px]">
                    <select 
                        v-model="filters.status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadStudents"
                    >
                        <option value="">Todos los estados</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                        <option value="graduated">Graduado</option>
                        <option value="suspended">Suspendido</option>
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

        <!-- TABLA DE ESTUDIANTES -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Teléfono</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3 text-gray-900 dark:text-white font-mono text-xs">
                                {{ student.code }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-900 dark:text-white font-medium">{{ student.user?.name || 'Sin nombre' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                {{ student.user?.email || '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                {{ student.phone || '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusClass(student.status)"
                                >
                                    {{ getStatusText(student.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <router-link 
                                        :to="`/academic/students/${student.id}`"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition"
                                        title="Ver"
                                    >
                                        👁️
                                    </router-link>
                                    <router-link 
                                        :to="`/academic/students/${student.id}/edit`"
                                        class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition"
                                        title="Editar"
                                    >
                                        ✏️
                                    </router-link>
                                    <button 
                                        @click="deleteStudent(student.id)"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
                                        title="Eliminar"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="students.length === 0 && !loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>No hay estudiantes disponibles</p>
                                <router-link 
                                    to="/academic/students/create"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm"
                                >
                                    Crear el primer estudiante
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <span class="animate-spin inline-block">⟳</span> Cargando estudiantes...
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
import Swal from 'sweetalert2';

// =============================================
// STATE
// =============================================
const students = ref([]);
const loading = ref(false);
const filters = ref({
    search: '',
    status: '',
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
const loadStudents = async () => {
    loading.value = true;
    try {
        // ✅ CORREGIDO: AGREGAR /api/
        const response = await axios.get('/api/academic/students', {
            params: {
                page: pagination.value.current_page,
                search: filters.value.search,
                status: filters.value.status,
                per_page: pagination.value.per_page,
            }
        });
        
        students.value = response.data.data || [];
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to,
        };
    } catch (error) {
        console.error('Error cargando estudiantes:', error);
        // ✅ Mostrar error al usuario
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los estudiantes',
        });
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadStudents();
};

const resetFilters = () => {
    filters.value = { search: '', status: '' };
    pagination.value.current_page = 1;
    loadStudents();
};

const deleteStudent = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar estudiante?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;
    
    try {
        // ✅ CORREGIDO: AGREGAR /api/
        await axios.delete(`/api/academic/students/${id}`);
        await loadStudents();
        Swal.fire({
            icon: 'success',
            title: '✅ Estudiante eliminado',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar el estudiante',
        });
    }
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
        graduated: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        suspended: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    };
    return classes[status] || classes.inactive;
};

const getStatusText = (status) => {
    const texts = {
        active: '✅ Activo',
        inactive: '⬜ Inactivo',
        graduated: '🎓 Graduado',
        suspended: '⛔ Suspendido',
    };
    return texts[status] || status;
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadStudents();
});
</script>