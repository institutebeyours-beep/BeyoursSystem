<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <header class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-black">🎨 Configuraciones Globales</h1>
                    <p class="text-indigo-100 mt-2">Personaliza la apariencia del sistema para todos los usuarios</p>
                </div>
                
                <div v-if="faviconPreview" class="mt-4 md:mt-0 flex items-center gap-3 bg-white/10 px-4 py-2 rounded-lg">
                    <span class="text-indigo-200 text-sm">Favicon:</span>
                    <img :src="faviconPreview" alt="Favicon" class="w-10 h-10 border-2 border-white rounded-lg" />
                </div>
            </div>
        </header>

        <!-- SECCIÓN APARIENCIA -->
        <section class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">🎨 Apariencia</h2>

            <div class="space-y-6">
                <!-- FAVICON -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div class="flex-1 mb-3 sm:mb-0">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Favicon</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sube cualquier imagen y la convertiremos a 32x32</p>
                        <p class="text-xs text-gray-400 mt-1">Formatos: .ico, .png, .jpg, .webp</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div v-if="faviconPreview" class="relative group">
                            <img :src="faviconPreview" class="w-12 h-12 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600" />
                            <button 
                                @click="removeImage('favicon')"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                ✕
                            </button>
                        </div>
                        
                        <label class="cursor-pointer px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                            <span>📤</span> Subir Imagen
                            <input 
                                type="file" 
                                class="hidden" 
                                accept=".ico,image/png,image/jpeg,image/webp,image/gif" 
                                @change="uploadImage($event, 'favicon')" 
                            />
                        </label>
                    </div>
                </div>

                <!-- LOGIN BACKGROUND -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex-1 mb-3 sm:mb-0">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Login Background</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Imagen de fondo para la página de login</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div v-if="loginBackgroundPreview" class="relative group">
                            <img :src="loginBackgroundPreview" class="w-16 h-12 object-cover rounded-lg border border-gray-200" />
                            <button 
                                @click="removeImage('login_background')"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                ✕
                            </button>
                        </div>
                        <label class="cursor-pointer px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">
                            📤 Subir
                            <input 
                                type="file" 
                                class="hidden" 
                                accept="image/*" 
                                @change="uploadImage($event, 'login_background')" 
                            />
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <!-- OTROS GRUPOS -->
        <section 
            v-for="(group, groupName) in otherSettings" 
            :key="groupName" 
            class="mt-8 bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6"
        >
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ getGroupLabel(groupName) }}</h2>

            <div class="space-y-6">
                <div 
                    v-for="(setting, key) in group" 
                    :key="key" 
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between"
                >
                    <div class="flex-1 mb-2 sm:mb-0">
                        <p class="font-medium text-gray-700 dark:text-gray-300">{{ setting.label || key }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ setting.description }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- BOOLEANO -->
                        <button 
                            v-if="setting.type === 'boolean'"
                            @click="updateSetting(key, !setting.value)"
                            class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-300"
                            :class="setting.value ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600'"
                        >
                            <span 
                                class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-300"
                                :class="setting.value ? 'translate-x-6' : 'translate-x-1'"
                            ></span>
                        </button>

                        <!-- NÚMERO -->
                        <input 
                            v-else-if="setting.type === 'number'"
                            :value="setting.value"
                            @change="updateSetting(key, parseInt($event.target.value))"
                            type="number"
                            class="w-24 px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />

                        <!-- TEXTO -->
                        <input 
                            v-else-if="setting.type === 'string'"
                            :value="setting.value"
                            @change="updateSetting(key, $event.target.value)"
                            type="text"
                            class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white min-w-[200px]"
                        />

                        <!-- JSON -->
                        <span v-else-if="setting.type === 'json'" class="text-sm text-gray-500">
                            {{ JSON.stringify(setting.value) }}
                        </span>

                        <!-- IMAGEN -->
                        <div v-else-if="setting.type === 'image'" class="flex items-center gap-2">
                            <div v-if="setting.value" class="relative group">
                                <img :src="getImageUrl(setting.value)" class="w-16 h-16 object-cover rounded-lg border border-gray-200" />
                                <button 
                                    @click="removeImage(key)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    ✕
                                </button>
                            </div>
                            <label class="cursor-pointer px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">
                                📤 Subir
                                <input type="file" class="hidden" accept="image/*" @change="uploadImage($event, key)" />
                            </label>
                        </div>

                        <span v-else class="text-sm text-gray-500">Tipo no soportado</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-3xl p-6">
            <p class="text-sm text-blue-700 dark:text-blue-300 text-center">
                💡 Las configuraciones globales afectan a <strong>todos los usuarios</strong> del sistema.
            </p>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

