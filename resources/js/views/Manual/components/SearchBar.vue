<template>
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
        <div class="flex items-center gap-3">
            <!-- Campo de búsqueda -->
            <div class="flex-1 relative">
                <!-- Icono de búsqueda -->
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                    🔍
                </span>
                
                <!-- Input -->
                <input 
                    ref="searchInput"
                    v-model="manualStore.searchQuery"
                    type="text"
                    :placeholder="placeholder"
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                    @keydown.esc="manualStore.resetSearch()"
                    @keydown.ctrl.k.prevent="focusSearch"
                />
                
                <!-- Botón limpiar -->
                <button 
                    v-if="manualStore.searchQuery"
                    @click="manualStore.resetSearch()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                    title="Limpiar búsqueda"
                >
                    ✕
                </button>

                <!-- Atajo de teclado -->
                <div v-if="!manualStore.searchQuery" class="absolute right-3 top-1/2 -translate-y-1/2 hidden sm:block">
                    <kbd class="px-2 py-0.5 text-[10px] font-mono text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                        Ctrl+K
                    </kbd>
                </div>
            </div>

            <!-- Contador de resultados -->
            <div v-if="manualStore.searchQuery" class="flex-shrink-0">
                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    {{ manualStore.searchResults.length }} 
                    {{ manualStore.searchResults.length === 1 ? 'resultado' : 'resultados' }}
                </span>
            </div>

            <!-- Botón de búsqueda avanzada (opcional) -->
            <button 
                v-if="manualStore.searchQuery"
                @click="manualStore.resetSearch()"
                class="flex-shrink-0 px-3 py-2 text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 transition"
            >
                ✕ Limpiar
            </button>
        </div>

        <!-- Indicador de búsqueda activa -->
        <div 
            v-if="manualStore.searchQuery"
            class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
        >
            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
            Buscando: "{{ manualStore.searchQuery }}"
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useManualStore } from '@/stores/manual';

// =============================================
// STORE
// =============================================
const manualStore = useManualStore();

// =============================================
// REFS
// =============================================
const searchInput = ref(null);

// =============================================
// COMPUTED
// =============================================

// Placeholder dinámico
const placeholder = computed(() => {
    const total = manualStore.totalSections;
    return `Buscar en ${total} secciones del manual... (Ctrl+K)`;
});

// =============================================
// FUNCIONES
// =============================================

// Enfocar el input de búsqueda
const focusSearch = () => {
    if (searchInput.value) {
        searchInput.value.focus();
        searchInput.value.select();
    }
};

// =============================================
// LIFECYCLE - Escuchar atajo de teclado global
// =============================================

const handleKeydown = (event) => {
    // Ctrl+K o Cmd+K
    if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
        event.preventDefault();
        focusSearch();
    }
    
    // Escape para limpiar búsqueda
    if (event.key === 'Escape' && manualStore.searchQuery) {
        manualStore.resetSearch();
        // Si el input está enfocado, quitamos el foco
        if (document.activeElement === searchInput.value) {
            searchInput.value.blur();
        }
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});

// =============================================
// EXPOSE - Para que el padre pueda enfocar
// =============================================
defineExpose({
    focusSearch,
    searchInput
});
</script>

<style scoped>
/* ============================================= */
/* ESTILOS DEL INPUT                             */
/* ============================================= */

input {
    transition: all 0.2s ease;
}

input:focus {
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
}

/* ============================================= */
/* ESTILOS DE KBD (Atajo de teclado)             */
/* ============================================= */

kbd {
    font-size: 10px;
    font-family: 'Courier New', monospace;
    padding: 1px 6px;
    border-radius: 4px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    color: #6b7280;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

:deep(.dark) kbd {
    background: #1f2937;
    border-color: #4b5563;
    color: #9ca3af;
}

/* ============================================= */
/* ANIMACIONES                                   */
/* ============================================= */

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.3;
    }
}

.animate-pulse {
    animation: pulse 1.5s ease-in-out infinite;
}

/* ============================================= */
/* RESPONSIVE                                    */
/* ============================================= */

@media (max-width: 640px) {
    .search-bar {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .search-input-wrapper {
        width: 100%;
    }
}
</style>