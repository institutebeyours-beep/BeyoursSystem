<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 sticky top-24">
        <!-- Título -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                📑 Índice
            </h3>
            <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 px-2 py-1 rounded-full">
                {{ totalSections }}
            </span>
        </div>
        
        <!-- Lista de categorías -->
        <div class="space-y-1 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
            <div 
                v-for="category in categories" 
                :key="category.key"
                class="mb-3"
            >
                <!-- Título de categoría -->
                <div class="text-xs font-semibold text-purple-600 dark:text-purple-400 px-2 py-1 mb-1">
                    {{ category.title }}
                </div>
                
                <!-- Secciones de la categoría -->
                <div class="space-y-0.5">
                    <button
                        v-for="section in category.sections"
                        :key="section.id"
                        @click="manualStore.setCurrentSection(section.id)"
                        class="w-full text-left px-3 py-2 rounded-lg transition flex items-center gap-2 text-sm group"
                        :class="[
                            isActive(section.id) 
                                ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' 
                                : 'hover:bg-gray-100 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300'
                        ]"
                    >
                        <!-- Icono -->
                        <span class="text-base flex-shrink-0">{{ section.icon }}</span>
                        
                        <!-- Título -->
                        <span class="flex-1 truncate">{{ section.title }}</span>
                        
                        <!-- Badge de rol (si es exclusivo) -->
                        <span 
                            v-if="!section.roles.includes('all')"
                            class="text-[8px] px-1.5 py-0.5 rounded-full bg-purple-200 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 flex-shrink-0"
                        >
                            {{ getRoleBadge(section.roles) }}
                        </span>
                        
                        <!-- Indicador de sección activa -->
                        <span 
                            v-if="isActive(section.id)"
                            class="w-1.5 h-1.5 rounded-full bg-purple-600 flex-shrink-0"
                        ></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>v1.0.0</span>
                <div class="flex items-center gap-2">
                    <span>{{ totalSections }} secciones</span>
                    <span class="w-px h-3 bg-gray-300 dark:bg-gray-600"></span>
                    <span>{{ totalCategories }} categorías</span>
                </div>
            </div>
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

// Categorías organizadas
const categories = computed(() => {
    const result = [];
    const content = manualStore.filteredContent;
    
    Object.keys(content).forEach(key => {
        if (content[key].sections && content[key].sections.length > 0) {
            result.push({
                key: key,
                title: content[key].title || key,
                sections: content[key].sections
            });
        }
    });
    
    return result;
});

// Total de secciones
const totalSections = computed(() => {
    return manualStore.totalSections;
});

// Total de categorías
const totalCategories = computed(() => {
    return categories.value.length;
});

// =============================================
// FUNCIONES
// =============================================

// Verificar si una sección está activa
const isActive = (sectionId) => {
    return manualStore.currentSection === sectionId;
};

// Obtener badge para roles
const getRoleBadge = (roles) => {
    const roleMap = {
        'super-admin': '👑',
        'admin': '🔧',
        'academico': '📚'
    };
    
    const filtered = roles.filter(r => r !== 'all');
    const badges = filtered.map(r => roleMap[r] || r).join('');
    return badges || '🔒';
};
</script>

<style scoped>
/* Estilos para el scrollbar personalizado */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c4b5fd;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a78bfa;
}

/* Modo oscuro */
:deep(.dark) .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4c1d95;
}

:deep(.dark) .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #6d28d9;
}

/* Transiciones */
button {
    transition: all 0.15s ease;
}
</style>