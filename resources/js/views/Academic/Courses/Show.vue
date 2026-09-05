<template>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black">{{ course?.name || 'Cargando...' }}</h1>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-mono">
                            {{ course?.code }}
                        </span>
                    </div>
                    <p class="text-emerald-100 mt-2">{{ course?.description || 'Sin descripción' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <router-link 
                        :to="`/academic/courses/${course?.id}/edit`"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        ✏️ Editar
                    </router-link>
                    <button 
                        @click="deleteCourse"
                        class="bg-red-500/20 hover:bg-red-500/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        🗑️ Eliminar
                    </button>
                    <router-link 
                        to="/academic/courses"
                        class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        📋 Volver
                    </router-link>
                </div>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div v-if="loading" class="text-center py-12">
            <span class="animate-spin inline-block text-4xl">⟳</span>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando curso...</p>
        </div>

        <div v-else-if="course" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- COLUMNA IZQUIERDA -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Estadísticas -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 Estadísticas</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">👨‍🎓</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_students || 0 }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Estudiantes</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">📈</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.average_grade || 0 }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Promedio</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">🎯</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.capacity_used || 0 }}%</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Capacidad usada</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">📚</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ course.subjects?.length || 0 }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Asignaturas</p>
                        </div>
                    </div>
                </div>

                <!-- ✅ NUEVA SECCIÓN: CRÉDITOS DEL CURSO -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🎯 Créditos del Curso</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ course.total_credits || 0 }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Usados</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ course.used_credits || 0 }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Disponibles</p>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                {{ course.available_credits || 0 }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Progreso</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ course.credits_progress || 0 }}%
                            </p>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                            <div 
                                class="h-4 rounded-full transition-all duration-500"
                                :class="course.credits_progress >= 100 ? 'bg-emerald-500' : 'bg-purple-500'"
                                :style="{ width: Math.min(course.credits_progress || 0, 100) + '%' }"
                            ></div>
                        </div>
                        <div class="flex justify-between mt-1 text-xs text-gray-500">
                            <span>0%</span>
                            <span v-if="course.credits_progress >= 100">✅ Curso completo</span>
                            <span v-else-if="course.total_credits > 0">Faltan {{ course.available_credits }} créditos</span>
                            <span v-else>Sin créditos definidos</span>
                            <span>100%</span>
                        </div>
                    </div>

                    <!-- Lista de asignaturas con créditos -->
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📖 Asignaturas asignadas ({{ course.subjects?.length || 0 }})
                        </p>
                        <div class="space-y-1">
                            <div v-for="subject in course.subjects" :key="subject.id"
                                class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>{{ subject.name }}</span>
                                <span class="font-medium">{{ subject.credits || 0 }} créditos</span>
                            </div>
                            <div v-if="!course.subjects?.length" class="text-sm text-gray-400">
                                No hay asignaturas asignadas
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ SECCIÓN: CARGA ACADÉMICA -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🎯 Carga Académica</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tipo de curso</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ getCourseTypeLabel(course.course_type) }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Clase / semana</p>
                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ course.class_hours_per_week || 0 }}h
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Estudio / semana</p>
                            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                                {{ course.study_hours_per_week || 0 }}h
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total / semana</p>
                            <p class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                {{ course.total_hours_per_week || 0 }}h
                            </p>
                        </div>
                    </div>

                    <!-- Detalle de laboratorio (si aplica) -->
                    <div v-if="course.lab_hours_per_week > 0" class="mt-3 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <p class="text-sm text-purple-700 dark:text-purple-300">
                            🧪 <span class="font-medium">{{ course.lab_hours_per_week }}h</span> de laboratorio por semana
                        </p>
                    </div>

                    <!-- Resumen de cargas -->
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">
                                📚 <span class="font-medium">{{ course.credits || 0 }}</span> créditos
                            </span>
                            <span class="text-gray-600 dark:text-gray-400">
                                📅 <span class="font-medium">{{ course.total_weeks || 16 }}</span> semanas
                            </span>
                            <span class="text-gray-600 dark:text-gray-400">
                                ⏱️ <span class="font-medium">{{ course.total_hours || 0 }}</span> horas totales
                            </span>
                        </div>
                    </div>

                    <!-- Proporciones -->
                    <div class="mt-2 text-xs text-gray-400 flex gap-4">
                        <span>📖 Estudio: 1:{{ course.study_ratio || 0 }}</span>
                        <span v-if="course.lab_ratio > 0">🧪 Laboratorio: 1:{{ course.lab_ratio || 0 }}</span>
                    </div>
                </div>

                <!-- Información General -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Información General</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Créditos</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ course.credits || 0 }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Duración</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ course.duration || 'No especificada' }} horas</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                :class="getStatusClass(course.status)">
                                {{ getStatusText(course.status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cupo</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ course.capacity || 0 }} estudiantes</p>
                        </div>
                        <div v-if="course.schedule" class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Horario</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ formatSchedule(course.schedule) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Asignaturas -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">📖 Asignaturas</h3>
                        <button 
                            @click="openAddSubjectModal"
                            class="px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm flex items-center gap-1"
                        >
                            ➕ Agregar
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div v-for="subject in course.subjects" :key="subject.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ subject.name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ subject.code }}</span>
                                <span class="text-xs text-purple-600 dark:text-purple-400 ml-2">
                                    {{ subject.credits || 0 }} créditos
                                </span>
                            </div>
                            <button 
                                @click="removeSubject(subject.id)"
                                class="text-red-500 hover:text-red-700 transition"
                            >
                                ✕
                            </button>
                        </div>
                        <div v-if="!course.subjects?.length" class="text-center py-4 text-gray-400">
                            No hay asignaturas asignadas
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA -->
            <div class="space-y-6">
                <!-- Configuración de Calificaciones -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚙️ Calificaciones</h3>
                    <div v-if="gradeConfigurations.length > 0" class="space-y-2">
                        <div v-for="config in gradeConfigurations" :key="config.id"
                            class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ config.name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ config.components?.length || 0 }} componentes
                            </p>
                        </div>
                    </div>
                    <div v-else class="text-center py-4 text-gray-400">
                        <p class="text-sm">Sin configuración</p>
                    </div>
                    <router-link 
                        :to="`/academic/grades/configurations?course=${course.id}`"
                        class="mt-3 block text-center text-sm text-purple-600 hover:text-purple-700 transition"
                    >
                        ⚙️ Configurar calificaciones
                    </router-link>
                </div>

                <!-- Acciones Rápidas -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🚀 Acciones Rápidas</h3>
                    <div class="space-y-2">
                        <router-link 
                            :to="`/academic/grades?course=${course.id}`"
                            class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm text-center"
                        >
                            📝 Registrar Notas
                        </router-link>
                        <router-link 
                            :to="`/academic/grades/reports/courses?course=${course.id}`"
                            class="block w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm text-center"
                        >
                            📊 Ver Reportes
                        </router-link>
                        <router-link 
                            :to="`/academic/students?course=${course.id}`"
                            class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm text-center"
                        >
                            👨‍🎓 Ver Estudiantes
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: AGREGAR ASIGNATURA -->
        <div v-if="showAddSubjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    ➕ Agregar Asignatura
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

                    <!-- ✅ Mostrar créditos disponibles -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            📊 Créditos disponibles: <strong>{{ course?.available_credits || 0 }}</strong>
                        </p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                            Total: {{ course?.total_credits || 0 }} · Usados: {{ course?.used_credits || 0 }}
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
                        @click="addSubject"
                        :disabled="!selectedSubjectId || addingSubject"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
                    >
                        <span v-if="addingSubject" class="animate-spin inline-block mr-2">⟳</span>
                        ✅ Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();
