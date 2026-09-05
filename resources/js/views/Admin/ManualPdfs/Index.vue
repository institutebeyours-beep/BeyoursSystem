<template>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">📄 Gestión de PDFs del Manual</h1>
            <p class="text-purple-100 mt-2">Administra los PDFs para cada rol de usuario</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
            <!-- Grid de roles -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="role in roles" 
                    :key="role.id"
                    class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-lg transition"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-2xl">{{ getRoleIcon(role.name) }}</span>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ role.display_name }}</h3>
                    </div>
                    
                    <div v-if="role.pdf" class="text-sm text-gray-600 dark:text-gray-400">
                        <p>📄 {{ role.pdf.file_name }}</p>
                        <p>📦 {{ role.pdf.formatted_size }}</p>
                        <p>📌 v{{ role.pdf.version }}</p>
                        <p class="text-xs text-gray-400">Subido: {{ formatDate(role.pdf.uploaded_at) }}</p>
                        <p class="text-xs text-gray-400">Por: {{ role.pdf.uploader || 'Sistema' }}</p>
                    </div>
                    <p v-else class="text-sm text-gray-400">No hay PDF subido</p>
                    
                    <div class="mt-3 flex gap-2">
                        <button 
                            @click="uploadPDF(role.id)" 
                            class="px-3 py-1 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                        >
                            📤 Subir
                        </button>
                        <button 
                            v-if="role.pdf" 
                            @click="deletePDF(role.pdf.id)" 
                            class="px-3 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                        >
                            🗑️
                        </button>
                        <button 
                            v-if="role.pdf" 
                            @click="downloadPDF(role.name)" 
                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        >
                            📥
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para subir PDF -->
        <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    📤 Subir PDF para {{ selectedRole?.display_name || 'Rol' }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Archivo PDF *
                        </label>
                        <input 
                            type="file" 
                            ref="fileInput"
                            accept=".pdf"
                            @change="handleFileSelect"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                        <p class="text-xs text-gray-500 mt-1">Máximo 10MB</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Versión
                        </label>
                        <input 
                            v-model="uploadVersion"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="1.0"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        @click="closeUploadModal"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="submitUpload"
                        :disabled="!selectedFile || uploading"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
                    >
                        <span v-if="uploading" class="animate-spin inline-block mr-2">⟳</span>
                        📤 Subir
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

// =============================================
// STATE
// =============================================
const roles = ref([]);
const loading = ref(false);
const showUploadModal = ref(false);
const selectedRoleId = ref(null);
const uploadVersion = ref('1.0');
const selectedFile = ref(null);
const uploading = ref(false);
const fileInput = ref(null);

// =============================================
// COMPUTED
// =============================================
const selectedRole = computed(() => {
    return roles.value.find(r => r.id === selectedRoleId.value);
});

// =============================================
// FUNCIONES
// =============================================

const loadData = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/admin/manual-pdfs');
        roles.value = response.data.roles || [];
    } catch (error) {
        console.error('Error cargando datos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los datos'
        });
    } finally {
        loading.value = false;
    }
};

const getRoleIcon = (roleName) => {
    const icons = {
        'super-admin': '👑',
        'admin': '🔧',
        'academico': '📚',
        'docente': '👨‍🏫',
        'estudiante': '👨‍🎓'
    };
    return icons[roleName] || '👤';
};

const uploadPDF = (roleId) => {
    selectedRoleId.value = roleId;
    uploadVersion.value = '1.0';
    selectedFile.value = null;
    showUploadModal.value = true;
};

const closeUploadModal = () => {
    showUploadModal.value = false;
    selectedRoleId.value = null;
    selectedFile.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.type !== 'application/pdf') {
            Swal.fire({
                icon: 'warning',
                title: 'Formato inválido',
                text: 'Solo se permiten archivos PDF'
            });
            return;
        }
        if (file.size > 10485760) {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo demasiado grande',
                text: 'El tamaño máximo es 10MB'
            });
            return;
        }
        selectedFile.value = file;
    }
};

const submitUpload = async () => {
    if (!selectedFile.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona un archivo',
            text: 'Debes seleccionar un archivo PDF'
        });
        return;
    }

    try {
        uploading.value = true;
        
        const formData = new FormData();
        formData.append('role_id', selectedRoleId.value);
        formData.append('pdf', selectedFile.value);
        formData.append('version', uploadVersion.value);

        await axios.post('/api/admin/manual-pdfs/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        Swal.fire({
            icon: 'success',
            title: '✅ PDF subido exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        closeUploadModal();
        await loadData();

    } catch (error) {
        console.error('Error subiendo PDF:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo subir el PDF'
        });
    } finally {
        uploading.value = false;
    }
};

const deletePDF = async (pdfId) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar PDF?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (confirm.isConfirmed) {
        try {
            await axios.delete(`/api/admin/manual-pdfs/${pdfId}`);
            
            Swal.fire({
                icon: 'success',
                title: '✅ PDF eliminado',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            
            await loadData();
        } catch (error) {
            console.error('Error eliminando PDF:', error);
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: 'No se pudo eliminar el PDF'
            });
        }
    }
};

const downloadPDF = (roleName) => {
    window.open(`/api/manual-pdfs/download/${roleName}`, '_blank');
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadData();
});
</script>