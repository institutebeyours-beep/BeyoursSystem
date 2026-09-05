<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            {{ isEditing ? '✏️ Editar Tipo de Componente' : '➕ Crear Tipo de Componente' }}
        </h1>

        <form @submit.prevent="handleSubmit">
            <div class="space-y-4">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre *
                    </label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Ej: Examen Oral"
                    />
                    <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Slug *
                    </label>
                    <input 
                        v-model="form.slug" 
                        type="text" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="ej: examen-oral"
                    />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Solo minúsculas, guiones y guiones bajos</p>
                    <p v-if="errors.slug" class="text-red-500 text-sm mt-1">{{ errors.slug }}</p>
                </div>

                <!-- ÍCONO - CON MÁS OPCIONES Y VISTA PREVIA -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Icono *
                    </label>
                    
                    <!-- Vista previa del icono seleccionado -->
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg flex items-center gap-4">
                        <span class="text-4xl">{{ form.icon || '📌' }}</span>
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Vista previa del icono
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                El icono seleccionado se mostrará en los componentes
                            </p>
                        </div>
                    </div>

                    <!-- Grid de iconos disponibles -->
                    <div class="grid grid-cols-8 gap-2 mb-2">
                        <button
                            v-for="icon in availableIcons"
                            :key="icon"
                            type="button"
                            @click="form.icon = icon"
                            class="text-2xl p-2 rounded-lg border-2 transition hover:bg-purple-50 dark:hover:bg-purple-900/20"
                            :class="form.icon === icon ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-transparent hover:border-gray-300'"
                            :title="icon"
                        >
                            {{ icon }}
                        </button>
                    </div>

                    <!-- Input para icono personalizado -->
                    <div class="flex gap-2 mt-2">
                        <input 
                            v-model="form.icon" 
                            type="text"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="O escribe tu propio icono (ej: 🎯)"
                            maxlength="10"
                        />
                        <span class="text-3xl flex items-center px-3">{{ form.icon || '📌' }}</span>
                    </div>
                    <p v-if="errors.icon" class="text-red-500 text-sm mt-1">{{ errors.icon }}</p>
                </div>

                <!-- COLOR - CON MÁS OPCIONES Y ETIQUETAS -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Color *
                    </label>

                    <!-- Vista previa del color seleccionado -->
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg flex items-center gap-4">
                        <div 
                            class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center text-white font-bold"
                            :style="{ backgroundColor: getColorHex(form.color) }"
                        >
                            {{ form.icon || 'A' }}
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Color: <span class="capitalize">{{ form.color }}</span>
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Código: <code>{{ getColorHex(form.color) }}</code>
                            </p>
                        </div>
                    </div>

                    <!-- Grid de colores disponibles -->
                    <div class="grid grid-cols-6 gap-3">
                        <button
                            v-for="color in availableColors"
                            :key="color.value"
                            type="button"
                            @click="form.color = color.value"
                            class="relative p-3 rounded-lg border-2 transition group"
                            :class="form.color === color.value ? 'border-purple-500 ring-2 ring-purple-500 ring-offset-2' : 'border-gray-200 dark:border-gray-700 hover:border-gray-400'"
                        >
                            <div class="flex items-center gap-2">
                                <div 
                                    class="w-6 h-6 rounded-full"
                                    :style="{ backgroundColor: color.hex }"
                                ></div>
                                <span class="text-xs capitalize hidden sm:inline">{{ color.label }}</span>
                            </div>
                            <!-- Checkmark cuando está seleccionado -->
                            <div 
                                v-if="form.color === color.value"
                                class="absolute -top-1 -right-1 w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs"
                            >
                                ✓
                            </div>
                        </button>
                    </div>
                    <p v-if="errors.color" class="text-red-500 text-sm mt-1">{{ errors.color }}</p>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Descripción
                    </label>
                    <textarea 
                        v-model="form.description" 
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Descripción del tipo de componente"
                    ></textarea>
                    <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</p>
                </div>

                <!-- Activo -->
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            v-model="form.is_active"
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300">Activo</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    type="button"
                    @click="$emit('cancel')"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    :disabled="loading"
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50 flex items-center gap-2"
                >
                    <span v-if="loading" class="animate-spin">⟳</span>
                    {{ isEditing ? '💾 Actualizar' : '💾 Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
            name: '',
            slug: '',
            icon: '📌',
            color: 'gray',
            description: '',
            is_active: true
        })
    },
    isEditing: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['submit', 'cancel']);

