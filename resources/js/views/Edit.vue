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
                            ✏️ Editar Perfil
                        </h1>
                        <p class="text-gray-300 text-xs sm:text-sm font-medium mt-0.5 sm:mt-1">
                            Actualiza tu información personal
                        </p>
                    </div>
                    <router-link 
                        to="/profile" 
                        class="px-4 py-2 sm:px-6 sm:py-2.5 bg-white/20 hover:bg-white/30 rounded-xl font-bold text-sm sm:text-base transition-all hover:scale-105 backdrop-blur-sm flex items-center gap-2"
                    >
                        ⬅️ Volver
                    </router-link>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TARJETA DE EDICIÓN                        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 sm:p-6">
                    <!-- ALERTA INFORMATIVA -->
                    <div class="flex items-center gap-3 p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl mb-6">
                        <span class="text-2xl">ℹ️</span>
                        <div>
                            <p class="text-sm font-bold text-blue-700 dark:text-blue-300">Información Personal</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">Modifica solo los campos que necesites actualizar.</p>
                        </div>
                    </div>

                    <form @submit.prevent="updateProfile">
                        <!-- ========================================== -->
                        <!-- FOTO DE PERFIL CON CÁMARA                 -->
                        <!-- ========================================== -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">🖼️ Foto de Perfil</h4>
                            
                            <div class="flex flex-col sm:flex-row items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                                <!-- Vista previa del avatar -->
                                <div class="relative">
                                    <!-- ✅ Usar getImageUrl() para mostrar la imagen -->
                                    <img 
                                        v-if="form.profile_image" 
                                        :src="getImageUrl(form.profile_image)" 
                                        alt="Profile" 
                                        class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-600 shadow-lg"
                                    />
                                    <div 
                                        v-else 
                                        class="w-24 h-24 rounded-full flex items-center justify-center text-3xl font-bold text-white border-4 border-white dark:border-gray-600 shadow-lg"
                                        :style="{ background: avatarColor }"
                                    >
                                        {{ getInitials(form.name) }}
                                    </div>
                                    
                                    <!-- Botón eliminar -->
                                    <button 
                                        v-if="form.profile_image" 
                                        @click="removeImage" 
                                        type="button"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm hover:bg-red-600 transition shadow-lg"
                                    >
                                        ✕
                                    </button>
                                </div>
                                
                                <!-- Opciones de carga -->
                                <div class="flex-1 text-center sm:text-left">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ form.profile_image ? 'Foto actual' : 'Sin foto de perfil' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mb-3">JPG, PNG o GIF. Máx 2MB</p>
                                    
                                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
                                        <!-- 📤 Subir desde archivos -->
                                        <label class="cursor-pointer px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm hover:bg-indigo-700 transition">
                                            📤 Subir Foto
                                            <input 
                                                type="file" 
                                                class="hidden" 
                                                accept="image/*" 
                                                @change="uploadImage($event)"
                                            />
                                        </label>
                                        
                                        <!-- 📷 Tomar foto con cámara -->
                                        <button 
                                            @click="openCamera" 
                                            type="button"
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold text-sm hover:bg-green-700 transition"
                                        >
                                            📷 Tomar Foto
                                        </button>
                                        
                                        <button 
                                            v-if="form.profile_image" 
                                            @click="removeImage" 
                                            type="button"
                                            class="px-4 py-2 bg-red-500 text-white rounded-lg font-bold text-sm hover:bg-red-600 transition"
                                        >
                                            🗑️ Eliminar
                                        </button>
                                    </div>
                                    
                                    <!-- Indicador de soporte de cámara -->
                                    <p class="text-[10px] text-gray-400 mt-2">
                                        {{ cameraSupported ? '📷 Cámara disponible' : '📷 Cámara no disponible en este dispositivo' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- DATOS PERSONALES                         -->
                        <!-- ========================================== -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">👤 Datos Personales</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.name" 
                                        required 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Tu nombre"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Apellido Paterno
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.lastname" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Tu apellido paterno"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Apellido Materno
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.second_lastname" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Tu apellido materno"
                                    >
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Fecha de Nacimiento
                                    </label>
                                    <input 
                                        type="date" 
                                        v-model="form.birth_date" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Dirección
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.address" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Tu dirección completa"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- DATOS DE CONTACTO                         -->
                        <!-- ========================================== -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">📱 Contacto</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        v-model="form.email" 
                                        required 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm cursor-not-allowed"
                                        disabled
                                    >
                                    <p class="text-xs text-gray-400 mt-1">El email no se puede modificar. Contacta al administrador.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Teléfono
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.phone" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="(123) 456-7890"
                                    >
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Celular
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.cellphone" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="(123) 456-7890"
                                >
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- BOTONES DE ACCIÓN                         -->
                        <!-- ========================================== -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                            <router-link 
                                to="/profile" 
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition text-center"
                            >
                                Cancelar
                            </router-link>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm hover:bg-indigo-700 transition disabled:opacity-50"
                                :disabled="saving"
                            >
                                {{ saving ? '⏳ Guardando...' : '💾 Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECCIÓN DE SEGURIDAD                      -->
            <!-- ========================================== -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 sm:p-6">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">🔐 Seguridad</h3>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Cambiar Contraseña</p>
                            <p class="text-xs text-gray-400">Actualiza tu contraseña de acceso al sistema.</p>
                        </div>
                        <button 
                            @click="changePassword" 
                            class="px-4 py-2 bg-amber-500 text-white rounded-lg font-bold text-sm hover:bg-amber-600 transition whitespace-nowrap"
                        >
                            🔑 Cambiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';

const router = useRouter();
const authStore = useAuthStore();
const settingsStore = useSettingsStore();

// =============================================
// COMPUTED
// =============================================
const user = computed(() => authStore.user);

const bgColor = computed(() => {
    return settingsStore.getSetting('background_color') || '#f3f4f6';
});

const headerGradient = computed(() => {
    const primaryColor = settingsStore.getSetting('primary_color') || '#6366f1';
    const secondaryColor = settingsStore.getSetting('secondary_color') || '#8b5cf6';
    return `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)`;
});

const avatarColor = computed(() => {
    const name = form.name || 'Usuario';
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
// STATE
// =============================================
const saving = ref(false);
const uploading = ref(false);
const cameraSupported = ref(false);

const form = reactive({
    name: '',
    lastname: '',
    second_lastname: '',
    email: '',
    phone: '',
    cellphone: '',
    birth_date: '',
    address: '',
    profile_image: '',
});

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
// FUNCIONES
// =============================================
const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const loadUserData = () => {
    const userData = authStore.user;
    if (userData) {
        form.name = userData.name || '';
        form.lastname = userData.lastname || '';
        form.second_lastname = userData.second_lastname || '';
        form.email = userData.email || '';
        form.phone = userData.phone || '';
        form.cellphone = userData.cellphone || '';
        form.birth_date = userData.birth_date || '';
        form.address = userData.address || '';
        form.profile_image = userData.profile_image || '';
        
        console.log('📝 Datos cargados en el formulario:', {
            name: form.name,
            lastname: form.lastname,
            second_lastname: form.second_lastname,
            email: form.email,
            phone: form.phone,
            cellphone: form.cellphone,
            birth_date: form.birth_date,
            address: form.address,
            profile_image: form.profile_image ? '✅ Tiene imagen' : '❌ Sin imagen'
        });
    }
};

// =============================================
// 📷 FUNCIONES DE CÁMARA
// =============================================
const checkCameraSupport = () => {
    cameraSupported.value = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
    console.log('📷 Soporte de cámara:', cameraSupported.value);
};

const openCamera = () => {
    if (!cameraSupported.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Cámara no disponible',
            text: 'Tu dispositivo o navegador no soporta la cámara. Usa la opción "Subir Foto".',
        });
        return;
    }

    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    
    if (isMobile) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.capture = 'environment';
        input.onchange = (event) => {
            uploadImage(event);
        };
        input.click();
    } else {
        openDesktopCamera();
    }
};

const openDesktopCamera = () => {
    Swal.fire({
        title: '📷 Tomar Foto',
        html: `
            <div class="relative">
                <video id="camera-stream" autoplay playsinline class="w-full rounded-lg bg-black"></video>
                <button id="capture-photo" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 px-6 py-3 bg-white text-gray-800 rounded-full font-bold shadow-lg hover:bg-gray-100 transition">
                    📸 Capturar
                </button>
                <canvas id="photo-canvas" class="hidden"></canvas>
            </div>
            <p class="text-xs text-gray-400 mt-2">Asegúrate de permitir el acceso a la cámara.</p>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        cancelButtonColor: '#6b7280',
        didOpen: async () => {
            try {
                const video = document.getElementById('camera-stream');
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user' }, 
                    audio: false 
                });
                video.srcObject = stream;
                await video.play();

                document.getElementById('capture-photo').addEventListener('click', () => {
                    const canvas = document.getElementById('photo-canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    const imageData = canvas.toDataURL('image/jpeg', 0.9);
                    
                    stream.getTracks().forEach(track => track.stop());
                    Swal.close();
                    processImage(imageData);
                });
            } catch (error) {
                console.error('❌ Error al acceder a la cámara:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de cámara',
                    text: 'No se pudo acceder a la cámara. Verifica los permisos.',
                });
            }
        },
        didClose: () => {
            const video = document.getElementById('camera-stream');
            if (video && video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        }
    });
};

const processImage = async (imageData) => {
    try {
        form.profile_image = imageData;
        uploading.value = true;
        
        const response = await axios.post('/api/profile/upload-image', {
            image: imageData
        });

        // ✅ Usar image_path en lugar de image_url
        if (response.data.image_path) {
            form.profile_image = response.data.image_path;
            
            if (authStore.user) {
                authStore.user.profile_image = response.data.image_path;
                localStorage.setItem('user', JSON.stringify(authStore.user));
            }
            
            Swal.fire({
                icon: 'success',
                title: '✅ Foto capturada y subida',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    } catch (error) {
        console.error('❌ Error al subir la foto:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'Error al subir la foto',
        });
        loadUserData();
    }
    uploading.value = false;
};

// =============================================
// 📤 SUBIR IMAGEN DESDE ARCHIVO
// =============================================
const uploadImage = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'warning',
            title: 'Archivo muy grande',
            text: 'La imagen debe ser menor a 2MB',
        });
        return;
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            icon: 'warning',
            title: 'Formato no válido',
            text: 'Solo se permiten JPG, PNG, GIF o WEBP',
        });
        return;
    }

    uploading.value = true;
    const reader = new FileReader();
    
    reader.onload = async (e) => {
        try {
            const response = await axios.post('/api/profile/upload-image', {
                image: e.target.result
            });

            // ✅ Usar image_path en lugar de image_url
            if (response.data.image_path) {
                form.profile_image = response.data.image_path;
                
                if (authStore.user) {
                    authStore.user.profile_image = response.data.image_path;
                    localStorage.setItem('user', JSON.stringify(authStore.user));
                }
                
                Swal.fire({
                    icon: 'success',
                    title: '✅ Imagen actualizada',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        } catch (error) {
            console.error('❌ Error al subir imagen:', error);
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'Error al subir la imagen',
            });
            loadUserData();
        }
        uploading.value = false;
    };
    
    reader.onerror = () => {
        uploading.value = false;
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: 'Error al leer el archivo',
        });
    };
    
    reader.readAsDataURL(file);
};

// =============================================
// 🗑️ ELIMINAR IMAGEN
// =============================================
const removeImage = async () => {
    const result = await Swal.fire({
        title: '¿Eliminar foto de perfil?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        try {
            await axios.delete('/api/profile/remove-image');
            
            form.profile_image = '';
            if (authStore.user) {
                authStore.user.profile_image = null;
                localStorage.setItem('user', JSON.stringify(authStore.user));
            }
            
            Swal.fire({
                icon: 'success',
                title: '✅ Imagen eliminada',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'Error al eliminar la imagen',
            });
        }
    }
};

// =============================================
// 💾 ACTUALIZAR PERFIL
// =============================================
const updateProfile = async () => {
    saving.value = true;
    try {
        const payload = {
            name: form.name,
            lastname: form.lastname,
            second_lastname: form.second_lastname,
            phone: form.phone,
            cellphone: form.cellphone,
            birth_date: form.birth_date,
            address: form.address,
        };
        
        console.log('📤 Enviando al backend:', payload);
        
        const response = await axios.put('/api/profile', payload);
        
        console.log('📥 Respuesta del backend:', response.data);
        
        if (response.data.user) {
            response.data.user.profile_image = form.profile_image;
            authStore.user = response.data.user;
            localStorage.setItem('user', JSON.stringify(authStore.user));
            
            console.log('✅ Usuario actualizado en el store:', authStore.user);
        }
        
        Swal.fire({
            icon: 'success',
            title: '✅ Perfil actualizado',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        setTimeout(() => {
            router.push('/profile');
        }, 500);
        
    } catch (error) {
        console.error('❌ Error al actualizar perfil:', error);
        console.error('❌ Detalles del error:', error.response?.data);
        
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'Error al actualizar el perfil',
        });
    }
    saving.value = false;
};

// =============================================
// 🔑 CAMBIAR CONTRASEÑA
// =============================================
const changePassword = async () => {
    const { value: password } = await Swal.fire({
        title: '🔑 Cambiar Contraseña',
        html: `
            <div class="text-left">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva Contraseña</label>
                <input id="password" type="password" class="swal2-input w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Mínimo 8 caracteres">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-3 mb-1">Confirmar Contraseña</label>
                <input id="password_confirmation" type="password" class="swal2-input w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Repite la contraseña">
            </div>
        `,
        confirmButtonText: 'Cambiar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        preConfirm: () => {
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            
            if (!password || password.length < 8) {
                Swal.showValidationMessage('La contraseña debe tener al menos 8 caracteres');
                return;
            }
            
            if (password !== password_confirmation) {
                Swal.showValidationMessage('Las contraseñas no coinciden');
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
// LIFECYCLE
// =============================================
onMounted(() => {
    loadUserData();
    checkCameraSupport();
});
</script>