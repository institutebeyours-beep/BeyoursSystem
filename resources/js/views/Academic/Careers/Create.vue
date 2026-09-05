<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">➕ Nueva Carrera</h1>
            <p class="text-emerald-100 mt-2">Crea una nueva carrera desde cero</p>
        </div>

        <Form 
            :isEditing="false"
            :loading="loading"
            :errors="errors"
            @submit="handleSubmit"
            @cancel="goBack"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import careersApi from '@/api/academic/careers';
import Form from './Form.vue';

const router = useRouter();
const loading = ref(false);
const errors = ref({});

const handleSubmit = async (data) => {
    try {
        loading.value = true;
        errors.value = {};
        
        await careersApi.create(data);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Carrera creada exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        router.push('/academic/careers');
    } catch (error) {
        console.error('Error creando carrera:', error);
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'No se pudo crear la carrera'
            });
        }
    } finally {
        loading.value = false;
    }
};

const goBack = () => {
    router.push('/academic/careers');
};
</script>