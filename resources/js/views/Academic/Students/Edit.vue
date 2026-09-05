<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-yellow-600 to-amber-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">✏️ Editar Estudiante</h1>
            <p class="text-yellow-100 mt-2">Actualiza la información del estudiante</p>
        </div>

        <!-- FORMULARIO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <form @submit.prevent="updateStudent" class="space-y-6">
                <!-- Datos personales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre completo *
                        </label>
                        <input 
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email *
                        </label>
                        <input 
                            v-model="form.email"
                            type="email"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            required
                        />
                    </div>
                </div>

                <!-- Teléfono y Fecha de Nacimiento -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Teléfono
                        </label>
                        <input 
                            v-model="form.phone"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Fecha de nacimiento
                        </label>
                        <input 
                            v-model="form.birth_date"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Dirección -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Dirección
                    </label>
                    <input 
                        v-model="form.address"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    />
                </div>

                <!-- Datos del apoderado -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre del apoderado
                        </label>
                        <input 
                            v-model="form.guardian_name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Teléfono del apoderado
                        </label>
                        <input 
                            v-model="form.guardian_phone"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Estado
                    </label>
                    <select 
                        v-model="form.status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <option value="active">✅ Activo</option>
                        <option value="inactive">⬜ Inactivo</option>
                        <option value="graduated">🎓 Graduado</option>
                        <option value="suspended">⛔ Suspendido</option>
                    </select>
                </div>

                <!-- BOTONES -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <router-link 
                        to="/academic/students"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                    >
                        Cancelar
                    </router-link>
                    <button 
                        type="submit"
                        :disabled="loading"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        {{ loading ? 'Guardando...' : '💾 Actualizar Estudiante' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const loading = ref(false);

// =============================================
// FORM
// =============================================
const form = ref({
    name: '',
    email: '',
    phone: '',
    address: '',
    birth_date: '',
    guardian_name: '',
    guardian_phone: '',
    status: 'active',
});

// =============================================
// FUNCIONES
// =============================================
const loadStudent = async () => {
    try {
        const response = await axios.get(`/api/academic/students/${route.params.id}`);
        const student = response.data.student;
        const user = student.user;
        
        console.log('📋 Datos recibidos:', student);
        console.log('📋 Fecha de nacimiento (raw):', student.birth_date);
        
        // ✅ FORMATER LA FECHA PARA EL INPUT DATE
        let birthDate = '';
        if (student.birth_date) {
            // Extraer solo la parte de la fecha (YYYY-MM-DD)
            const dateObj = new Date(student.birth_date);
            birthDate = dateObj.toISOString().split('T')[0];
        }
        
        console.log('📋 Fecha formateada para input:', birthDate);
        
        // ✅ Asignar TODOS los valores al formulario
        form.value = {
            name: user?.name || '',
            email: user?.email || '',
            phone: student.phone || '',
            address: student.address || '',
            birth_date: birthDate, // ✅ Fecha formateada
            guardian_name: student.guardian_name || '',
            guardian_phone: student.guardian_phone || '',
            status: student.status || 'active',
        };
        
        console.log('✅ Formulario cargado:', form.value);
        
    } catch (error) {
        console.error('❌ Error cargando estudiante:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la información del estudiante',
        });
    }
};

const updateStudent = async () => {
    loading.value = true;
    try {
        await axios.put(`/api/academic/students/${route.params.id}`, form.value);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Estudiante actualizado exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        router.push('/academic/students');
    } catch (error) {
        console.error('❌ Error actualizando:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo actualizar el estudiante',
        });
    } finally {
        loading.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadStudent();
});
</script>