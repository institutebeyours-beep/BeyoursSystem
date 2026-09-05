<template>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-card">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <img 
                        v-if="logoUrl" 
                        :src="logoUrl" 
                        alt="Logo" 
                        class="h-16 mx-auto mb-4 object-contain"
                    />
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-800 dark:text-white">
                        {{ appName || 'Beyours' }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        🔐 Verificación de dos factores
                    </p>
                </div>

                <!-- Formulario -->
                <form @submit.prevent="handleVerify" class="space-y-5">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            📱 Código de 6 dígitos
                        </label>
                        <input 
                            type="text" 
                            id="code" 
                            v-model="code" 
                            maxlength="6"
                            placeholder="000000"
                            required
                            autofocus
                            autocomplete="one-time-code"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-center text-2xl tracking-[8px] focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            Ingresa el código de 6 dígitos de tu aplicación de autenticación.
                        </p>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="loading" 
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-indigo-500/30"
                        :class="loading ? 'opacity-70' : ''"
                    >
                        <span v-if="loading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verificando...
                        </span>
                        <span v-else>🔐 Verificar</span>
                    </button>

                    <!-- Mensaje de error -->
                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                        <p class="text-sm text-red-600 dark:text-red-400 text-center">{{ error }}</p>
                    </div>
                </form>

                <!-- Volver -->
                <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    <a href="#" @click.prevent="goBack" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                        ⬅️ Volver al inicio de sesión
                    </a>
                </p>

                <!-- Versión -->
                <p class="mt-4 text-center text-xs text-gray-400 dark:text-gray-500">
                    v1.0.0
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';

const router = useRouter();
const authStore = useAuthStore();
const settingsStore = useSettingsStore();

// =============================================
// STATE
// =============================================
const code = ref('');
const loading = ref(false);
const error = ref('');

// =============================================
// 🖼️ FUNCIÓN PARA OBTENER URL DE IMAGEN
// =============================================
const getImageUrl = (path) => {
    if (!path) return null;
    
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }
    
    if (path.startsWith('storage/')) {
        return `/${path}`;
    }
    
    const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin;
    return `${baseUrl}/storage/${path}`;
};

// =============================================
// COMPUTED
// =============================================
const appName = computed(() => {
    return settingsStore.getSetting('app_name') || 'Beyours';
});

const logoUrl = computed(() => {
    const logo = settingsStore.getSetting('logo');
    return getImageUrl(logo);
});

const loginBackground = computed(() => {
    const bg = settingsStore.getSetting('login_background');
    if (bg) {
        return `url(${getImageUrl(bg)})`;
    }
    const primaryColor = settingsStore.getSetting('primary_color') || '#6366f1';
    const secondaryColor = settingsStore.getSetting('secondary_color') || '#8b5cf6';
    return `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)`;
});

// =============================================
// FUNCIONES
// =============================================
const handleVerify = async () => {
    if (!code.value || code.value.length !== 6) {
        error.value = 'Ingresa un código de 6 dígitos';
        Swal.fire({
            icon: 'warning',
            title: 'Código inválido',
            text: 'Ingresa un código de 6 dígitos.',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        return;
    }

    loading.value = true;
    error.value = '';

    const tempToken = localStorage.getItem('2fa_temp_token');
    
    if (!tempToken) {
        error.value = 'Sesión expirada. Inicia sesión nuevamente.';
        loading.value = false;
        
        Swal.fire({
            icon: 'error',
            title: '❌ Sesión expirada',
            text: 'El tiempo ha expirado. Inicia sesión nuevamente.',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        setTimeout(() => {
            router.push('/login');
        }, 1500);
        return;
    }

try {
    const response = await axios.post('/api/2fa/verify', {
        code: code.value,
        temp_token: tempToken
    });

    // ✅ Guardar autenticación (esto debe incluir two_factor_secret y two_factor_confirmed_at)
    authStore.setAuth(response.data);
    localStorage.removeItem('2fa_temp_token');
    sessionStorage.setItem('2fa_verified', 'true');

    // ✅ Forzar actualización del usuario
    await authStore.fetchUser();

    Swal.fire({
        icon: 'success',
        title: '✅ Verificación exitosa',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });

    setTimeout(() => {
        window.location.href = '/dashboard';
    }, 500);
    
} catch (err) {
   
        error.value = err.response?.data?.message || 'Código inválido. Intenta nuevamente.';
        
        Swal.fire({
            icon: 'error',
            title: '❌ Código inválido',
            text: error.value,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        // Limpiar el campo para reintentar
        code.value = '';
        document.getElementById('code')?.focus();
    }

    loading.value = false;
};

const goBack = () => {
    localStorage.removeItem('2fa_temp_token');
    
    Swal.fire({
        icon: 'info',
        title: 'Volviendo al inicio de sesión',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
    
    setTimeout(() => {
        router.push('/login');
    }, 300);
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(async () => {
    await settingsStore.initialize();
    
    // Verificar si hay token temporal
    const tempToken = localStorage.getItem('2fa_temp_token');
    if (!tempToken) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión no iniciada',
            text: 'No hay una sesión de 2FA activa. Inicia sesión nuevamente.',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        setTimeout(() => {
            router.push('/login');
        }, 1500);
    }
});
</script>

<style scoped>
/* ========================================== */
/* LOGIN WRAPPER - OCUPA EL 100%             */
/* ========================================== */
.login-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    min-height: 100vh;
    background-image: v-bind(loginBackground);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

/* ========================================== */
/* CONTENEDOR - CENTRADO                     */
/* ========================================== */
.login-container {
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
}

/* ========================================== */
/* TARJETA - CON EFECTO DE VIDRIO            */
/* ========================================== */
.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 2rem 2rem 1.5rem;
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.3);
    width: 100%;
}

/* Modo oscuro */
:root.dark .login-card {
    background: rgba(31, 41, 55, 0.95);
    border-color: rgba(55, 65, 81, 0.5);
}

/* ========================================== */
/* RESPONSIVE                                */
/* ========================================== */
@media (max-width: 640px) {
    .login-card {
        padding: 1.5rem 1.25rem 1.25rem;
        border-radius: 1rem;
    }
    
    .login-wrapper {
        padding: 0.75rem;
    }
}

@media (max-width: 400px) {
    .login-card {
        padding: 1.25rem 1rem 1rem;
    }
}
</style>