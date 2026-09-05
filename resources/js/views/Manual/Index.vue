<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- ========================================== -->
        <!-- HEADER                                     -->
        <!-- ========================================== -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-6 md:p-8 mb-8 text-white shadow-2xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black flex items-center gap-3">
                        📚 Manual de Usuario
                    </h1>
                    <p class="text-purple-100 mt-2 text-sm md:text-base">
                        {{ welcomeMessage }}
                    </p>
                </div>
                
                <!-- Acciones -->
                <div class="flex flex-wrap gap-2 no-print">
                    <button 
                        @click="printManual"
                        class="px-3 md:px-4 py-1.5 md:py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-xs md:text-sm flex items-center gap-2"
                        title="Imprimir manual"
                    >
                        🖨️ <span class="hidden sm:inline">Imprimir</span>
                    </button>
                    <button 
                        @click="downloadPDF"
                        class="px-3 md:px-4 py-1.5 md:py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-xs md:text-sm flex items-center gap-2"
                        title="Descargar PDF"
                    >
                        📥 <span class="hidden sm:inline">Descargar PDF</span>
                    </button>
                    <button 
                        @click="toggleFullscreen"
                        class="px-3 md:px-4 py-1.5 md:py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-xs md:text-sm flex items-center gap-2"
                        title="Pantalla completa"
                    >
                        ⛶ <span class="hidden sm:inline">Pantalla completa</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- CONTENIDO PRINCIPAL                        -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar - Índice (visible en desktop) -->
            <div class="lg:col-span-1 no-print">
                <!-- Botón toggle para móvil -->
                <button 
                    @click="showSidebar = !showSidebar"
                    class="lg:hidden w-full mb-4 px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-lg flex items-center justify-between"
                >
                    <span>📑 Índice del Manual</span>
                    <span>{{ showSidebar ? '▲' : '▼' }}</span>
                </button>

                <!-- Sidebar -->
                <div 
                    :class="[
                        'transition-all duration-300 overflow-hidden',
                        showSidebar ? 'max-h-[2000px] opacity-100' : 'max-h-0 opacity-0 lg:max-h-[2000px] lg:opacity-100'
                    ]"
                >
                    <ManualSidebar />
                </div>
            </div>

            <!-- Visor -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <!-- Barra de búsqueda -->
                    <SearchBar ref="searchBarRef" />

                    <!-- Contenido -->
                    <div class="p-4 md:p-6">
                        <ManualViewer />
                    </div>

                    <!-- Footer del visor -->
                    <div class="px-4 md:px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex justify-between items-center text-xs text-gray-400 no-print">
                        <span>
                            📖 {{ manualStore.totalSections }} secciones disponibles
                        </span>
                        <span>
                            {{ manualStore.currentSectionData ? 'Leyendo: ' + manualStore.currentSectionData.title : 'Selecciona una sección' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BOTÓN DE AYUDA FLOTANTE                    -->
        <!-- ========================================== -->
        <div class="fixed bottom-6 right-6 z-50 no-print">
            <button 
                @click="showHelp = !showHelp"
                class="w-12 h-12 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg transition flex items-center justify-center text-xl"
                title="Ayuda"
            >
                ❓
            </button>
            
            <!-- Tooltip de ayuda -->
            <div 
                v-if="showHelp"
                class="absolute bottom-16 right-0 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-4 border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-start gap-3">
                    <span class="text-2xl">💡</span>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">¿Cómo usar el manual?</h4>
                        <ul class="text-xs text-gray-600 dark:text-gray-400 mt-2 space-y-1 list-disc list-inside">
                            <li>Usa el índice para navegar</li>
                            <li>Presiona <kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[10px]">Ctrl+K</kbd> para buscar</li>
                            <li>Usa los botones <span class="font-bold">⬅️ Anterior</span> y <span class="font-bold">Siguiente ➡️</span></li>
                            <li>El contenido se adapta a tu rol</li>
                        </ul>
                        <button 
                            @click="showHelp = false"
                            class="mt-2 text-xs text-purple-600 hover:text-purple-700"
                        >
                            Entendido ✅
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useManualStore } from '@/stores/manual';
import ManualSidebar from './components/ManualSidebar.vue';
import ManualViewer from './components/ManualViewer.vue';
import SearchBar from './components/SearchBar.vue';

// =============================================
// STORES
// =============================================
const authStore = useAuthStore();
const manualStore = useManualStore();

// =============================================
// REFS
// =============================================
const showSidebar = ref(true);
const showHelp = ref(false);
const searchBarRef = ref(null);

// =============================================
// COMPUTED
// =============================================

// Mensaje de bienvenida personalizado
const welcomeMessage = computed(() => {
    const name = authStore.user?.name || 'Usuario';
    const role = authStore.user?.roles?.[0] || '';
    
    const roleMap = {
        'super-admin': '👑 Super-Administrador',
        'admin': '🔧 Administrador',
        'academico': '📚 Académico',
        'docente': '👨‍🏫 Docente',
        'estudiante': '👨‍🎓 Estudiante'
    };
    
    const roleLabel = roleMap[role] || 'Usuario';
    const total = manualStore.totalSections;
    
    return `Bienvenido ${name} · ${roleLabel} · ${total} secciones disponibles`;
});

// =============================================
// FUNCIONES
// =============================================

// Imprimir manual
const printManual = () => {
    window.print();
};

// Actualizar la función de descarga
const downloadPDF = async () => {
    try {
        // ✅ Obtener el rol principal del usuario
        const authStore = useAuthStore();
        const primaryRole = authStore.primaryRole || 'academico';
        
        // ✅ Abrir la descarga en nueva pestaña
        const url = `/api/manual-pdfs/download/${primaryRole}`;
        window.open(url, '_blank');

        Swal.fire({
            icon: 'success',
            title: '✅ PDF descargado',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

    } catch (error) {
        console.error('Error descargando PDF:', error);
        Swal.fire({
            icon: 'warning',
            title: 'PDF no disponible',
            text: 'El manual en PDF aún no está disponible para tu rol',
            confirmButtonColor: '#7C3AED'
        });
    }
};
// Pantalla completa
const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {
            // Si falla, ignorar
        });
    } else {
        document.exitFullscreen().catch(() => {
            // Si falla, ignorar
        });
    }
};

