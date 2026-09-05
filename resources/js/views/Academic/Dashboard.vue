<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">📊 Dashboard Académico</h1>
                    <p class="text-emerald-100 mt-2">Visión general del rendimiento académico</p>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        @click="loadStats" 
                        :disabled="loading"
                        class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        <span v-else>🔄</span>
                        {{ loading ? 'Cargando...' : 'Actualizar' }}
                    </button>
                    <div class="bg-white/10 px-4 py-2 rounded-lg">
                        <span class="text-sm">📚 Académico</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ESTADÍSTICAS RÁPIDAS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-2xl">
                        📚
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Cursos Activos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.active_courses || 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total: {{ stats.total_courses || 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-2xl">
                        👨‍🎓
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Estudiantes Activos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.active_students || 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total: {{ stats.total_students || 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-2xl">
                        📊
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Promedio General</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.average_grade || 0 }}%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Inscripciones: {{ stats.enrollments_this_month || 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-2xl">
                        📋
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Asistencia</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ attendanceRate }}%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Presentes: {{ stats.attendance_stats?.present || 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICAS Y DETALLES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Cursos Populares -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🏆 Cursos con más estudiantes</h3>
                <div v-if="stats.top_courses?.length > 0" class="space-y-3">
                    <div 
                        v-for="(item, index) in stats.top_courses" 
                        :key="index"
                        class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-gray-400">{{ index + 1 }}.</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ item.course?.name || 'Sin nombre' }}</span>
                        </div>
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ item.total }} estudiantes</span>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-400 text-center py-4">No hay datos disponibles</p>
            </div>

            <!-- Estudiantes con Bajo Rendimiento -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚠️ Estudiantes con bajo rendimiento</h3>
                <div v-if="stats.low_performance?.length > 0" class="space-y-3">
                    <div 
                        v-for="(item, index) in stats.low_performance" 
                        :key="index"
                        class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-gray-400">{{ index + 1 }}.</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ item.student?.full_name || 'Sin nombre' }}</span>
                        </div>
                        <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ item.average || 0 }}%</span>
                    </div>
                </div>
                <p v-else class="text-sm text-green-400 text-center py-4">✅ No hay estudiantes con bajo rendimiento</p>
            </div>

            <!-- Últimos Estudiantes -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🆕 Últimos estudiantes registrados</h3>
                <div v-if="stats.recent_students?.length > 0" class="space-y-3">
                    <div 
                        v-for="student in stats.recent_students" 
                        :key="student.id"
                        class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ student.user?.name || 'Sin nombre' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ student.code }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ formatDate(student.created_at) }}</span>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-400 text-center py-4">No hay estudiantes registrados</p>
            </div>

            <!-- Cursos con Cupo Lleno -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🔴 Cursos con cupo lleno</h3>
                <div v-if="stats.full_courses?.length > 0" class="space-y-3">
                    <div 
                        v-for="course in stats.full_courses" 
                        :key="course.id"
                        class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ course.name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ course.code }}</p>
                        </div>
                        <span class="text-xs font-bold text-red-600 dark:text-red-400">
                            {{ course.students_count || 0 }}/{{ course.capacity }}
                        </span>
                    </div>
                </div>
                <p v-else class="text-sm text-green-400 text-center py-4">✅ No hay cursos con cupo lleno</p>
            </div>
        </div>

        <!-- ESTADÍSTICAS DE ASISTENCIA -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Resumen de Asistencia</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.attendance_stats?.total || 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.attendance_stats?.present || 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">✅ Presentes</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.attendance_stats?.absent || 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">❌ Ausentes</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.attendance_stats?.justified || 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">📝 Justificados</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import moment from 'moment';

// =============================================
// STATE
// =============================================
const stats = ref({});
const loading = ref(false);

// =============================================
// COMPUTED
// =============================================
const attendanceRate = computed(() => {
    const total = stats.value.attendance_stats?.total || 0;
    const present = stats.value.attendance_stats?.present || 0;
    if (total === 0) return 0;
    return Math.round((present / total) * 100);
});

// =============================================
// FUNCIONES
// =============================================
const loadStats = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/academic/dashboard/stats');
        stats.value = response.data;
        console.log('📊 Estadísticas académicas cargadas:', stats.value);
    } catch (error) {
        console.error('❌ Error cargando estadísticas:', error);
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return '-';
    return moment(date).format('DD/MM/YYYY');
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadStats();
});
</script>