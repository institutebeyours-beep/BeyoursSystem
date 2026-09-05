<template>
    <div class="min-h-screen" :style="{ backgroundColor: bgColor }">
        <div class="container mx-auto px-3 sm:px-4 md:px-6 py-4 sm:py-6 max-w-4xl">
            <!-- ========================================== -->
            <!-- HEADER                                    -->
            <!-- ========================================== -->
            <div class="rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 mb-4 sm:mb-6 md:mb-8 text-white shadow-2xl" :style="{ background: headerGradient }">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
                    <div>
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-black leading-tight">
                            👤 Mi Perfil
                        </h1>
                        <p class="text-gray-300 text-xs sm:text-sm font-medium mt-0.5 sm:mt-1">
                            Gestiona tu información personal
                        </p>
                    </div>
                    <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-white/20 rounded-full text-xs sm:text-sm font-bold backdrop-blur-sm">
                        🟢 En línea
                    </span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TARJETA DE PERFIL                         -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Avatar y encabezado -->
                <div class="relative">
                    <!-- Fondo decorativo -->
                    <div class="h-24 sm:h-32 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 dark:from-indigo-500/10 dark:to-purple-500/10"></div>
                    
                    <!-- Avatar con foto de perfil -->
                    <div class="absolute -bottom-12 left-1/2 sm:left-8 transform -translate-x-1/2 sm:translate-x-0">
                        <!-- ✅ Usar getImageUrl() para mostrar la imagen -->
                        <img 
                            v-if="user?.profile_image" 
                            :src="getImageUrl(user.profile_image)" 
                            alt="Profile" 
                            class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white dark:border-gray-800 object-cover shadow-xl"
                        />
                        <div 
                            v-else 
                            class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white dark:border-gray-800 flex items-center justify-center text-3xl sm:text-4xl font-bold text-white shadow-xl" 
                            :style="{ background: avatarColor }"
                        >
                            {{ getInitials(user?.name) }}
                        </div>
                    </div>
                </div>

                <!-- Información del perfil -->
                <div class="pt-16 sm:pt-16 pb-6 px-4 sm:px-6">
                    <div class="text-center sm:text-left">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
                            {{ user?.name || 'Usuario' }}
                        </h2>
                        <span class="px-3 py-1 inline-block mt-1 rounded-full text-xs font-bold" :class="getRoleBadgeClass(getRoleName())">
                            {{ getRoleName() }}
                        </span>
                    </div>

                    <!-- Datos del usuario -->
                    <div class="mt-6 space-y-3 divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center py-3 gap-1 sm:gap-4">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-32">📧 Email</span>
                            <span class="text-sm text-gray-800 dark:text-white font-medium">{{ user?.email || 'No disponible' }}</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center py-3 gap-1 sm:gap-4">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-32">🔑 UUID</span>
                            <span class="text-sm text-gray-600 dark:text-gray-300 font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ user?.uuid || 'No disponible' }}</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center py-3 gap-1 sm:gap-4">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-32">🔐 2FA</span>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold" :class="has2FA ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'">
                                    {{ has2FA ? '✅ Activado' : '❌ Desactivado' }}
                                </span>
                                <router-link 
                                    v-if="!has2FA" 
                                    to="/2fa/setup" 
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                                >
                                    Activar ahora
                                </router-link>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center py-3 gap-1 sm:gap-4">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-32">📅 Miembro desde</span>
                            <span class="text-sm text-gray-800 dark:text-white">{{ formatDate(user?.created_at) }}</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center py-3 gap-1 sm:gap-4">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-32">🕐 Último acceso</span>
                            <span class="text-sm text-gray-800 dark:text-white">{{ formatDate(user?.last_login_at) }}</span>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3">
                        <router-link 
                            to="/edit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm hover:bg-indigo-700 transition text-center"
                        >
                            ✏️ Editar Perfil
                        </router-link>
                        <router-link 
                            v-if="!has2FA" 
                            to="/2fa/setup" 
                            class="px-4 py-2 bg-amber-500 text-white rounded-lg font-bold text-sm hover:bg-amber-600 transition text-center"
                        >
                            🔐 Configurar 2FA
                        </router-link>
                        <button 
                            @click="changePassword" 
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            🔑 Cambiar Contraseña
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECCIÓN DE ACTIVIDAD RECIENTE              -->
            <!-- ========================================== -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">📋 Actividad Reciente</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <span class="text-xl">✅</span>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Inicio de sesión</p>
                            <p class="text-xs text-gray-400">Hace 2 minutos</p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-[10px] font-bold">Completado</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <span class="text-xl">👤</span>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Perfil actualizado</p>
                            <p class="text-xs text-gray-400">Hace 1 hora</p>
                        </div>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-[10px] font-bold">Actualizado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();
