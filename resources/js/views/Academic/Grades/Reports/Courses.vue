<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">📊 Reporte de Cursos</h1>
            <p class="text-purple-100 mt-2">Análisis de rendimiento por curso</p>
        </div>

        <!-- FILTROS -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Buscar curso</label>
                    <input 
                        v-model="filters.search"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Nombre o código..."
                        @input="loadReport"
                    />
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Estado</label>
                    <select 
                        v-model="filters.status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        @change="loadReport"
                    >
                        <option value="">Todos</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                        <option value="completed">Completados</option>
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

        <!-- TABLA DE REPORTE -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Curso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estudiantes</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cupo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Promedio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="course in courses" :key="course.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3">
                                <div class="text-gray-900 dark:text-white font-medium">{{ course.name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ course.teacher || 'Sin profesor' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">
                                {{ course.code }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-center">
                                {{ course.total_students || 0 }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-center">
                                <span :class="getCapacityClass(course)">
                                    {{ course.total_students || 0 }}/{{ course.capacity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span 
                                    class="font-bold"
                                    :class="getGradeClass(course.average_grade)"
                                >
                                    {{ course.average_grade || 0 }}%
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
                        </tr>
                        <tr v-if="courses.length === 0 && !loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>No hay cursos para mostrar</p>
                            </td>
                        </tr>
                        <tr v-if="loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <span class="animate-spin inline-block">⟳</span> Cargando datos...
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

const courses = ref([]);
const loading = ref(false);
const filters = ref({
    search: '',
    status: '',
});

const loadReport = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/academic/reports/courses', {
            params: filters.value
        });
        courses.value = response.data.courses || [];
    } catch (error) {
        console.error('Error cargando reporte:', error);
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', status: '' };
    loadReport();
};

const getGradeClass = (grade) => {
    if (!grade) return 'text-gray-500';
    if (grade >= 80) return 'text-green-600 dark:text-green-400';
    if (grade >= 60) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
};

const getCapacityClass = (course) => {
    const percentage = (course.total_students || 0) / course.capacity * 100;
    if (percentage >= 100) return 'text-red-600 dark:text-red-400 font-bold';
    if (percentage >= 80) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-green-600 dark:text-green-400';
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

onMounted(() => {
    loadReport();
});
</script>