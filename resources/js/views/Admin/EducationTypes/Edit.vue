<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">✏️ Editar Tipo de Enseñanza</h1>
            <p class="text-indigo-100 mt-2">Actualiza la información del tipo de enseñanza</p>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-12">
            <span class="animate-spin inline-block text-4xl">⟳</span>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando tipo...</p>
        </div>

        <Form 
            v-else
            :initialData="formData"
            :isEditing="true"
            :loading="submitting"
            :errors="errors"
            @submit="handleSubmit"
            @cancel="goBack"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Swal from 'sweetalert2';
import educationTypesApi from '@/api/admin/educationTypes';
import Form from './Form.vue';

const router = useRouter();
const route = useRoute();

const loading = ref(true);
const submitting = ref(false);
const formData = ref({
    name: '',
    code: '',
    description: '',
    sort_order: 0,
    is_active: true,
});
const errors = ref({});

const loadType = async () => {
    try {
        loading.value = true;
        const response = await educationTypesApi.getById(route.params.id);
        formData.value = response.data.type;
    } catch (error) {
        console.error('Error cargando tipo:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar el tipo'
        }).then(() => {
            router.push('/admin/education-types');
        });
    } finally {
        loading.value = false;
    }
};

const handleSubmit = async (data) => {
    try {
        submitting.value = true;
        errors.value = {};
        
        await educationTypesApi.update(route.params.id, data);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Tipo actualizado exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        router.push('/admin/education-types');
    } catch (error) {
        console.error('Error actualizando tipo:', error);
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'No se pudo actualizar el tipo'
            });
        }
    } finally {
        submitting.value = false;
    }
};

const goBack = () => {
    router.push('/admin/education-types');
};

onMounted(() => {
    loadType();
});
</script>