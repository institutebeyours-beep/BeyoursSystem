<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- ========================================== -->
        <!-- HEADER                                     -->
        <!-- ========================================== -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-6 md:p-8 mb-8 text-white shadow-2xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black flex items-center gap-3">
                        📄 Manual del Rol
                    </h1>
                    <p class="text-purple-100 mt-2 text-sm md:text-base">
                        {{ manualPdfStore.manual?.file_name || 'Cargando manual...' }}
                    </p>
                    <p v-if="manualPdfStore.hasManual" class="text-purple-200 text-xs mt-1">
                        Versión {{ manualPdfStore.manualVersion }} · 
                        {{ formatFileSize(manualPdfStore.manual?.file_size) }}
                    </p>
                </div>
                
                <!-- Acciones -->
                <div class="flex flex-wrap gap-2 no-print">
                    <button 
                        @click="downloadManual"
                        v-if="manualPdfStore.hasManual"
                        class="px-3 md:px-4 py-1.5 md:py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-xs md:text-sm flex items-center gap-2"
                        title="Descargar PDF"
                    >
                        📥 <span class="hidden sm:inline">Descargar</span>
                    </button>
                    <button 
                        @click="toggleFullscreen"
                        class="px-3 md:px-4 py-1.5 md:py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-xs md:text-sm flex items-center gap-2"
                        title="Pantalla completa"
                    >
                        ⛶ <span class="hidden sm:inline">Pantalla completa</span>
                    </button>
                    <button 
                        @click="$router.back()"
                        class="px-3 md:px-4 py-1.5 md:py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-xs md:text-sm flex items-center gap-2"
                        title="Volver"
                    >
                        ⬅ <span class="hidden sm:inline">Volver</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- VISOR DEL PDF                              -->
        <!-- ========================================== -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Cargando -->
            <div v-if="manualPdfStore.loading" class="flex items-center justify-center h-[600px]">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
                    <p class="mt-4 text-gray-500 dark:text-gray-400">Cargando manual...</p>
                </div>
            </div>

            <!-- Visor del PDF -->
            <div v-else-if="manualPdfStore.hasManual && manualPdfStore.isManualActive" class="relative">
                <iframe 
                    :src="manualPdfStore.manualUrl"
                    class="w-full h-[700px] border-0"
                    frameborder="0"
                ></iframe>
                
                <!-- Footer del visor -->
                <div class="px-4 md:px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex justify-between items-center text-xs text-gray-400 no-print">
                    <span>
                        📄 {{ manualPdfStore.manual?.file_name || 'Manual' }}
                    </span>
                    <div class="flex items-center gap-4">
                        <span>Versión: {{ manualPdfStore.manualVersion || '1.0' }}</span>
                        <span class="w-px h-3 bg-gray-300 dark:bg-gray-600"></span>
                        <span>{{ formatFileSize(manualPdfStore.manual?.file_size) }}</span>
                    </div>
                </div>
            </div>

            <!-- No disponible -->
            <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
                <span class="text-6xl block mb-4">📭</span>
                <p class="text-lg font-medium">Manual no disponible</p>
                <p class="text-sm mt-2">No hay manual asignado para tu rol</p>
                <button 
                    @click="$router.back()"
                    class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                >
                    ⬅ Volver
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useManualPdfStore } from '@/stores/manualPdf';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const manualPdfStore = useManualPdfStore();
const authStore = useAuthStore();

// =============================================
// FUNCIONES
// =============================================

// Formatear tamaño de archivo
const formatFileSize = (bytes) => {
    if (!bytes) return '0 KB';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Descargar manual
const downloadManual = () => {
    if (!manualPdfStore.hasManual) return;
    
    const link = document.createElement('a');
    link.href = manualPdfStore.manualUrl;
    link.download = manualPdfStore.manual?.file_name || 'manual.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Pantalla completa
const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
    } else {
        document.exitFullscreen().catch(() => {});
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(async () => {
    const roleId = route.params.roleId || authStore.user?.role_id;
    
    if (roleId) {
        try {
            await manualPdfStore.fetchManualByRoleId(roleId);
        } catch (error) {
            console.error('Error al cargar manual:', error);
        }
    }
});
</script>

<style scoped>
/* ============================================= */
/* ESTILOS DE IMPRESIÓN                         */
/* ============================================= */
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-gradient-to-r {
        background: #7c3aed !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .shadow-xl, .shadow-2xl {
        box-shadow: none !important;
    }
    
    .border {
        border: 1px solid #e5e7eb !important;
    }
    
    .rounded-3xl, .rounded-2xl {
        border-radius: 0.5rem !important;
    }
    
    .max-w-7xl {
        max-width: 100% !important;
        padding: 1rem !important;
    }
}

/* ============================================= */
/* ANIMACIONES                                   */
/* ============================================= */
button {
    transition: all 0.2s ease;
}

iframe {
    transition: all 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>