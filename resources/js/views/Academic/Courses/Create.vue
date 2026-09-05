<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">➕ Crear Nuevo Curso</h1>
            <p class="text-emerald-100 mt-2">Completa los datos para crear un nuevo curso académico</p>
        </div>

        <!-- FORMULARIO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <form @submit.prevent="saveCourse" class="space-y-6">
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
                            placeholder="Ej: Programación Web"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Código (opcional)
                        </label>
                        <input 
                            v-model="form.code"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Ej: PROG-001"
                        />
                        <p class="text-xs text-gray-400 mt-1">Si no se ingresa, se generará automáticamente</p>
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
                        placeholder="Breve descripción del curso..."
                    ></textarea>
                </div>

                <!-- ========================================== -->
                <!-- SECCIÓN: CRÉDITOS TOTALES                  -->
                <!-- ========================================== -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        🎯 Créditos del Curso
                    </h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Créditos totales del curso
                        </label>
                        <input 
                            v-model.number="form.totalCredits"
                            type="number"
                            min="0"
                            max="300"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Ej: 60"
                        />
                        <div class="flex items-start gap-1 mt-1">
                            <span class="text-xs text-gray-400">💡</span>
                            <p class="text-xs text-gray-400">
                                Define el total de créditos que el estudiante debe completar en este curso.
                                Luego podrás asignar asignaturas que consumirán estos créditos.
                            </p>
                        </div>
                    </div>

                    <!-- Info de asignaturas (si hay) -->
                    <div v-if="form.totalCredits > 0" class="mt-3 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            📊 Con <strong>{{ form.totalCredits }}</strong> créditos totales, 
                            puedes distribuir en varias asignaturas. 
                            <span class="text-xs block mt-1">
                                💡 Ejemplo: 6 asignaturas de 10 créditos cada una
                            </span>
                        </p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SECCIÓN: HORARIO                           -->
                <!-- ========================================== -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        📅 Horario de clases
                    </h3>
                    
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

                    <!-- Horas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Hora de inicio
                            </label>
                            <input 
                                v-model="form.startTime"
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                @change="calculateLoad"
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
                                @change="calculateLoad"
                            />
                        </div>
                    </div>

                    <!-- Resumen del horario -->
                    <div v-if="selectedDays.length > 0 && form.startTime && form.endTime" class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            📅 <span class="font-medium">{{ selectedDays.join(', ') }}</span>
                            ⏰ <span class="font-medium">{{ form.startTime }} - {{ form.endTime }}</span>
                            <span class="ml-2 text-xs text-gray-400">
                                ({{ hoursPerDay }} horas/día)
                            </span>
                        </p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SECCIÓN: TIPO DE CURSO                     -->
                <!-- ========================================== -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        🧪 Tipo de curso
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <button
                            v-for="type in courseTypes"
                            :key="type.value"
                            type="button"
                            @click="selectCourseType(type.value)"
                            class="p-3 rounded-lg border-2 transition text-left"
                            :class="form.courseType === type.value 
                                ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-xl">{{ type.icon }}</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ type.label }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ type.description }}</p>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-400">
                                <span class="inline-block mr-3">📚 Estudio: {{ type.studyRatio }}h</span>
                                <span v-if="type.labRatio > 0">🧪 Lab: {{ type.labRatio }}h</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SECCIÓN: RESULTADOS                        -->
                <!-- ========================================== -->
                <div v-if="showResults" class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        📊 Carga académica calculada
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Clase / semana</p>
                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ calculatedLoad.classHours }}h
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Estudio / semana</p>
                            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                                {{ calculatedLoad.studyHours }}h
                            </p>
                        </div>
                        <div v-if="calculatedLoad.labHours > 0" class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Laboratorio / semana</p>
                            <p class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                {{ calculatedLoad.labHours }}h
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total / semana</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ calculatedLoad.totalHours }}h
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    📚 <span class="font-medium">{{ calculatedLoad.credits }}</span> créditos
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ calculatedLoad.totalHours }}h totales · {{ form.weeks }} semanas
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">
                                    Proporción estudio: 1:{{ calculatedLoad.studyRatio }}
                                </p>
                                <p v-if="calculatedLoad.labRatio > 0" class="text-xs text-gray-400">
                                    Proporción laboratorio: 1:{{ calculatedLoad.labRatio }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Capacidad y Estado -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Cupo máximo
                        </label>
                        <input 
                            v-model.number="form.capacity"
                            type="number"
                            min="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="20"
                        />
                    </div>
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
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        {{ loading ? 'Guardando...' : '💾 Guardar Curso' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();
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
// TIPOS DE CURSO
// =============================================
const courseTypes = [
    {
        value: 'theoretical',
        label: '📖 Teórico',
        icon: '📖',
        studyRatio: 2.5,
        labRatio: 0,
        description: 'Mayor carga de estudio, sin práctica'
    },
    {
        value: 'theoretical_practical',
        label: '📖🔬 Teórico-Práctico',
        icon: '📖🔬',
        studyRatio: 2,
        labRatio: 0.5,
        description: 'Balance entre teoría y práctica'
    },
    {
        value: 'practical',
        label: '🔬 Práctico (Laboratorio)',
        icon: '🔬',
        studyRatio: 1,
        labRatio: 1,
        description: 'Enfoque práctico con laboratorio'
    },
    {
        value: 'specialized_lab',
        label: '🧪 Laboratorio Especializado',
        icon: '🧪',
        studyRatio: 1.5,
        labRatio: 2,
        description: 'Alta carga de laboratorio'
    }
];

// =============================================
// FORM
// =============================================
const form = ref({
    name: '',
    code: '',
    description: '',
    courseType: 'theoretical_practical',
    totalCredits: 0,  // ✅ NUEVO
    selectedDays: [],
    startTime: '08:00',
    endTime: '10:30',
    weeks: 16,
    capacity: 20,
    status: 'active',
});

// =============================================
// COMPUTED
// =============================================
const selectedDays = computed({
    get: () => form.value.selectedDays || [],
    set: (val) => { form.value.selectedDays = val; }
});

const hoursPerDay = computed(() => {
    if (!form.value.startTime || !form.value.endTime) return 0;
    const [startH, startM] = form.value.startTime.split(':').map(Number);
    const [endH, endM] = form.value.endTime.split(':').map(Number);
    let hours = endH - startH;
    let minutes = endM - startM;
    if (minutes < 0) {
        hours--;
        minutes += 60;
    }
    return hours + (minutes / 60);
});

const classHoursPerWeek = computed(() => {
    return selectedDays.value.length * hoursPerDay.value;
});

const showResults = computed(() => {
    return selectedDays.value.length > 0 && 
           form.value.startTime && 
           form.value.endTime && 
           form.value.courseType &&
           classHoursPerWeek.value > 0;
});

// =============================================
// CÁLCULO DE CARGAS
// =============================================
const calculatedLoad = computed(() => {
    if (!showResults.value) {
        return {
            classHours: 0,
            studyHours: 0,
            labHours: 0,
            totalHours: 0,
            credits: 0,
            totalHoursAll: 0,
            studyRatio: 0,
            labRatio: 0
        };
    }

    const type = courseTypes.find(t => t.value === form.value.courseType);
    if (!type) return null;

    const classH = classHoursPerWeek.value;
    const studyH = classH * type.studyRatio;
    const labH = classH * type.labRatio;
    const totalH = classH + studyH + labH;
    const totalHoursAll = totalH * form.value.weeks;
    const credits = Math.round(totalHoursAll / 45);

    return {
        classHours: classH.toFixed(1),
        studyHours: studyH.toFixed(1),
        labHours: labH.toFixed(1),
        totalHours: totalH.toFixed(1),
        credits: credits,
        totalHoursAll: totalHoursAll.toFixed(0),
        studyRatio: type.studyRatio,
        labRatio: type.labRatio
    };
});

// =============================================
// FUNCIONES
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

const selectCourseType = (type) => {
    form.value.courseType = type;
};

const calculateLoad = () => {
    // Los computed ya actualizan automáticamente
};

// =============================================
// FUNCIÓN PRINCIPAL
// =============================================
const saveCourse = async () => {
    // ✅ Validar horario
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
            code: form.value.code || null,
            description: form.value.description,
            course_type: form.value.courseType,
            total_credits: form.value.totalCredits,  // ✅ NUEVO
            class_hours_per_week: parseFloat(calculatedLoad.value.classHours),
            study_hours_per_week: parseFloat(calculatedLoad.value.studyHours),
            lab_hours_per_week: parseFloat(calculatedLoad.value.labHours) || 0,
            total_hours_per_week: parseFloat(calculatedLoad.value.totalHours),
            total_weeks: form.value.weeks,
            total_hours: parseFloat(calculatedLoad.value.totalHoursAll),
            study_ratio: parseFloat(calculatedLoad.value.studyRatio),
            lab_ratio: parseFloat(calculatedLoad.value.labRatio) || 0,
            credits: calculatedLoad.value.credits,
            duration: form.value.weeks,
            schedule: schedule,
            capacity: form.value.capacity,
            status: form.value.status,
        };

        await axios.post('/api/academic/courses', data);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Curso creado exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        router.push('/academic/courses');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo crear el curso',
        });
    } finally {
        loading.value = false;
    }
};
</script>