const form = ref({ ...props.initialData });

// Watch para actualizar form cuando cambia initialData
watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
}, { deep: true });

// =============================================
// ICONOS DISPONIBLES
// =============================================
const availableIcons = [
    '📝', '📊', '📋', '🚀', '📚', '🧪', '📌',
    '🎯', '🏆', '⭐', '🔥', '💡', '🎓', '📖',
    '✏️', '📈', '📉', '💯', '✅', '💪', '🤝',
    '🎨', '⚡', '🌟', '🎮', '🎵', '🎶', '🌈',
    '🎯', '🎪', '🎭', '🎨', '🎼', '🎹', '🎺',
    '🎻', '🥁', '🎸', '🎷', '🎤', '🎧', '🎼'
];

// =============================================
// MAPEO DE COLORES (Únicos y bien diferenciados)
// =============================================
const colorMap = {
    // Tonos neutros
    slate: '#64748B',
    gray: '#6B7280',
    zinc: '#71717A',
    neutral: '#737373',
    stone: '#78716C',
    
    // Tonos cálidos
    red: '#EF4444',
    orange: '#F97316',
    amber: '#F59E0B',
    yellow: '#EAB308',
    lime: '#84CC16',
    
    // Tonos verdes
    green: '#22C55E',
    emerald: '#10B981',
    teal: '#14B8A6',
    
    // Tonos azules
    cyan: '#06B6D4',
    sky: '#0EA5E9',
    blue: '#3B82F6',
    indigo: '#6366F1',
    
    // Tonos violetas
    violet: '#8B5CF6',
    purple: '#A855F7',
    fuchsia: '#D946EF',
    
    // Tonos rosas
    pink: '#EC4899',
    rose: '#F43F5E'
};

// =============================================
// COLORES DISPONIBLES (con etiquetas en español)
// =============================================
const availableColors = Object.entries(colorMap).map(([value, hex]) => {
    const labels = {
        slate: 'Pizarra',
        gray: 'Gris',
        zinc: 'Zinc',
        neutral: 'Neutral',
        stone: 'Piedra',
        red: 'Rojo',
        orange: 'Naranja',
        amber: 'Ámbar',
        yellow: 'Amarillo',
        lime: 'Lima',
        green: 'Verde',
        emerald: 'Esmeralda',
        teal: 'Verde Azulado',
        cyan: 'Cian',
        sky: 'Celeste',
        blue: 'Azul',
        indigo: 'Índigo',
        violet: 'Violeta',
        purple: 'Púrpura',
        fuchsia: 'Fucsia',
        pink: 'Rosa',
        rose: 'Rosa intenso'
    };
    return { value, hex, label: labels[value] || value };
});

// =============================================
// FUNCIÓN PARA OBTENER EL HEX DE UN COLOR
// =============================================
const getColorHex = (colorValue) => {
    return colorMap[colorValue] || '#6B7280';
};

// =============================================
// FUNCIÓN PARA GENERAR EL SLUG AUTOMÁTICAMENTE
// =============================================
const generateSlug = () => {
    if (form.value.name) {
        form.value.slug = form.value.name
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
};

// Watch para generar slug automáticamente cuando cambia el nombre
watch(() => form.value.name, (newName) => {
    if (newName && !form.value.slug) {
        generateSlug();
    }
});

// =============================================
// ENVIAR FORMULARIO
// =============================================
const handleSubmit = () => {
    // Generar slug si está vacío
    if (!form.value.slug && form.value.name) {
        generateSlug();
    }
    emit('submit', form.value);
};

// =============================================
// EXPOSER PARA DEBUGGING
// =============================================
defineExpose({
    form,
    availableIcons,
    availableColors,
    colorMap,
    getColorHex,
    generateSlug
});
</script>