// =============================================
// LIFECYCLE
// =============================================

onMounted(() => {
    // Si no hay sección seleccionada, ir a la primera
    if (!manualStore.currentSectionData && manualStore.allSections.length > 0) {
        manualStore.goToFirstSection();
    }

    // Cerrar ayuda después de 10 segundos
    setTimeout(() => {
        showHelp.value = false;
    }, 10000);
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
    
    .grid {
        display: block !important;
    }
    
    .lg\\:col-span-1, .lg\\:col-span-3 {
        grid-column: auto !important;
    }
}

/* ============================================= */
/* RESPONSIVE                                    */
/* ============================================= */

@media (max-width: 1024px) {
    .max-h-0 {
        max-height: 0;
    }
    
    .max-h-\[2000px\] {
        max-height: 2000px;
    }
}

/* ============================================= */
/* ESTILOS DEL TOOLTIP                          */
/* ============================================= */

kbd {
    font-family: 'Courier New', monospace;
    padding: 1px 6px;
    border-radius: 4px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    font-size: 10px;
}

:deep(.dark) kbd {
    background: #1f2937;
    border-color: #4b5563;
    color: #9ca3af;
}

/* ============================================= */
/* ANIMACIONES                                   */
/* ============================================= */

.fixed {
    transition: all 0.3s ease;
}

.fixed button:hover {
    transform: scale(1.05);
}

/* Tooltip animation */
.fixed > div {
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>