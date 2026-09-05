<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">📝 Calificaciones</h1>
                    <p class="text-indigo-100 mt-2">Lista de calificaciones registradas</p>
                </div>
                <router-link 
                    to="/academic/grades/register"
                    class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                >
                    <span>➕</span> Registrar Notas
                </router-link>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <select 
                        v-model="filters.course_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadGrades"
                    >
                        <option value="">Todos los cursos</option>
                        <option v-for="course in courses" :key="course.id" :value="course.id">
                            {{ course.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <select 
                        v-model="filters.partial"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadGrades"
                    >
                        <option value="">Todas las parciales</option>
                        <option value="1">Parcial 1</option>
                        <option value="2">Parcial 2</option>
                        <option value="3">Parcial 3</option>
                        <option value="final">Final</option>
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

        <!-- TABLA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estudiante</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Curso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Parcial</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nota</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Final</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="grade in grades" :key="grade.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ grade.student?.full_name || 'Sin nombre' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ grade.course?.name || 'Sin curso' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-center">{{ grade.partial }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold" :class="getGradeClass(grade.grade)">
                                    {{ grade.grade || '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">{{ grade.grade_final || '-' }}</td>
                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusClass(grade.grade_final)"
                                >
                                    {{ getStatusText(grade.grade_final) }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="grades.length === 0 && !loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <div class="text-4xl mb-2">📭</div>
                                <p>No hay calificaciones registradas</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const grades = ref([]);
const courses = ref([]);
const loading = ref(false);
const filters = ref({ course_id: '', partial: '' });

const loadGrades = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/academic/grades', { params: filters.value });
        grades.value = response.data.data || [];
    } catch (error) {
        console.error('Error:', error);
    } finally {
        loading.value = false;
    }
};

const loadCourses = async () => {
    try {
        const response = await axios.get('/academic/courses?per_page=100');
        courses.value = response.data.data || [];
    } catch (error) {
        console.error('Error:', error);
    }
};

const resetFilters = () => {
    filters.value = { course_id: '', partial: '' };
    loadGrades();
};

const getGradeClass = (grade) => {
    if (!grade) return 'text-gray-400';
    if (grade >= 80) return 'text-green-600 dark:text-green-400';
    if (grade >= 60) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
};

const getStatusClass = (grade) => {
    if (!grade) return 'bg-gray-100 text-gray-700';
    if (grade >= 60) return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
    return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
};

const getStatusText = (grade) => {
    if (!grade) return '⏳ Pendiente';
    if (grade >= 60) return '✅ Aprobado';
    return '❌ Reprobado';
};

onMounted(() => {
    loadCourses();
    loadGrades();
});
</script>