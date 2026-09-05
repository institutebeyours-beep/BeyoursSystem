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
                        Inicia sesión en tu cuenta
                    </p>
                </div>

                <!-- Formulario -->
                <form @submit.prevent="handleLogin" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            📧 Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            v-model="form.email" 
                            placeholder="tu@email.com"
                            required
                            autocomplete="email"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                🔑 Contraseña
                            </label>
                            <router-link 
                                to="/password/forgot" 
                                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                            >
                                ¿Olvidaste tu contraseña?
                            </router-link>
                        </div>
                        <div class="relative">
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                id="password" 
                                v-model="form.password" 
                                placeholder="********"
                                required
                                autocomplete="current-password"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                {{ showPassword ? '🙈' : '👁️' }}
                            </button>
                        </div>
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
                            Cargando...
                        </span>
                        <span v-else>🚀 Iniciar Sesión</span>
                    </button>

                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                        <p class="text-sm text-red-600 dark:text-red-400 text-center">{{ error }}</p>
                    </div>
                </form>

                <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    ¿No tienes cuenta?
                    <router-link to="/register" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                        Regístrate
                    </router-link>
                </p>

                <p class="mt-4 text-center text-xs text-gray-400 dark:text-gray-500">
                    v1.0.0
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';
import Swal from 'sweetalert2';

const router = useRouter();
const authStore = useAuthStore();
const settingsStore = useSettingsStore();

// =============================================
// STATE
// =============================================
const form = reactive({
    email: '',
    password: '',
    two_factor_code: '',
});

const loading = ref(false);
const error = ref('');
const showPassword = ref(false);
const requires2FA = ref(false);
const tempToken = ref(null);

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
// LOGIN
// =============================================
const handleLogin = async () => {
    loading.value = true;
    error.value = '';
    requires2FA.value = false;

    try {
        console.log('🔐 Intentando login con:', form.email);
        
        const response = await authStore.login(form.email, form.password);
        
        console.log('📥 Respuesta completa del login:', response);

        // ✅ PRIMERO: Asegurar que los settings estén cargados
        if (!settingsStore.initialized) {
            await settingsStore.initialize();
        }
        
        const twoFactorRequired = settingsStore.getSetting('2fa_required');
        console.log('🔍 Política 2FA:', twoFactorRequired);

        // ========================================== //
        // 🔐 VERIFICACIÓN DE 2FA (solo si política ACTIVA)
        // ========================================== //

        // 🔐 Si el usuario necesita verificar 2FA (ya tiene configurado) Y la política está activa
        if (twoFactorRequired && response.requires_2fa) {
            console.log('🔐 2FA requerido - necesita verificar código');
            tempToken.value = response.temp_token;
            requires2FA.value = true;
            loading.value = false;
            
            Swal.fire({
                icon: 'info',
                title: '🔐 Código 2FA requerido',
                text: 'Ingresa el código de tu aplicación de autenticación.',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            
            setTimeout(() => {
                document.getElementById('2fa_code')?.focus();
            }, 100);
            
            return;
        }

        // ✅ Si el backend dice que debe configurar 2FA (solo si política activa)
        if (twoFactorRequired && response.requires_2fa_setup) {
            console.log('🔐 Debe configurar 2FA - redirigiendo a /2fa/setup');
            
            sessionStorage.setItem('2fa_forced', 'true');
            
            if (response.token && response.user) {
                authStore.setAuth(response);
            }
            
            Swal.fire({
                icon: 'info',
                title: '🔐 Configuración 2FA requerida',
                text: 'La política de seguridad requiere que configures la autenticación de dos factores.',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            
            setTimeout(() => {
                router.push('/2fa/setup');
            }, 500);
            
            loading.value = false;
            return;
        }

        // ✅ Login exitoso (sin restricciones)
        if (response.success) {
            console.log('✅ Login exitoso - redirigiendo a dashboard');
            
            const user = response.user || authStore.user;
            const isAdmin = user?.roles?.includes('super-admin') || user?.roles?.includes('admin');
            const has2FA = !!user?.two_factor_secret;

            console.log('🔍 Verificación final 2FA en Login:', {
                twoFactorRequired,
                isAdmin,
                has2FA,
            });

            // ⚠️ Redundante: Si la política está activa, no es admin y no tiene 2FA
            if (twoFactorRequired && !isAdmin && !has2FA) {
                console.log('🔐 Redirigiendo a /2fa/setup desde Login (fallback)');
                sessionStorage.setItem('2fa_forced', 'true');
                setTimeout(() => {
                    router.push('/2fa/setup');
                }, 500);
                loading.value = false;
                return;
            }
            
            Swal.fire({
                icon: 'success',
                title: '✅ ¡Bienvenido!',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            
            setTimeout(() => {
                router.push('/dashboard');
            }, 500);
        } else {
            error.value = response.message || 'Credenciales incorrectas';
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.value,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    } catch (err) {
        console.error('❌ Error en login:', err);
        error.value = err.response?.data?.message || 'Error al iniciar sesión';
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.value,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    loading.value = false;
};
// =============================================
// 🔐 VERIFICAR 2FA (cuando ya está configurado)
// =============================================
const verify2FA = async () => {
    if (!form.two_factor_code || form.two_factor_code.length !== 6) {
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
    try {
        const response = await authStore.verify2FA({
            temp_token: tempToken.value,
            code: form.two_factor_code
        });

        if (response.success) {
            // ✅ MARCAR COMO VERIFICADO
            sessionStorage.setItem('2fa_verified', 'true');
            
            Swal.fire({
                icon: 'success',
                title: '✅ ¡Bienvenido!',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            
            // ✅ USAR WINDOW.LOCATION EN VEZ DE ROUTER.PUSH
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 500);
        } else {
            error.value = response.message || 'Código 2FA inválido';
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.value,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Error al verificar 2FA';
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.value,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    loading.value = false;
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(async () => {
    await settingsStore.initialize();
    
    if (authStore.isAuthenticated) {
        router.push('/dashboard');
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