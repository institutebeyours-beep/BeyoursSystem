<template>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">⚙️ Configuración</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Personaliza tu experiencia y gestiona el sistema</p>
        </div>

        <!-- TABS -->
        <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 dark:border-gray-700">
            <button 
                v-for="tab in tabs" 
                :key="tab.id"
                @click="activeTab = tab.id"
                class="px-4 py-2 text-sm font-medium transition-all rounded-t-lg"
                :class="activeTab === tab.id 
                    ? 'bg-indigo-600 text-white shadow-lg' 
                    : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
            >
                {{ tab.icon }} {{ tab.label }}
            </button>
        </div>

        <!-- TAB 1: MI PERFIL -->
        <div v-if="activeTab === 'personal'" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">🌙 Modo Oscuro</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Activa el modo oscuro para toda la aplicación</p>
                        <p class="text-xs text-blue-500 dark:text-blue-400 mt-1">💡 Configuración personal (solo para ti)</p>
                    </div>
                    <button 
                        @click="settings.toggleDarkMode()"
                        class="relative inline-flex items-center h-8 rounded-full w-16 transition-colors duration-300 focus:outline-none flex-shrink-0"
                        :class="settings.darkMode ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600'"
                    >
                        <span 
                            class="inline-block w-6 h-6 transform bg-white rounded-full transition-transform duration-300 shadow-md"
                            :class="settings.darkMode ? 'translate-x-8' : 'translate-x-1'"
                        >
                            <span class="flex items-center justify-center w-full h-full text-sm">
                                {{ settings.darkMode ? '🌙' : '☀️' }}
                            </span>
                        </span>
                    </button>
                </div>
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        💡 El modo oscuro se guarda en tu navegador y no afecta a otros usuarios.
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">🖼️ Imagen del Sidebar</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Imagen personalizada en la parte superior del menú</p>
                        <p class="text-xs text-blue-500 dark:text-blue-400 mt-1">💡 Configuración personal (solo para ti)</p>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-[10px] font-bold">Personal</span>
                </div>
                
                <div class="mt-4 space-y-4">
                    <div v-if="settings.sidebarImage" class="relative inline-block">
                        <img 
                            :src="settings.sidebarImage" 
                            alt="Imagen del sidebar" 
                            class="w-full max-w-xs h-32 object-cover rounded-xl border border-gray-200 dark:border-gray-600"
                        />
                        <button 
                            @click="removeLocalImage"
                            class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition text-xs shadow-lg"
                            title="Eliminar imagen"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <label class="cursor-pointer px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium shadow-sm hover:shadow-md">
                            📤 Subir imagen
                            <input type="file" class="hidden" accept="image/*" @change="uploadLocalImage" />
                        </label>

                        <button 
                            v-for="preset in presetImages" 
                            :key="preset.id"
                            @click="selectPresetImage(preset.url)"
                            class="w-12 h-12 rounded-lg overflow-hidden border-2 hover:border-indigo-500 transition flex-shrink-0"
                            :class="settings.sidebarImage === preset.url ? 'border-indigo-500 ring-2 ring-indigo-300' : 'border-gray-200 dark:border-gray-600'"
                            :title="preset.name"
                        >
                            <img :src="preset.url" :alt="preset.name" class="w-full h-full object-cover" />
                        </button>

                        <button 
                            v-if="settings.sidebarImage"
                            @click="removeLocalImage"
                            class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition text-sm font-medium"
                        >
                            🗑️ Quitar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: ADMINISTRACIÓN -->
        <div v-if="activeTab === 'global'">
            <div v-if="isAdmin" class="space-y-6">
                <!-- APARIENCIA GLOBAL -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">🎨 Apariencia Global</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Configuración visual para todos los usuarios</p>
                        </div>
                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full text-[10px] font-bold">Global</span>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- NOMBRE -->
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Nombre de la aplicación</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nombre que aparece en el sistema</p>
                            </div>
                            <input 
                                type="text" 
                                :value="getValue('app_name')"
                                @change="updateSetting('app_name', $event.target.value)"
                                class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white w-48 text-sm"
                            />
                        </div>

                        <!-- COLOR SIDEBAR -->
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Color del sidebar</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Color de fondo del menú lateral</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input 
                                    type="color" 
                                    :value="getValue('sidebar_color')"
                                    @change="updateSetting('sidebar_color', $event.target.value)"
                                    class="w-12 h-12 rounded-lg border border-gray-200 cursor-pointer"
                                />
                                <span class="text-sm text-gray-500">
                                    {{ getValue('sidebar_color') }}
                                </span>
                            </div>
                        </div>

                        <!-- COLOR TEXTO SIDEBAR -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Color del texto del sidebar</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Color del texto en el menú lateral</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input 
                                    type="color" 
                                    :value="getValue('sidebar_text_color')"
                                    @change="updateSetting('sidebar_text_color', $event.target.value)"
                                    class="w-12 h-12 rounded-lg border border-gray-200 cursor-pointer"
                                />
                                <span class="text-sm text-gray-500">
                                    {{ getValue('sidebar_text_color') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEGURIDAD GLOBAL -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">🔐 Seguridad Global</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Políticas de seguridad para todos</p>
                        </div>
                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full text-[10px] font-bold">Global</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">2FA Obligatorio</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Forzar autenticación de dos factores</p>
                            </div>
                            <button 
                                @click="updateSetting('2fa_required', !getValue('2fa_required'))"
                                class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-300 flex-shrink-0"
                                :class="getValue('2fa_required') ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600'"
                            >
                                <span 
                                    class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-300"
                                    :class="getValue('2fa_required') ? 'translate-x-6' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Recuperación de contraseña</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Permitir recuperación por email</p>
                            </div>
                            <button 
                                @click="updateSetting('password_recovery', !getValue('password_recovery'))"
                                class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-300 flex-shrink-0"
                                :class="getValue('password_recovery') ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600'"
                            >
                                <span 
                                    class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-300"
                                    :class="getValue('password_recovery') ? 'translate-x-6' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Expiración de sesión</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Minutos de inactividad</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    :value="getValue('session_timeout')"
                                    @change="updateSetting('session_timeout', parseInt($event.target.value))"
                                    class="w-20 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-center"
                                />
                                <span class="text-sm text-gray-500">min</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Intentos de login</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Intentos fallidos antes de bloquear</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    :value="getValue('max_login_attempts')"
                                    @change="updateSetting('max_login_attempts', parseInt($event.target.value))"
                                    class="w-20 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-center"
                                />
                                <span class="text-sm text-gray-500">intentos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!isAdmin" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-8 text-center">
                <div class="text-6xl mb-4">🔒</div>
                <h3 class="text-xl font-bold text-yellow-700 dark:text-yellow-400 mb-2">Acceso Restringido</h3>
                <p class="text-sm text-yellow-600 dark:text-yellow-300 max-w-md mx-auto">
                    La configuración global solo está disponible para administradores del sistema.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';
import Swal from 'sweetalert2';

const settings = useSettingsStore();
const authStore = useAuthStore();

const activeTab = ref('personal');

const isAdmin = computed(() => {
    const roles = authStore.user?.roles || [];
    return roles.includes('super-admin') || roles.includes('admin');
});

const tabs = [
    { id: 'personal', label: 'Mi Perfil', icon: '👤' },
    { id: 'global', label: 'Administración', icon: '⚙️' },
];

const presetImages = [
    { id: 1, name: 'Montañas', url: 'https://picsum.photos/id/10/200/100' },
    { id: 2, name: 'Bosque', url: 'https://picsum.photos/id/11/200/100' },
    { id: 3, name: 'Mar', url: 'https://picsum.photos/id/12/200/100' },
    { id: 4, name: 'Atardecer', url: 'https://picsum.photos/id/13/200/100' },
    { id: 5, name: 'Naturaleza', url: 'https://picsum.photos/id/14/200/100' },
];

// ✅ OBTENER VALOR
const getValue = (key) => {
    try {
        const value = settings.getSetting(key);
        if (value !== null && value !== undefined && value !== '') {
            return value;
        }
        const defaults = {
            app_name: 'Beyours',
            sidebar_color: '#1a202c',
            sidebar_text_color: '#ffffff',
            '2fa_required': false,
            password_recovery: true,
            session_timeout: 120,
            max_login_attempts: 5,
        };
        return defaults[key] || '';
    } catch {
        return '';
    }
};

// ✅ ACTUALIZAR CONFIGURACIÓN
const updateSetting = async (key, value) => {
    try {
        await settings.updateSetting(key, value);
        await settings.fetchSettings();
        
        // Forzar actualización del sidebar
        if (key === 'app_name' || key === 'sidebar_color' || key === 'sidebar_text_color') {
            window.dispatchEvent(new Event('storage'));
        }
        
        Swal.fire({
            icon: 'success',
            title: '✅ Configuración actualizada',
            timer: 1500,
            showConfirmButton: false,
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: 'Error al actualizar',
        });
    }
};

// IMAGEN LOCAL
const uploadLocalImage = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        settings.setSidebarImage(e.target.result);
        Swal.fire({
            icon: 'success',
            title: '✅ Imagen actualizada',
            timer: 2000,
            showConfirmButton: false,
        });
    };
    reader.readAsDataURL(file);
};

const selectPresetImage = (url) => {
    settings.setSidebarImage(url);
    Swal.fire({
        icon: 'success',
        title: '✅ Imagen actualizada',
        timer: 2000,
        showConfirmButton: false,
    });
};

const removeLocalImage = () => {
    Swal.fire({
        title: '¿Eliminar imagen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            settings.removeSidebarImage();
            Swal.fire({
                icon: 'success',
                title: '✅ Imagen eliminada',
                timer: 2000,
                showConfirmButton: false,
            });
        }
    });
};

onMounted(async () => {
    await settings.initialize();
});
</script>