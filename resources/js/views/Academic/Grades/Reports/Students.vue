<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">📊 Reporte de Estudiantes</h1>
            <p class="text-blue-100 mt-2">Análisis detallado de rendimiento por estudiante</p>
        </div>

        <!-- FILTROS -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Buscar estudiante</label>
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
                        <option value="graduated">Graduados</option>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estudiante</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cursos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Promedio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Asistencia</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-4 py-3">
                                <div class="text-gray-900 dark:text-white font-medium">{{ student.name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ student.email }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">
                                {{ student.code }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-center">
                                {{ student.total_courses }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span 
                                    class="font-bold"
                                    :class="getGradeClass(student.average_grade)"
                                >
                                    {{ student.average_grade || 0 }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span 
                                    class="font-bold"
                                    :class="getAttendanceClass(student.attendance_rate)"
                                >
                                    {{ student.attendance_rate || 0 }}%
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusClass(student.status)"
                                >
                                    {{ getStatusText(student.status) }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="students.length === 0 && !loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>No hay estudiantes para mostrar</p>
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

// =============================================
// STATE
// =============================================
const students = ref([]);
const loading = ref(false);
const filters = ref({
    search: '',
    status: '',
});

// =============================================
// FUNCIONES
// =============================================
const loadReport = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/academic/reports/students', {
            params: filters.value
        });
        students.value = response.data.students || [];
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

const getAttendanceClass = (rate) => {
    if (!rate) return 'text-gray-500';
    if (rate >= 80) return 'text-green-600 dark:text-green-400';
    if (rate >= 60) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
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
    loadReport();
});
</script>