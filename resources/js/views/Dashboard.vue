<template>
    <div class="min-h-screen" :style="{ backgroundColor: bgColor }">
        <div class="container mx-auto px-3 sm:px-4 md:px-6 py-4 sm:py-6 max-w-7xl">
            <!-- ========================================== -->
            <!-- HEADER CON SALUDO                         -->
            <!-- ========================================== -->
            <div class="rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 mb-4 sm:mb-6 md:mb-8 text-white shadow-2xl" :style="{ background: headerGradient }">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
                    <div class="w-full sm:w-auto">
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-black leading-tight">
                            👋 ¡Bienvenido, 
                            <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                {{ user?.name }}
                            </span>!
                        </h1>
                        <p class="text-gray-300 text-xs sm:text-sm font-medium mt-0.5 sm:mt-1">
                            {{ getTimeGreeting() }} - {{ getCurrentDate() }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 sm:gap-3 w-full sm:w-auto">
                        <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-green-500/20 text-green-400 rounded-full text-[10px] sm:text-xs font-bold border border-green-500/30">
                            🟢 En línea
                        </span>
                        <span 
                            class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-[10px] sm:text-xs font-bold border"
                            :class="getRoleClass()"
                        >
                            👑 {{ getRoleName(user?.roles) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TARJETAS DE ESTADÍSTICAS                   -->
            <!-- ========================================== -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-6 mb-4 sm:mb-6 md:mb-8">
                <!-- Tarjeta 1: Usuarios -->
                <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 md:p-6 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500"></div>
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-blue-50 dark:bg-blue-900/30 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl md:text-3xl group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition">
                            👤
                        </div>
                        <div>
                            <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-800 dark:text-white">1</p>
                            <p class="text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider leading-tight">Usuarios</p>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-3 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full" style="width: 85%"></div>
                    </div>
                </div>

                <!-- Tarjeta 2: Cursos -->
                <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 md:p-6 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-500"></div>
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-green-50 dark:bg-green-900/30 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl md:text-3xl group-hover:bg-green-100 dark:group-hover:bg-green-900/50 transition">
                            🎓
                        </div>
                        <div>
                            <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-800 dark:text-white">0</p>
                            <p class="text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider leading-tight">Cursos</p>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-3 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full" style="width: 65%"></div>
                    </div>
                </div>

                <!-- Tarjeta 3: Roles -->
                <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 md:p-6 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-amber-50 dark:bg-amber-900/30 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl md:text-3xl group-hover:bg-amber-100 dark:group-hover:bg-amber-900/50 transition">
                            📊
                        </div>
                        <div>
                            <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-800 dark:text-white">{{ user?.roles?.length || 0 }}</p>
                            <p class="text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider leading-tight">Roles</p>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-3 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full" style="width: 45%"></div>
                    </div>
                </div>

                <!-- ❌ Tarjeta 4: 2FA ELIMINADA -->
            </div>

            <!-- ========================================== -->
            <!-- ACTIVIDAD RECIENTE                        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 md:p-6 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 sm:mb-6">
                    <h2 class="text-base sm:text-lg font-black text-gray-800 dark:text-white">📋 Actividad Reciente</h2>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full text-[8px] sm:text-[10px] font-bold uppercase tracking-wider">
                        Últimos 7 días
                    </span>
                </div>
                
                <div class="space-y-2 sm:space-y-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all hover:translate-x-1 cursor-pointer">
                        <span class="text-xl sm:text-2xl">✅</span>
                        <div class="flex-1 w-full sm:w-auto">
                            <p class="font-bold text-gray-800 dark:text-white text-xs sm:text-sm">Inicio de sesión exitoso</p>
                            <span class="text-[10px] sm:text-xs text-gray-400">Hace 2 minutos</span>
                        </div>
                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-[8px] sm:text-[10px] font-bold whitespace-nowrap">Completado</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all hover:translate-x-1 cursor-pointer">
                        <span class="text-xl sm:text-2xl">👤</span>
                        <div class="flex-1 w-full sm:w-auto">
                            <p class="font-bold text-gray-800 dark:text-white text-xs sm:text-sm">Perfil actualizado</p>
                            <span class="text-[10px] sm:text-xs text-gray-400">Hace 1 hora</span>
                        </div>
                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-[8px] sm:text-[10px] font-bold whitespace-nowrap">Actualizado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useSettingsStore } from '../stores/settings';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const settingsStore = useSettingsStore();

// =============================================
// COMPUTED
// =============================================
const user = computed(() => authStore.user);

// ❌ has2FA ELIMINADO

const isAdmin = computed(() => {
    if (!authStore.user) return false;
    return authStore.isAdmin || authStore.isSuperAdmin;
});

const bgColor = computed(() => {
    return settingsStore.getSetting('background_color') || '#f3f4f6';
});

const headerGradient = computed(() => {
    const primaryColor = settingsStore.getSetting('primary_color') || '#6366f1';
    const secondaryColor = settingsStore.getSetting('secondary_color') || '#8b5cf6';
    return `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)`;
});

// =============================================
// FUNCIONES
// =============================================
const getTimeGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return '🌅 Buenos días';
    if (hour < 18) return '☀️ Buenas tardes';
    return '🌙 Buenas noches';
};

const getCurrentDate = () => {
    return new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getRoleName = (roles) => {
    if (!roles) return 'Sin rol';
    if (Array.isArray(roles)) {
        return roles.length ? roles[0] : 'Sin rol';
    }
    if (typeof roles === 'object') {
        const values = Object.values(roles);
        return values.length ? values[0] : 'Sin rol';
    }
    return String(roles) || 'Sin rol';
};

const getRoleClass = () => {
    const role = getRoleName(user.value?.roles);
    if (role.includes('super-admin')) return 'border-purple-400 text-purple-400 bg-purple-500/20';
    if (role.includes('admin')) return 'border-blue-400 text-blue-400 bg-blue-500/20';
    if (role.includes('manager')) return 'border-green-400 text-green-400 bg-green-500/20';
    return 'border-gray-400 text-gray-400 bg-gray-500/20';
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(async () => {
    await authStore.fetchUser();
    
    Swal.fire({
        icon: 'success',
        title: '¡Bienvenido!',
        text: `Hola ${authStore.user?.name}, disfruta tu experiencia.`,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: '#1f2937',
        color: '#ffffff',
        iconColor: '#6366f1',
    });
});
</script>