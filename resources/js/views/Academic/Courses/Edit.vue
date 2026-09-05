<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-yellow-600 to-amber-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">✏️ Editar Curso</h1>
            <p class="text-yellow-100 mt-2">Actualiza la información del curso</p>
        </div>

        <!-- FORMULARIO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <form @submit.prevent="updateCourse" class="space-y-6">
                <!-- Nombre y Código -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre del curso *
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
                            Código
                        </label>
                        <input 
                            v-model="form.code"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            disabled
                        />
                        <p class="text-xs text-gray-400 mt-1">El código no se puede modificar</p>
                    </div>
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
                    ></textarea>
                </div>

                <!-- Créditos y Duración -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Créditos
                        </label>
                        <input 
                            v-model.number="form.credits"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Duración (horas)
                        </label>
                        <input 
                            v-model.number="form.duration"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Cupo máximo
                        </label>
                        <input 
                            v-model.number="form.capacity"
                            type="number"
                            min="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        />
                    </div>
                </div>

                <!-- HORARIO AMIGABLE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Horario
                    </label>
                    
                    <!-- Días de la semana -->
                    <div class="grid grid-cols-7 gap-2 mb-3">
                        <button
                            v-for="day in daysOfWeek"
                            :key="day.value"
                            type="button"
                            @click="toggleDay(day.value)"
                            class="py-2 rounded-lg text-sm font-medium transition"
                            :class="isDaySelected(day.value) 
                                ? 'bg-indigo-600 text-white hover:bg-indigo-700' 
                                : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                        >
                            {{ day.label }}
                        </button>
                    </div>

                    <!-- Hora de inicio y fin -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Hora de inicio
                            </label>
                            <input 
                                v-model="form.startTime"
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Hora de fin
                            </label>
                            <input 
                                v-model="form.endTime"
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            />
                        </div>
                    </div>

                    <!-- Resumen del horario -->
                    <div v-if="selectedDays.length > 0 && form.startTime && form.endTime" class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            📅 <span class="font-medium">{{ selectedDays.join(', ') }}</span>
                            ⏰ <span class="font-medium">{{ form.startTime }} - {{ form.endTime }}</span>
                        </p>
                    </div>
                    <p v-else class="text-xs text-gray-400 mt-2">
                        Selecciona los días y el horario del curso
                    </p>
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
                        <option value="completed">📌 Completado</option>
                    </select>
                </div>

                <!-- BOTONES -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <router-link 
                        to="/academic/courses"
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
                        {{ loading ? 'Guardando...' : '💾 Actualizar Curso' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const loading = ref(false);

// =============================================
// DÍAS DE LA SEMANA
// =============================================
const daysOfWeek = [
    { value: 'Lunes', label: 'Lun' },
    { value: 'Martes', label: 'Mar' },
    { value: 'Miércoles', label: 'Mié' },
    { value: 'Jueves', label: 'Jue' },
    { value: 'Viernes', label: 'Vie' },
    { value: 'Sábado', label: 'Sáb' },
    { value: 'Domingo', label: 'Dom' },
];

// =============================================
// FORM
// =============================================
const form = ref({
    name: '',
    code: '',
    description: '',
    credits: 0,
    duration: 0,
    capacity: 20,
    selectedDays: [],
    startTime: '08:00',
    endTime: '10:00',
    status: 'active',
});

// =============================================
// COMPUTED
// =============================================
const selectedDays = computed({
    get: () => form.value.selectedDays || [],
    set: (val) => { form.value.selectedDays = val; }
});

// =============================================
// FUNCIONES DEL HORARIO
// =============================================
const toggleDay = (day) => {
    const index = form.value.selectedDays.indexOf(day);
    if (index > -1) {
        form.value.selectedDays.splice(index, 1);
    } else {
        form.value.selectedDays.push(day);
    }
};

const isDaySelected = (day) => {
    return form.value.selectedDays.includes(day);
};

// =============================================
// CARGAR DATOS DEL CURSO
// =============================================
const loadCourse = async () => {
    try {
        const response = await axios.get(`/api/academic/courses/${route.params.id}`);
        const course = response.data.course;
        
        // Extraer días y horario del schedule
        let selectedDays = [];
        let startTime = '08:00';
        let endTime = '10:00';
        
        if (course.schedule) {
            selectedDays = course.schedule.days || [];
            startTime = course.schedule.start_time || '08:00';
            endTime = course.schedule.end_time || '10:00';
        }
        
        form.value = {
            name: course.name,
            code: course.code,
            description: course.description || '',
            credits: course.credits,
            duration: course.duration || 0,
            capacity: course.capacity,
            selectedDays: selectedDays,
            startTime: startTime,
            endTime: endTime,
            status: course.status,
        };
    } catch (error) {
        console.error('Error cargando curso:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la información del curso',
        });
    }
};

// =============================================
// ACTUALIZAR CURSO
// =============================================
const updateCourse = async () => {
    // Validar horario
    if (form.value.selectedDays.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Horario incompleto',
            text: 'Selecciona al menos un día para el curso',
        });
        return;
    }

    if (!form.value.startTime || !form.value.endTime) {
        Swal.fire({
            icon: 'warning',
            title: 'Horario incompleto',
            text: 'Ingresa la hora de inicio y fin del curso',
        });
        return;
    }

    loading.value = true;
    try {
        const schedule = {
            days: form.value.selectedDays,
            start_time: form.value.startTime,
            end_time: form.value.endTime,
        };

        const data = {
            name: form.value.name,
            description: form.value.description,
            credits: form.value.credits,
            duration: form.value.duration,
            capacity: form.value.capacity,
            schedule: schedule,
            status: form.value.status,
        };

        // ✅ CORREGIDO: CON /api
        await axios.put(`/api/academic/courses/${route.params.id}`, data);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Curso actualizado exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        router.push('/academic/courses');
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo actualizar el curso',
        });
    } finally {
        loading.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadCourse();
});
</script>