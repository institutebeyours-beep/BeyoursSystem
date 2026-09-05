<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black">✏️ Editar Carrera</h1>
                    <p class="text-emerald-100 mt-2">
                        Actualiza la información de la carrera
                        <span v-if="!loading && formData.name" class="block text-sm text-emerald-200 mt-1">
                            📌 {{ formData.name }} ({{ formData.code }})
                        </span>
                    </p>
                </div>
                <router-link 
                    to="/academic/careers"
                    class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                >
                    📋 Volver
                </router-link>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-12">
            <span class="animate-spin inline-block text-4xl">⟳</span>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando carrera...</p>
        </div>

        <!-- Error de carga -->
        <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-6 text-center">
            <span class="text-4xl block mb-4">❌</span>
            <h3 class="text-lg font-semibold text-red-700 dark:text-red-400 mb-2">Error al cargar la carrera</h3>
            <p class="text-red-600 dark:text-red-300">{{ error }}</p>
            <button 
                @click="goBack"
                class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
            >
                Volver a carreras
            </button>
        </div>

        <!-- Formulario -->
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
import careersApi from '@/api/academic/careers';
import Form from './Form.vue';

const router = useRouter();
const route = useRoute();

// =============================================
// STATE
// =============================================
const loading = ref(true);
const submitting = ref(false);
const error = ref(null);
const formData = ref({
    education_type_id: '',
    name: '',
    code: '',
    description: '',
    total_credits: 0,
    theoretical_hours: 0,
    practical_hours: 0,
    duration_years: 0,
    duration_semesters: 0,
    is_active: true,
});
const errors = ref({});

// =============================================
// FUNCIONES
// =============================================

const loadCareer = async () => {
    try {
        loading.value = true;
        error.value = null;
        
        const response = await careersApi.getById(route.params.id);
        formData.value = response.data.career;
        
        console.log('✅ Carrera cargada:', formData.value.name);
    } catch (err) {
        console.error('Error cargando carrera:', err);
        error.value = err.response?.data?.message || 'No se pudo cargar la carrera';
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.value
        });
    } finally {
        loading.value = false;
    }
};

const handleSubmit = async (data) => {
    try {
        submitting.value = true;
        errors.value = {};
        
        await careersApi.update(route.params.id, data);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Carrera actualizada exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        // Redirigir a la vista de detalles
        router.push(`/academic/careers/${route.params.id}`);
    } catch (err) {
        console.error('Error actualizando carrera:', err);
        
        if (err.response?.data?.errors) {
            errors.value = err.response.data.errors;
        } else {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: err.response?.data?.message || 'No se pudo actualizar la carrera'
            });
        }
    } finally {
        submitting.value = false;
    }
};

const goBack = () => {
    router.push('/academic/careers');
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadCareer();
});
</script>