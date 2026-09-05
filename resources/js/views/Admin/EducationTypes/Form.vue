<template>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
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
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Ej: Bachillerato"
                            required
                        />
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                    </div>

                    <!-- Código -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Código *
                        </label>
                        <input 
                            v-model="form.code"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Ej: BACH"
                            required
                        />
                        <p class="text-xs text-gray-400 mt-1">Código único para identificar el tipo de enseñanza</p>
                        <p v-if="errors.code" class="text-red-500 text-sm mt-1">{{ errors.code }}</p>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Descripción
                        </label>
                        <textarea 
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Descripción del tipo de enseñanza..."
                        ></textarea>
                    </div>

                    <!-- Orden -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Orden de visualización
                        </label>
                        <input 
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="0"
                        />
                        <p class="text-xs text-gray-400 mt-1">Los tipos se mostrarán en orden ascendente</p>
                    </div>

                    <!-- Activo -->
                    <div>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                v-model="form.is_active"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Activo</span>
                        </label>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
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
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        {{ isEditing ? '💾 Actualizar' : '💾 Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
            name: '',
            code: '',
            description: '',
            sort_order: 0,
            is_active: true,
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

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
}, { deep: true });

const handleSubmit = () => {
    emit('submit', form.value);
};
</script>