const route = useRoute();

// =============================================
// STATE
// =============================================
const course = ref(null);
const loading = ref(true);
const stats = ref({
    total_students: 0,
    average_grade: 0,
    capacity_used: 0,
});
const gradeConfigurations = ref([]);
const allSubjects = ref([]);

// Modal: Agregar Asignatura
const showAddSubjectModal = ref(false);
const selectedSubjectId = ref('');
const addingSubject = ref(false);

// =============================================
// COMPUTED
// =============================================
const availableSubjects = computed(() => {
    if (!course.value) return allSubjects.value;
    const assignedIds = course.value.subjects?.map(s => s.id) || [];
    return allSubjects.value.filter(s => !assignedIds.includes(s.id));
});

// =============================================
// FUNCIONES
// =============================================
const loadCourse = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/academic/courses/${route.params.id}`);
        course.value = response.data.course;
        stats.value = response.data.stats || { total_students: 0, average_grade: 0, capacity_used: 0 };
        gradeConfigurations.value = course.value.grade_configurations || [];
    } catch (error) {
        console.error('Error cargando curso:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar el curso'
        }).then(() => {
            router.push('/academic/courses');
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
    }
};

const deleteCourse = async () => {
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
        await axios.delete(`/api/academic/courses/${course.value.id}`);
        Swal.fire({
            icon: 'success',
            title: '✅ Curso eliminado',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        router.push('/academic/courses');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar el curso'
        });
    }
};

const openAddSubjectModal = () => {
    selectedSubjectId.value = '';
    showAddSubjectModal.value = true;
};

const closeAddSubjectModal = () => {
    showAddSubjectModal.value = false;
    selectedSubjectId.value = '';
};

const addSubject = async () => {
    if (!selectedSubjectId.value) return;

    try {
        addingSubject.value = true;
        await axios.post('/api/academic/courses/assign-subject', {
            course_id: course.value.id,
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
        await loadCourse();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo agregar la asignatura'
        });
    } finally {
        addingSubject.value = false;
    }
};

const removeSubject = async (subjectId) => {
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
        await axios.delete(`/api/academic/courses/${course.value.id}/subjects/${subjectId}`);
        Swal.fire({
            icon: 'success',
            title: '✅ Asignatura eliminada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        await loadCourse();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo eliminar la asignatura'
        });
    }
};

const getCourseTypeLabel = (type) => {
    const types = {
        'theoretical': '📖 Teórico',
        'theoretical_practical': '📖🔬 Teórico-Práctico',
        'practical': '🔬 Práctico (Laboratorio)',
        'specialized_lab': '🧪 Laboratorio Especializado'
    };
    return types[type] || 'No definido';
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

const formatSchedule = (schedule) => {
    if (!schedule) return 'No especificado';
    if (typeof schedule === 'string') return schedule;
    const days = schedule.days?.join(', ') || '';
    const time = schedule.start_time && schedule.end_time 
        ? `${schedule.start_time} - ${schedule.end_time}` 
        : '';
    return `${days} ${time}`.trim() || 'No especificado';
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadCourse();
    loadAllSubjects();
});
</script>