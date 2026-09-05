<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">📚 Cursos</h1>
                    <p class="text-emerald-100 mt-2">
                        Gestiona los cursos académicos
                        <span v-if="selectedCareer" class="block text-sm text-emerald-200 mt-1">
                            📌 {{ getCareerName(selectedCareer) }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <router-link 
                        v-if="selectedCareer"
                        to="/academic/courses/create"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        <span>➕</span> Nuevo Curso
                    </router-link>
                    <button 
                        @click="loadCourses"
                        :disabled="loading || !selectedCareer"
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
                <!-- ✅ PRIMERO: Seleccionar Carrera (Obligatorio) -->
                <div class="min-w-[200px] flex-1">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        🎓 Carrera *
                    </label>
                    <select 
                        v-model="selectedCareer"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="onCareerChange"
                    >
                        <option value="">Selecciona una carrera</option>
                        <option 
                            v-for="career in careers" 
                            :key="career.id" 
                            :value="career.id"
                        >
                            {{ career.name }} ({{ career.code }})
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
                        @input="loadCourses"
                        :disabled="!selectedCareer"
                    />
                </div>

                <!-- Estado -->
                <div class="min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        📊 Estado
                    </label>
                    <select 
                        v-model="filters.status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadCourses"
                        :disabled="!selectedCareer"
                    >
                        <option value="">Todos</option>
                        <option value="active">✅ Activo</option>
                        <option value="inactive">⬜ Inactivo</option>
                        <option value="completed">📌 Completado</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button 
                        @click="resetFilters"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                        :disabled="!selectedCareer"
                    >
                        🔄 Limpiar
                    </button>
                </div>
            </div>

            <!-- Mensaje si no hay carrera seleccionada -->
            <div v-if="!selectedCareer" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    ⚠️ Selecciona una carrera para ver sus cursos
                </p>
            </div>
        </div>

        <!-- TABLA DE CURSOS -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Curso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Asignaturas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Créditos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cupo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="course in courses" :key="course.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">
                                {{ course.code }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-900 dark:text-white font-medium">{{ course.name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ course.description }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span 
                                        v-for="subject in course.subjects" 
                                        :key="subject.id"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400"
                                        :title="subject.code"
                                    >
                                        {{ subject.name }}
                                        <button 
                                            @click.stop="removeSubjectFromCourse(course.id, subject.id)"
                                            class="ml-1 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                            title="Eliminar asignatura del curso"
                                        >
                                            ×
                                        </button>
                                    </span>
                                    <button 
                                        @click="openAddSubjectModal(course.id)"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-600 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition"
                                    >
                                        +
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ course.total_credits || 0 }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                <span :class="getCapacityClass(course)">
                                    {{ course.students_count || 0 }}/{{ course.capacity }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusClass(course.status)"
                                >
                                    {{ getStatusText(course.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <router-link 
                                        :to="`/academic/courses/${course.id}`"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded-lg transition text-xs"
                                        title="Ver detalles"
                                    >
                                        📋 Detalles
                                    </router-link>
                                    <router-link 
                                        :to="`/academic/courses/${course.id}/edit`"
                                        class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition"
                                        title="Editar"
                                    >
                                        ✏️
                                    </router-link>
                                    <button 
                                        @click="deleteCourse(course.id)"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
                                        title="Eliminar"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="courses.length === 0 && !loading && selectedCareer">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>No hay cursos para esta carrera</p>
                                <router-link 
                                    v-if="selectedCareer"
                                    to="/academic/courses/create"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm"
                                >
                                    Crear el primer curso
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="!selectedCareer && !loading">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">🎓</div>
                                <p>Selecciona una carrera para ver sus cursos</p>
                            </td>
                        </tr>
                        <tr v-if="loading">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <span class="animate-spin inline-block">⟳</span> Cargando cursos...
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
                        :disabled="pagination.current_page <= 1 || !selectedCareer"
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        ◀
                    </button>
                    <span class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ pagination.current_page }} / {{ pagination.last_page }}
                    </span>
                    <button 
                        @click="changePage(pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page || !selectedCareer"
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        ▶
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: AGREGAR ASIGNATURA AL CURSO          -->
    <!-- ========================================== -->
    <div v-if="showAddSubjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                ➕ Agregar Asignatura al Curso
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Asignatura *
                    </label>
                    <select 
                        v-model="selectedSubjectId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <option value="">Selecciona una asignatura</option>
                        <option 
                            v-for="subject in availableSubjects" 
                            :key="subject.id" 
                            :value="subject.id"
                        >
                            {{ subject.name }} ({{ subject.code }}) - {{ subject.credits || 0 }} créditos
                        </option>
                    </select>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        ⚠️ Las asignaturas que no aparecen en la lista ya están asignadas a este curso.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    @click="closeAddSubjectModal"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 transition"
                >
                    Cancelar
                </button>
                <button 
                    @click="addSubjectToCourse"
                    :disabled="!selectedSubjectId || addingSubject"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
                >
                    <span v-if="addingSubject" class="animate-spin inline-block mr-2">⟳</span>
                    ✅ Agregar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';  // ✅ watch importado
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// =============================================
// STATE
// =============================================
const courses = ref([]);
const careers = ref([]);
const selectedCareer = ref('');
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

// Modal: Agregar Asignatura
const showAddSubjectModal = ref(false);
const currentCourseId = ref(null);
const selectedSubjectId = ref('');
const addingSubject = ref(false);
const allSubjects = ref([]);

// =============================================
// COMPUTED
// =============================================

const availableSubjects = computed(() => {
    const currentCourse = courses.value.find(c => c.id === currentCourseId.value);
    if (!currentCourse) return allSubjects.value;
    const assignedIds = currentCourse.subjects?.map(s => s.id) || [];
    return allSubjects.value.filter(s => !assignedIds.includes(s.id));
});

const getCareerName = (id) => {
    const career = careers.value.find(c => c.id === id);
    return career ? career.name : '';
};

// =============================================
// FUNCIONES
// =============================================

const loadCareers = async () => {
    try {
        const response = await axios.get('/api/academic/careers/all');
        careers.value = response.data.careers || [];
    } catch (error) {
        console.error('Error cargando carreras:', error);
        careers.value = [];
    }
};

const loadCourses = async () => {
    if (!selectedCareer.value) {
        courses.value = [];
        pagination.value.total = 0;
        return;
    }

    try {
        loading.value = true;
        
        const params = {
            page: pagination.value.current_page,
            career_id: selectedCareer.value,
            with_subjects: true,
        };
        
        if (filters.value.search) {
            params.search = filters.value.search;
        }
        
        if (filters.value.status) {
            params.status = filters.value.status;
        }
        
        const response = await axios.get('/api/academic/courses', { params });
        
        courses.value = response.data.data || [];
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to,
        };
    } catch (error) {
        console.error('Error cargando cursos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los cursos'
        });
    } finally {
        loading.value = false;
    }
};

const loadAllSubjects = async () => {
    try {
        const response = await axios.get('/api/academic/subjects/all');
        allSubjects.value = response.data.subjects || [];
    } catch (error) {
        console.error('Error cargando asignaturas:', error);
        allSubjects.value = [];
    }
};

const onCareerChange = () => {
    pagination.value.current_page = 1;
    filters.value.search = '';
    loadCourses();
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadCourses();
};

const resetFilters = () => {
    filters.value = {
        search: '',
        status: '',
    };
    pagination.value.current_page = 1;
    loadCourses();
};

const deleteCourse = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar curso?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirm.isConfirmed) return;

    try {
        await axios.delete(`/api/academic/courses/${id}`);
        await loadCourses();
        Swal.fire({
            icon: 'success',
            title: '✅ Curso eliminado',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar el curso'
        });
    }
};

