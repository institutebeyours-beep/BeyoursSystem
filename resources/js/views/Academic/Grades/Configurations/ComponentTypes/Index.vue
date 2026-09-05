<template>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black">📋 Tipos de Componente</h1>
                    <p class="text-purple-100 mt-2">Gestiona los tipos de componentes para calificaciones</p>
                </div>
                <button 
                    @click="goToCreate"
                    class="px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium"
                >
                    ➕ Nuevo Tipo
                </button>
            </div>
        </div>

        <!-- Contenido -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Lista de Tipos ({{ types.length }})
                </h2>
                <button 
                    @click="loadTypes"
                    class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400"
                >
                    🔄 Actualizar
                </button>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center py-8">
                <span class="animate-spin inline-block text-2xl">⟳</span>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando tipos...</p>
            </div>

            <!-- Tabla -->
            <TypeTable 
                v-else
                :types="types" 
                @edit="goToEdit"
                @delete="confirmDelete"
            />
        </div>

        <!-- Modal de confirmación -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    🗑️ Eliminar Tipo
                </h3>
                <p class="text-gray-700 dark:text-gray-300">
                    ¿Estás seguro de eliminar el tipo <strong>"{{ deleteItem?.name }}"</strong>?
                </p>
                <p v-if="deleteItem?.components_count > 0" class="text-red-500 text-sm mt-2">
                    ⚠️ Este tipo tiene {{ deleteItem.components_count }} componentes asociados. No se puede eliminar.
                </p>
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        @click="closeDeleteModal"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="deleteType"
                        :disabled="deleteItem?.components_count > 0 || deleting"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50"
                    >
                        <span v-if="deleting" class="animate-spin inline-block mr-2">⟳</span>
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import componentTypesApi from '@/api/academic/componentTypes';
import TypeTable from '../components/TypeTable.vue';

const router = useRouter();
const types = ref([]);
const loading = ref(false);
const deleting = ref(false);
const showDeleteModal = ref(false);
const deleteItem = ref(null);

const loadTypes = async () => {
    try {
        loading.value = true;
        const response = await componentTypesApi.getAll();
        types.value = response.data.types || [];
    } catch (error) {
        console.error('Error cargando tipos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los tipos de componente'
        });
    } finally {
        loading.value = false;
    }
};

const goToCreate = () => {
    router.push({ name: 'academic.component-types.create' });
};

const goToEdit = (id) => {
    router.push({ name: 'academic.component-types.edit', params: { id } });
};

const confirmDelete = (type) => {
    deleteItem.value = type;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deleteItem.value = null;
};

const deleteType = async () => {
    if (!deleteItem.value) return;
    
    try {
        deleting.value = true;
        await componentTypesApi.delete(deleteItem.value.id);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Tipo eliminado',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        closeDeleteModal();
        await loadTypes();
    } catch (error) {
        console.error('Error eliminando tipo:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'No se pudo eliminar el tipo'
        });
    } finally {
        deleting.value = false;
    }
};

onMounted(() => {
    loadTypes();
});
</script>