<template>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Icono</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Slug</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Color</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Orden</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Componentes</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="type in types" :key="type.id" 
                    class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                    <td class="px-4 py-3 text-2xl">{{ type.icon }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ type.name }}</td>
                    <td class="px-4 py-3">
                        <code class="bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded text-sm">{{ type.slug }}</code>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs text-white" 
                              :style="{ backgroundColor: getColor(type.color) }">
                            {{ type.color }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium"
                              :class="type.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'">
                            {{ type.is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ type.sort_order }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                            {{ type.components_count || 0 }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <button @click="$emit('edit', type.id)" 
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                ✏️
                            </button>
                            <button @click="$emit('delete', type)" 
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="types.length === 0">
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No hay tipos de componente creados
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

defineProps({
    types: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['edit', 'delete']);

const getColor = (color) => {
    const colors = {
        gray: '#6B7280',
        blue: '#3B82F6',
        purple: '#8B5CF6',
        green: '#22C55E',
        orange: '#F97316',
        yellow: '#EAB308',
        red: '#EF4444',
        pink: '#EC4899',
        indigo: '#6366F1'
    };
    return colors[color] || colors.gray;
};
</script>