const openAddSubjectModal = (courseId) => {
    currentCourseId.value = courseId;
    selectedSubjectId.value = '';
    showAddSubjectModal.value = true;
};

const closeAddSubjectModal = () => {
    showAddSubjectModal.value = false;
    currentCourseId.value = null;
    selectedSubjectId.value = '';
};

const addSubjectToCourse = async () => {
    if (!selectedSubjectId.value || !currentCourseId.value) return;

    try {
        addingSubject.value = true;
        
        await axios.post('/api/academic/courses/assign-subject', {
            course_id: currentCourseId.value,
            subject_id: selectedSubjectId.value,
        });

        Swal.fire({
            icon: 'success',
            title: '✅ Asignatura agregada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        closeAddSubjectModal();
        await loadCourses();
    } catch (error) {
        console.error('Error agregando asignatura:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo agregar la asignatura'
        });
    } finally {
        addingSubject.value = false;
    }
};

const removeSubjectFromCourse = async (courseId, subjectId) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar asignatura?',
        text: 'Esta acción eliminará la asignatura del curso',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirm.isConfirmed) return;

    try {
        await axios.delete(`/api/academic/courses/${courseId}/subjects/${subjectId}`);
        Swal.fire({
            icon: 'success',
            title: '✅ Asignatura eliminada del curso',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        await loadCourses();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar la asignatura'
        });
    }
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
        completed: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    };
    return classes[status] || classes.inactive;
};

const getStatusText = (status) => {
    const texts = {
        active: '✅ Activo',
        inactive: '⬜ Inactivo',
        completed: '📌 Completado',
    };
    return texts[status] || status;
};

const getCapacityClass = (course) => {
    const percentage = (course.students_count || 0) / Math.max(course.capacity, 1) * 100;
    if (percentage >= 100) return 'text-red-600 dark:text-red-400 font-bold';
    if (percentage >= 80) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-green-600 dark:text-green-400';
};

// =============================================
// WATCH
// =============================================

watch(() => route.query.career_id, (newVal) => {
    if (newVal) {
        selectedCareer.value = parseInt(newVal);
        loadCourses();
    }
});

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadCareers();
    loadAllSubjects();
    
    if (route.query.career_id) {
        selectedCareer.value = parseInt(route.query.career_id);
        loadCourses();
    }
});
</script>