<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER CON GRADIENTE -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">🎨 Configuraciones Globales</h1>
            <p class="text-indigo-100 mt-2">Personaliza la apariencia del sistema para todos los usuarios</p>
        </div>

        <!-- ========================================== -->
        <!-- TARJETAS DE COLORES EN GRID               -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Tarjeta: Color del Sidebar -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-1">
                <div class="h-2" :style="{ backgroundColor: getColorValue('sidebar_color') }"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-white">📐 Color del Sidebar</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Global</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Fondo del menú lateral</p>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-2xl shadow-lg border-2 border-gray-200" :style="{ backgroundColor: getColorValue('sidebar_color') }"></div>
                        <span class="text-sm font-mono text-gray-600 dark:text-gray-300">{{ getColorValue('sidebar_color') }}</span>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <button 
                            v-for="color in sidebarColors"
                            :key="color"
                            @click="updateSetting('sidebar_color', color)"
                            class="w-10 h-10 rounded-full border-2 transition-all hover:scale-110 hover:shadow-lg"
                            :class="getColorValue('sidebar_color') === color ? 'border-indigo-600 ring-4 ring-indigo-300 scale-110' : 'border-gray-200 dark:border-gray-600'"
                            :style="{ backgroundColor: color }"
                            :title="color"
                        ></button>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Color del Texto del Sidebar -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-1">
                <div class="h-2" :style="{ backgroundColor: getColorValue('sidebar_text_color') }"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-white">✏️ Color del Texto</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Global</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Color del texto en el menú</p>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-2xl shadow-lg border-2 border-gray-200" :style="{ backgroundColor: getColorValue('sidebar_text_color') }"></div>
                        <span class="text-sm font-mono text-gray-600 dark:text-gray-300">{{ getColorValue('sidebar_text_color') }}</span>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <button 
                            v-for="color in textColors"
                            :key="color"
                            @click="updateSetting('sidebar_text_color', color)"
                            class="w-10 h-10 rounded-full border-2 transition-all hover:scale-110 hover:shadow-lg"
                            :class="getColorValue('sidebar_text_color') === color ? 'border-indigo-600 ring-4 ring-indigo-300 scale-110' : 'border-gray-200 dark:border-gray-600'"
                            :style="{ backgroundColor: color }"
                            :title="color"
                        ></button>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Color Primario -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-1">
                <div class="h-2" :style="{ backgroundColor: getColorValue('primary_color') }"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-white">🎯 Color Primario</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Global</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Color principal de la interfaz</p>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-2xl shadow-lg border-2 border-gray-200" :style="{ backgroundColor: getColorValue('primary_color') }"></div>
                        <span class="text-sm font-mono text-gray-600 dark:text-gray-300">{{ getColorValue('primary_color') }}</span>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <button 
                            v-for="color in primaryColors"
                            :key="color"
                            @click="updateSetting('primary_color', color)"
                            class="w-10 h-10 rounded-full border-2 transition-all hover:scale-110 hover:shadow-lg"
                            :class="getColorValue('primary_color') === color ? 'border-indigo-600 ring-4 ring-indigo-300 scale-110' : 'border-gray-200 dark:border-gray-600'"
                            :style="{ backgroundColor: color }"
                            :title="color"
                        ></button>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Tema por defecto -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-1">
                <div class="h-2 bg-gradient-to-r from-yellow-400 to-orange-400"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-white">🌓 Tema por defecto</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Global</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Tema para nuevos usuarios</p>
                    
                    <div class="flex gap-4">
                        <button 
                            @click="updateSetting('default_theme', 'light')"
                            class="flex-1 py-3 px-4 rounded-xl font-bold transition-all hover:scale-105"
                            :class="getValue('default_theme') === 'light' 
                                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' 
                                : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                        >
                            ☀️ Claro
                        </button>
                        <button 
                            @click="updateSetting('default_theme', 'dark')"
                            class="flex-1 py-3 px-4 rounded-xl font-bold transition-all hover:scale-105"
                            :class="getValue('default_theme') === 'dark' 
                                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' 
                                : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                        >
                            🌙 Oscuro
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Nombre de la aplicación -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-1">
                <div class="h-2 bg-gradient-to-r from-blue-400 to-indigo-400"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-white">📝 Nombre de la aplicación</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Global</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Nombre que aparece en el sistema</p>
                    <input 
                        type="text" 
                        :value="getValue('app_name')"
                        @change="updateSetting('app_name', $event.target.value)"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    />
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ========================================== -->
        <!-- 🔥 SECCIÓN MI PERFIL (ADAPTADO)            -->
        <!-- ========================================== -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Tarjeta: Modo Oscuro (personal) -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
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

            <!-- Tarjeta: Imagen del Sidebar (personal) -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
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
                        <!-- ✅ Usar getImageUrl() para mostrar la imagen -->
                        <img 
                            :src="getImageUrl(settings.sidebarImage)" 
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

        <!-- FOOTER -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-3xl p-6">
            <p class="text-sm text-blue-700 dark:text-blue-300 text-center">
                💡 Las configuraciones globales afectan a <strong>todos los usuarios</strong> del sistema.
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useSettingsStore } from '@/stores/settings';
import { useAuthStore } from '@/stores/auth';

