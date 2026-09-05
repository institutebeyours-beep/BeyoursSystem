<template>
    <div class="manual-viewer">
        <!-- ========================================== -->
        <!-- RESULTADOS DE BÚSQUEDA                      -->
        <!-- ========================================== -->
        <div v-if="manualStore.searchQuery && manualStore.searchResults.length > 0">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    🔍 Resultados de búsqueda: "{{ manualStore.searchQuery }}"
                </h2>
                <button 
                    @click="manualStore.resetSearch()"
                    class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400"
                >
                    ✕ Limpiar
                </button>
            </div>
            
            <div 
                v-for="result in manualStore.searchResults" 
                :key="result.id"
                class="mb-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition border border-transparent hover:border-purple-300 dark:hover:border-purple-800"
                @click="manualStore.setCurrentSection(result.id)"
            >
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">{{ result.icon }}</span>
                    <h3 class="text-md font-semibold text-purple-600 dark:text-purple-400">
                        {{ result.title }}
                    </h3>
                    <span class="text-xs text-gray-400">({{ result.category }})</span>
                </div>
                <div 
                    class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 prose prose-sm max-w-none"
                    v-html="result.content"
                ></div>
                <div class="mt-2 text-xs text-purple-600 dark:text-purple-400">
                    📖 Haz clic para leer más →
                </div>
            </div>

            <div v-if="manualStore.searchResults.length === 0" class="text-center py-12">
                <span class="text-4xl block mb-4">🔍</span>
                <p class="text-gray-500 dark:text-gray-400">No se encontraron resultados para "{{ manualStore.searchQuery }}"</p>
                <button 
                    @click="manualStore.resetSearch()"
                    class="mt-4 text-sm text-purple-600 hover:text-purple-700"
                >
                    ✕ Limpiar búsqueda
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECCIÓN ACTUAL                             -->
        <!-- ========================================== -->
        <div v-else-if="manualStore.currentSectionData">
            <!-- Encabezado de la sección -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-3xl">{{ manualStore.currentSectionData.icon }}</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ manualStore.currentSectionData.title }}
                    </h2>
                </div>
                
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Badge de categoría -->
                    <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">
                        📂 {{ manualStore.currentSectionData.category || 'General' }}
                    </span>
                    
                    <!-- Badge de rol -->
                    <span 
                        v-if="!manualStore.currentSectionData.roles.includes('all')"
                        class="text-xs px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full"
                    >
                        {{ getRoleLabel }}
                    </span>
                    
                    <!-- Indicador de sección -->
                    <span class="text-xs text-gray-400">
                        Sección {{ navigationInfo.currentIndex }} de {{ navigationInfo.total }}
                    </span>
                </div>
            </div>

            <!-- Contenido -->
            <div 
                class="prose prose-purple dark:prose-invert max-w-none manual-content"
                v-html="manualStore.currentSectionData.content"
            ></div>

            <!-- Navegación entre secciones -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button 
                    @click="manualStore.previousSection()"
                    :disabled="!navigationInfo.hasPrevious"
                    class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="navigationInfo.hasPrevious 
                        ? 'text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' 
                        : 'text-gray-400 dark:text-gray-600'"
                >
                    ⬅️ Anterior
                </button>
                
                <span class="text-xs text-gray-400">
                    {{ navigationInfo.currentIndex }} / {{ navigationInfo.total }}
                </span>
                
                <button 
                    @click="manualStore.nextSection()"
                    :disabled="!navigationInfo.hasNext"
                    class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="navigationInfo.hasNext 
                        ? 'text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' 
                        : 'text-gray-400 dark:text-gray-600'"
                >
                    Siguiente ➡️
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SIN SELECCIÓN                              -->
        <!-- ========================================== -->
        <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
            <span class="text-6xl block mb-4">📚</span>
            <p class="text-lg font-medium">Selecciona una sección del índice para comenzar</p>
            <p class="text-sm mt-2">O usa la barra de búsqueda para encontrar temas específicos</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useManualStore } from '@/stores/manual';

// =============================================
// STORE
// =============================================
const manualStore = useManualStore();

// =============================================
// COMPUTED
// =============================================

// Información de navegación
const navigationInfo = computed(() => {
    return manualStore.navigationInfo;
});

// Etiqueta de rol
const getRoleLabel = computed(() => {
    const roles = manualStore.currentSectionData?.roles || [];
    const roleMap = {
        'super-admin': '👑 Super-Admin',
        'admin': '🔧 Administrador',
        'academico': '📚 Académico'
    };
    const filtered = roles.filter(r => r !== 'all');
    return filtered.map(r => roleMap[r] || r).join(', ');
});
</script>

<style scoped>
/* ============================================= */
/* ESTILOS PARA EL CONTENIDO DEL MANUAL          */
/* ============================================= */

:deep(.manual-content) {
    /* Títulos */
    h1, h2, h3, h4, h5, h6 {
        color: #1f2937;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    h2 {
        font-size: 1.5rem;
        color: #7c3aed;
    }

    h3 {
        font-size: 1.25rem;
        color: #6b21a8;
    }

    /* Párrafos */
    p {
        margin-bottom: 1rem;
        line-height: 1.75;
        color: #374151;
    }

    /* Listas */
    ul, ol {
        margin: 1rem 0;
        padding-left: 1.5rem;
    }

    ul li, ol li {
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }

    /* Código */
    code {
        background: #f3f4f6;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        color: #7c3aed;
    }

    /* Bloques de código */
    pre {
        background: #1f2937;
        color: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1rem 0;
    }

    pre code {
        background: transparent;
        color: inherit;
        padding: 0;
    }

    /* Tarjetas de ejemplo */
    .bg-yellow-50, .bg-blue-50, .bg-green-50, .bg-red-50 {
        padding: 1rem;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }

    .bg-yellow-50 {
        background-color: #fefce8;
        border: 1px solid #fcd34d;
    }

    .bg-blue-50 {
        background-color: #eff6ff;
        border: 1px solid #93c5fd;
    }

    .bg-green-50 {
        background-color: #f0fdf4;
        border: 1px solid #86efac;
    }

    .bg-red-50 {
        background-color: #fef2f2;
        border: 1px solid #fca5a5;
    }

    /* Enlaces */
    a {
        color: #7c3aed;
        text-decoration: underline;
    }

    a:hover {
        color: #6b21a8;
    }
}

/* ============================================= */
/* MODO OSCURO                                   */
/* ============================================= */
:deep(.dark) .manual-content {
    h1, h2, h3, h4, h5, h6 {
        color: #f3f4f6;
    }

    h2 {
        color: #a78bfa;
    }

    h3 {
        color: #8b5cf6;
    }

    p {
        color: #d1d5db;
    }

    code {
        background: #374151;
        color: #a78bfa;
    }

    pre {
        background: #111827;
        color: #f3f4f6;
    }

    .bg-yellow-50 {
        background-color: #78350f;
        border-color: #f59e0b;
    }

    .bg-blue-50 {
        background-color: #172554;
        border-color: #3b82f6;
    }

    .bg-green-50 {
        background-color: #064e3b;
        border-color: #10b981;
    }

    .bg-red-50 {
        background-color: #7f1d1d;
        border-color: #ef4444;
    }
}

/* ============================================= */
/* ANIMACIONES                                   */
/* ============================================= */
button {
    transition: all 0.2s ease;
}

.manual-viewer {
    animation: fadeIn 0.3s ease;
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

/* Límite de líneas para resultados */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>