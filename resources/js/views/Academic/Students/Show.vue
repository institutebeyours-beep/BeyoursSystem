<template>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">{{ student.user?.name || 'Estudiante' }}</h1>
                    <p class="text-blue-100 mt-2">{{ student.code }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span 
                        class="px-3 py-1 rounded-full text-sm font-medium"
                        :class="getStatusClass(student.status)"
                    >
                        {{ getStatusText(student.status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN GENERAL -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ student.user?.email || '-' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Teléfono</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ student.phone || '-' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Cursos</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ stats.total_courses || 0 }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Promedio</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ stats.average_grade || 0 }}%</p>
            </div>
        </div>

        <!-- INFORMACIÓN PERSONAL -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Datos personales -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">👤 Datos Personales</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nombre completo</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ student.user?.name || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Email</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ student.user?.email || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Teléfono</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ student.phone || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Fecha de nacimiento</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ formatDate(student.birth_date) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Dirección</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ student.address || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Código</span>
                        <span class="font-bold text-gray-900 dark:text-white font-mono">{{ student.code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Fecha de inscripción</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ formatDate(student.enrollment_date) }}</span>
                    </div>
                </div>
            </div>

            <!-- Apoderado -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">👨‍👩‍👦 Apoderado</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nombre</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ student.guardian_name || 'No registrado' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Teléfono</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ student.guardian_phone || 'No registrado' }}</span>
                    </div>
                    <div v-if="student.guardian_name" class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Información de contacto de emergencia</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RENDIMIENTO ACADÉMICO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 Rendimiento Académico</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_courses || 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total de cursos</p>
                </div>
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.completed_courses || 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">✅ Cursos completados</p>
                </div>
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.average_grade || 0 }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">📊 Promedio general</p>
                </div>
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ stats.attendance_percentage || 0 }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">📋 Asistencia</p>
                </div>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="flex items-center justify-end gap-3">
            <router-link 
                to="/academic/students"
                class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
            >
                ← Volver
            </router-link>
            <router-link 
                :to="`/academic/students/${student.id}/edit`"
                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition flex items-center gap-2"
            >
                ✏️ Editar
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';

const route = useRoute();
const student = ref({});
const stats = ref({});

// =============================================
// FUNCIONES
// =============================================
const loadStudent = async () => {
    try {
        // ✅ CORREGIDO: Agregar /api/
        const response = await axios.get(`/api/academic/students/${route.params.id}`);
        student.value = response.data.student;
        stats.value = response.data.stats || {};
        
        console.log('📋 Estudiante cargado:', student.value);
        console.log('📊 Estadísticas:', stats.value);
    } catch (error) {
        console.error('Error cargando estudiante:', error);
    }
};

// ✅ Función para formatear fecha
const formatDate = (date) => {
    if (!date) return 'No especificada';
    
    try {
        const d = new Date(date);
        return d.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (error) {
        return 'No especificada';
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
    loadStudent();
});
</script>