const settingsStore = useSettingsStore();

// =============================================
// 🖼️ FUNCIÓN PARA OBTENER URL DE IMAGEN
// =============================================
const getImageUrl = (path) => {
    if (!path) return null;
    
    // Si ya es una URL completa, devolverla
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }
    
    // Si el path ya incluye 'storage/', no duplicar
    if (path.startsWith('storage/')) {
        return `/${path}`;
    }
    
    // Obtener la URL base del entorno
    const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin;
    
    // Path relativo estándar
    return `${baseUrl}/storage/${path}`;
};

// =============================================
// COMPUTED
// =============================================
const user = computed(() => authStore.user);

const has2FA = computed(() => {
    return authStore.user?.two_factor_secret !== null;
});

const bgColor = computed(() => {
    return settingsStore.getSetting('background_color') || '#f3f4f6';
});

const headerGradient = computed(() => {
    const primaryColor = settingsStore.getSetting('primary_color') || '#6366f1';
    const secondaryColor = settingsStore.getSetting('secondary_color') || '#8b5cf6';
    return `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)`;
});

const avatarColor = computed(() => {
    const name = user.value?.name || 'Usuario';
    const colors = [
        '#4299e1', '#48bb78', '#ed8936', '#9f7aea',
        '#fc8181', '#68d391', '#63b3ed', '#f6ad55',
        '#4fd1c5', '#b794f4', '#f687b3', '#f6ad55'
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
});

// =============================================
// FUNCIONES
// =============================================
const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const getRoleName = () => {
    const roles = user.value?.roles;
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

const getRoleBadgeClass = (role) => {
    const classes = {
        'super-admin': 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
        'admin': 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
        'manager': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
        'editor': 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
        'viewer': 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    };
    return classes[role] || 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
};

const formatDate = (date) => {
    if (!date) return 'Nunca';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// =============================================
// ✅ CAMBIAR CONTRASEÑA CON POLÍTICA DE SETTINGS
// =============================================
const changePassword = async () => {
    // Obtener la política de contraseñas desde settings
    const passwordPolicy = settingsStore.getSetting('password_policy') || 'min:8';
    
    // Parsear la política
    const policyRules = parsePasswordPolicy(passwordPolicy);
    
    // Construir el mensaje de requisitos
    const requirements = buildRequirementsMessage(policyRules);
    
    const { value: password } = await Swal.fire({
        title: '🔑 Cambiar Contraseña',
        html: `
            <div class="text-left">
                <div class="mb-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <p class="text-xs font-bold text-amber-700 dark:text-amber-300">📋 Requisitos de contraseña:</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400">${requirements}</p>
                </div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva Contraseña</label>
                <input id="password" type="password" class="swal2-input w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Ingresa tu nueva contraseña">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-3 mb-1">Confirmar Contraseña</label>
                <input id="password_confirmation" type="password" class="swal2-input w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Repite la contraseña">
                <div id="password-strength" class="mt-2 text-xs"></div>
            </div>
        `,
        confirmButtonText: 'Cambiar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        didOpen: () => {
            const passwordInput = document.getElementById('password');
            const strengthDiv = document.getElementById('password-strength');
            
            passwordInput.addEventListener('input', () => {
                const value = passwordInput.value;
                const validation = validatePassword(value, policyRules);
                
                if (value.length > 0) {
                    let color = 'text-red-500';
                    let text = '❌ Contraseña débil';
                    
                    if (validation.isValid) {
                        color = 'text-green-500';
                        text = '✅ Contraseña válida';
                    } else if (validation.score >= 3) {
                        color = 'text-yellow-500';
                        text = '⚠️ Contraseña media';
                    }
                    
                    strengthDiv.innerHTML = `<span class="${color} font-medium">${text}</span>`;
                } else {
                    strengthDiv.innerHTML = '';
                }
            });
        },
        preConfirm: () => {
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            
            const validation = validatePassword(password, policyRules);
            
            if (!validation.isValid) {
                Swal.showValidationMessage(`❌ ${validation.message}`);
                return;
            }
            
            if (password !== password_confirmation) {
                Swal.showValidationMessage('❌ Las contraseñas no coinciden');
                return;
            }
            
            return { password, password_confirmation };
        }
    });

    if (password) {
        try {
            await axios.post('/api/profile/password', {
                password: password.password,
                password_confirmation: password.password_confirmation
            });
            
            Swal.fire({
                icon: 'success',
                title: '✅ Contraseña actualizada',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'Error al cambiar la contraseña',
            });
        }
    }
};

// =============================================
// 🔧 FUNCIONES DE POLÍTICA DE CONTRASEÑAS
// =============================================
const parsePasswordPolicy = (policy) => {
    const rules = {
        min: 8,
        special: false,
        numbers: false,
        uppercase: false,
        lowercase: false,
    };
    
    const parts = policy.split(',');
    parts.forEach(part => {
        const [key, value] = part.split(':');
        if (key === 'min') {
            rules.min = parseInt(value) || 8;
        } else if (key === 'special') {
            rules.special = value === 'true';
        } else if (key === 'numbers') {
            rules.numbers = value === 'true';
        } else if (key === 'uppercase') {
            rules.uppercase = value === 'true';
        } else if (key === 'lowercase') {
            rules.lowercase = value === 'true';
        }
    });
    
    return rules;
};

const validatePassword = (password, rules) => {
    const errors = [];
    let score = 0;
    
    if (password.length < rules.min) {
        errors.push(`Mínimo ${rules.min} caracteres`);
    } else {
        score++;
    }
    
    if (rules.special) {
        const hasSpecial = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(password);
        if (!hasSpecial) {
            errors.push('Al menos 1 carácter especial (!@#$%^&*)');
        } else {
            score++;
        }
    }
    
    if (rules.numbers) {
        const hasNumber = /\d/.test(password);
        if (!hasNumber) {
            errors.push('Al menos 1 número');
        } else {
            score++;
        }
    }
    
    if (rules.uppercase) {
        const hasUppercase = /[A-Z]/.test(password);
        if (!hasUppercase) {
            errors.push('Al menos 1 mayúscula');
        } else {
            score++;
        }
    }
    
    if (rules.lowercase) {
        const hasLowercase = /[a-z]/.test(password);
        if (!hasLowercase) {
            errors.push('Al menos 1 minúscula');
        } else {
            score++;
        }
    }
    
    const isValid = errors.length === 0;
    
    return {
        isValid,
        score,
        message: errors.join('. '),
        errors
    };
};

const buildRequirementsMessage = (rules) => {
    const requirements = [];
    
    requirements.push(`📏 Mínimo ${rules.min} caracteres`);
    if (rules.special) requirements.push('🔣 Carácter especial');
    if (rules.numbers) requirements.push('🔢 Números');
    if (rules.uppercase) requirements.push('🔠 Mayúsculas');
    if (rules.lowercase) requirements.push('🔡 Minúsculas');
    
    return requirements.join(' • ');
};
</script>