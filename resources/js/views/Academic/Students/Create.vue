<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">➕ Crear Nuevo Estudiante</h1>
            <p class="text-blue-100 mt-2">Registra un nuevo estudiante en el sistema</p>
        </div>

        <!-- FORMULARIO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <form @submit.prevent="saveStudent" class="space-y-6">
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
                            placeholder="Ej: Juan Pérez"
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
                            placeholder="ejemplo@email.com"
                            required
                        />
                    </div>
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contraseña *
                    </label>
                    <input 
                        v-model="form.password"
                        type="password"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Mínimo 8 caracteres"
                        required
                    />
                    <p class="text-xs text-gray-400 mt-1">Mínimo 8 caracteres</p>
                </div>

                <!-- Teléfono y Dirección -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Teléfono
                        </label>
                        <input 
                            v-model="form.phone"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Ej: 555-1234"
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
                        placeholder="Dirección completa"
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
                            placeholder="Nombre del apoderado"
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
                            placeholder="Teléfono del apoderado"
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
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        {{ loading ? 'Guardando...' : '💾 Guardar Estudiante' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);

// =============================================
// FORM
// =============================================
const form = ref({
    name: '',
    email: '',
    password: '',
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
const saveStudent = async () => {
    // ✅ Validar contraseña
    if (form.value.password.length < 8) {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña muy corta',
            text: 'La contraseña debe tener al menos 8 caracteres',
        });
        return;
    }

    loading.value = true;
    try {
        // ✅ CORREGIDO: Agregar /api/
        await axios.post('/api/academic/students', form.value);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Estudiante creado exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        // ✅ Redirigir sin /api
        router.push('/academic/students');
    } catch (error) {
        console.error('❌ Error creando estudiante:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo crear el estudiante',
        });
    } finally {
        loading.value = false;
    }
};
</script>