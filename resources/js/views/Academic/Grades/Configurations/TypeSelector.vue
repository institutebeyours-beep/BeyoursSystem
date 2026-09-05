<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Tipo *
        </label>
        <select 
            :value="modelValue"
            @change="$emit('update:modelValue', parseInt($event.target.value))"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            :disabled="loading"
        >
            <option value="">Selecciona un tipo</option>
            <option 
                v-for="type in types" 
                :key="type.id" 
                :value="type.id"
            >
                {{ type.icon }} {{ type.name }}
            </option>
        </select>
        
        <!-- Loading -->
        <div v-if="loading" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            ⏳ Cargando tipos...
        </div>
        
        <!-- Mostrar información del tipo seleccionado -->
        <div v-if="selectedType" class="mt-2 p-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg flex items-center gap-2">
            <span class="text-lg">{{ selectedType.icon }}</span>
            <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ selectedType.name }}
                </span>
                <p v-if="selectedType.description" class="text-xs text-gray-500 dark:text-gray-400">
                    {{ selectedType.description }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

// =============================================
// PROPS
// =============================================
const props = defineProps({
    modelValue: {
        type: [Number, String],
        default: null
    }
});

const emit = defineEmits(['update:modelValue']);

// =============================================
// STATE
// =============================================
const types = ref([]);
const loading = ref(false);

// =============================================
// COMPUTED
// =============================================
const selectedType = computed(() => {
    return types.value.find(t => t.id === parseInt(props.modelValue));
});

// =============================================
// FUNCTIONS
// =============================================
const loadTypes = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/academic/component-types');
        types.value = response.data.types || [];
        
        // Si no hay tipo seleccionado y hay tipos disponibles, seleccionar el primero
        if (!props.modelValue && types.value.length > 0) {
            const defaultType = types.value.find(t => t.is_default) || types.value[0];
            if (defaultType) {
                emit('update:modelValue', defaultType.id);
            }
        }
    } catch (error) {
        console.error('Error cargando tipos:', error);
        // Fallback en caso de error
        types.value = [
            { id: 1, name: 'Parcial', icon: '📝', slug: 'partial' },
            { id: 2, name: 'Examen Final', icon: '📊', slug: 'final' },
            { id: 3, name: 'Tarea', icon: '📚', slug: 'homework' },
        ];
    } finally {
        loading.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadTypes();
});

// =============================================
// EXPOSE
// =============================================
defineExpose({
    loadTypes,
    types
});
</script>