const settings = useSettingsStore();
const authStore = useAuthStore();

const presetImages = [
    { id: 1, name: 'Montañas', url: 'https://picsum.photos/id/10/200/100' },
    { id: 2, name: 'Bosque', url: 'https://picsum.photos/id/11/200/100' },
    { id: 3, name: 'Mar', url: 'https://picsum.photos/id/12/200/100' },
    { id: 4, name: 'Atardecer', url: 'https://picsum.photos/id/13/200/100' },
    { id: 5, name: 'Naturaleza', url: 'https://picsum.photos/id/14/200/100' },
];

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
// 🎨 PALETAS DE COLORES
// =============================================
const sidebarColors = [
    '#1a202c', '#2d3748', '#4a5568', '#718096',
    '#9f7aea', '#805ad5', '#6b46c1',
    '#4299e1', '#3182ce', '#2b6cb0',
    '#48bb78', '#38a169',
    '#ed8936', '#dd6b20',
    '#e53e3e', '#c53030',
];

const textColors = [
    '#ffffff', '#f7fafc', '#e2e8f0', '#cbd5e0',
    '#a0aec0', '#718096', '#4a5568', '#2d3748', '#1a202c',
];

const primaryColors = [
    '#6366f1', '#8b5cf6', '#7c3aed', '#6d28d9',
    '#4f46e5', '#4338ca', '#3730a3',
    '#3b82f6', '#2563eb', '#1d4ed8',
    '#0ea5e9', '#0284c7',
    '#14b8a6', '#0d9488',
    '#f59e0b', '#d97706',
    '#ef4444', '#dc2626',
];

// =============================================
// FUNCIONES
// =============================================
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
            primary_color: '#6366f1',
            default_theme: 'light',
        };
        return defaults[key] || '';
    } catch {
        return '';
    }
};

const getColorValue = (key) => {
    try {
        const value = settings.getSetting(key);
        if (value) return value;
        
        const defaults = {
            sidebar_color: '#1a202c',
            sidebar_text_color: '#ffffff',
            primary_color: '#6366f1',
        };
        return defaults[key] || '#000000';
    } catch {
        return '#000000';
    }
};

const updateSetting = async (key, value) => {
    try {
        await settings.updateSetting(key, value);
        await settings.fetchSettings();
        
        if (key === 'sidebar_color' || key === 'sidebar_text_color' || key === 'app_name') {
            window.dispatchEvent(new Event('storage'));
        }
        
        Swal.fire({
            icon: 'success',
            title: '✅ Configuración actualizada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: 'Error al actualizar',
        });
    }
};

const uploadLocalImage = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validar tamaño (máx 2MB)
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'warning',
            title: 'Archivo muy grande',
            text: 'La imagen debe ser menor a 2MB',
        });
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        // ✅ Guardar en el store (para el sidebar personal)
        settings.setSidebarImage(e.target.result);
        Swal.fire({
            icon: 'success',
            title: '✅ Imagen actualizada',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };
    reader.readAsDataURL(file);
};

const selectPresetImage = (url) => {
    // ✅ Guardar en el store (para el sidebar personal)
    settings.setSidebarImage(url);
    Swal.fire({
        icon: 'success',
        title: '✅ Imagen actualizada',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
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
            // ✅ Eliminar del store
            settings.removeSidebarImage();
            Swal.fire({
                icon: 'success',
                title: '✅ Imagen eliminada',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });
};

onMounted(async () => {
    await settings.initialize();
});
</script>