// =============================================
// STATE
// =============================================
const settings = ref({});

// =============================================
// COMPUTED
// =============================================
const faviconPreview = computed(() => {
    const path = settings.value?.appearance?.favicon?.value;
    return path ? getImageUrl(path) : null;
});

const loginBackgroundPreview = computed(() => {
    const path = settings.value?.appearance?.login_background?.value;
    return path ? getImageUrl(path) : null;
});

const otherSettings = computed(() => {
    const result = {};
    for (const [groupName, group] of Object.entries(settings.value)) {
        if (groupName !== 'appearance') {
            result[groupName] = group;
        }
    }
    return result;
});

// =============================================
// UTILIDADES
// =============================================
const getImageUrl = (path) => {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    if (path.startsWith('/')) return path;
    if (path.startsWith('settings/')) {
        return `${window.location.origin}/storage/${path}`;
    }
    return `${window.location.origin}/storage/${path}`;
};

const getGroupLabel = (group) => {
    const labels = {
        appearance: '🎨 Apariencia',
        security: '🔐 Seguridad',
        registration: '📝 Registro',
        notifications: '📧 Notificaciones',
        academic: '🎓 Académico',
        maintenance: '🛠️ Mantenimiento',
    };
    return labels[group] || group;
};

// =============================================
// CRUD SETTINGS
// =============================================
const loadSettings = async () => {
    try {
        const { data } = await axios.get('/api/admin/settings');
        settings.value = data;
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar las configuraciones' });
    }
};

const updateSetting = async (key, value) => {
    try {
        await axios.put(`/api/admin/settings/${key}`, { value });
        await loadSettings();
        Swal.fire({
            icon: 'success',
            title: '✅ Actualizado',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({ icon: 'error', title: '❌ Error', text: 'Error al actualizar' });
    }
};

// =============================================
// IMÁGENES
// =============================================
const uploadImage = async (event, key) => {
    const file = event.target.files[0];
    if (!file) return;

    const maxSize = key === 'favicon' ? 2 * 1024 * 1024 : 5 * 1024 * 1024;
    if (file.size > maxSize) {
        Swal.fire({
            icon: 'warning',
            title: 'Archivo muy grande',
            text: key === 'favicon' ? 'Máximo 2MB' : 'Máximo 5MB',
        });
        event.target.value = '';
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();
    const allowed = ['ico', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
    if (!allowed.includes(extension)) {
        Swal.fire({
            icon: 'warning',
            title: 'Formato no permitido',
            text: 'Permitidos: ' + allowed.join(', '),
        });
        event.target.value = '';
        return;
    }

    // Mostrar loading
    if (key === 'favicon') {
        Swal.fire({
            title: '🔄 Procesando',
            text: 'Generando favicon...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    }

    const formData = new FormData();
    formData.append('image', file);

    try {
        await axios.post(`/api/admin/settings/${key}/image`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        await loadSettings();
        
        Swal.fire({
            icon: 'success',
            title: '✅ Imagen actualizada',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        const msg = error.response?.data?.message || 'Error al subir la imagen';
        Swal.fire({ icon: 'error', title: '❌ Error', text: msg });
    }
};

const removeImage = async (key) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar imagen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });
    
    if (!confirm.isConfirmed) return;

    try {
        await axios.delete(`/api/admin/settings/${key}/image`);
        await loadSettings();
        Swal.fire({
            icon: 'success',
            title: '✅ Imagen eliminada',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        Swal.fire({ icon: 'error', title: '❌ Error', text: 'Error al eliminar la imagen' });
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(loadSettings);
</script>