<template>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black">{{ career?.name || 'Cargando...' }}</h1>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-mono">
                            {{ career?.code }}
                        </span>
                    </div>
                    <p class="text-emerald-100 mt-2">{{ career?.description || 'Sin descripción' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <router-link 
                        :to="`/academic/careers/${career?.id}/edit`"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        ✏️ Editar
                    </router-link>
                    <router-link 
                        to="/academic/careers"
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
            <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando carrera...</p>
        </div>

        <div v-else-if="career" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- COLUMNA IZQUIERDA -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Estadísticas -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 Estadísticas</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">📚</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_courses || 0 }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Cursos</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">👨‍🎓</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_students || 0 }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Estudiantes</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">🎯</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.credits_progress || 0 }}%</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Progreso créditos</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                            <span class="text-3xl block mb-1">📖</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ career.total_credits || 0 }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Créditos totales</p>
                        </div>
                    </div>
                </div>

              <!-- Información General -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Información General</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tipo de Enseñanza</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ career.education_type?.name || 'No especificado' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Créditos totales</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ career.total_credits || 0 }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Duración</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ career.duration_years || 0 }} años 
                    <span v-if="career.duration_semesters">({{ career.duration_semesters }} semestres)</span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                <span 
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                    :class="career.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'"
                >
                    {{ career.is_active ? '✅ Activa' : '⬜ Inactiva' }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Horas teóricas</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ career.theoretical_hours || 0 }} horas</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Horas prácticas</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ career.practical_hours || 0 }} horas</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total de horas</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ (career.theoretical_hours || 0) + (career.practical_hours || 0) }} horas
                    <span class="text-xs text-gray-400 ml-2">
                        ({{ career.theoretical_hours || 0 }}h teóricas + {{ career.practical_hours || 0 }}h prácticas)
                    </span>
                </p>
            </div>
        </div>
    </div>

                <!-- Cursos de la carrera -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">📚 Cursos de la Carrera</h3>
                        <router-link 
                            :to="`/academic/courses?career_id=${career.id}`"
                            class="text-sm text-emerald-600 hover:text-emerald-700 transition"
                        >
                            Ver todos →
                        </router-link>
                    </div>
                    <div class="space-y-2">
                        <div v-for="course in career.courses" :key="course.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ course.name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ course.code }}</span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ course.total_credits || 0 }} créditos</span>
                        </div>
                        <div v-if="!career.courses?.length" class="text-center py-4 text-gray-400">
                            No hay cursos asociados a esta carrera
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA -->
            <div class="space-y-6">
                <!-- Resumen de Créditos -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🎯 Resumen de Créditos</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ career.total_credits || 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Usados</span>
                            <span class="font-medium text-blue-600 dark:text-blue-400">{{ stats.used_credits || 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Disponibles</span>
                            <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ stats.available_credits || 0 }}</span>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div 
                                class="h-3 rounded-full transition-all duration-500 bg-emerald-500"
                                :style="{ width: Math.min(stats.credits_progress || 0, 100) + '%' }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">
                            {{ stats.credits_progress || 0 }}% completado
                        </p>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🚀 Acciones Rápidas</h3>
                    <div class="space-y-2">
                        <router-link 
                            :to="`/academic/courses?career_id=${career.id}`"
                            class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm text-center"
                        >
                            📚 Ver Cursos
                        </router-link>
                        <router-link 
                            :to="`/academic/students?career_id=${career.id}`"
                            class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm text-center"
                        >
                            👨‍🎓 Ver Estudiantes
                        </router-link>
                        <router-link 
                            :to="`/academic/careers/${career.id}/edit`"
                            class="block w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm text-center"
                        >
                            ✏️ Editar Carrera
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import careersApi from '@/api/academic/careers';

const router = useRouter();
const route = useRoute();

const career = ref(null);
const stats = ref({
    total_courses: 0,
    total_students: 0,
    used_credits: 0,
    available_credits: 0,
    credits_progress: 0,
});
const loading = ref(true);

const loadCareer = async () => {
    try {
        loading.value = true;
        const response = await careersApi.getById(route.params.id);
        career.value = response.data.career;
        stats.value = response.data.stats || {
            total_courses: 0,
            total_students: 0,
            used_credits: 0,
            available_credits: 0,
            credits_progress: 0,
        };
    } catch (error) {
        console.error('Error cargando carrera:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadCareer();
});
</script>