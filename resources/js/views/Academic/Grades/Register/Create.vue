<template>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">📝 Registrar Calificaciones</h1>
            <p class="text-emerald-100 mt-2">Ingresa las calificaciones de los estudiantes</p>
        </div>

        <!-- FORMULARIO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <form @submit.prevent="saveGrades" class="space-y-6">
                <!-- Selección de Curso -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Curso *
                        </label>
                        <select 
                            v-model="form.course_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            @change="loadStudents"
                            required
                        >
                            <option value="">Seleccionar curso</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">
                                {{ course.name }} ({{ course.code }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Parcial *
                        </label>
                        <select 
                            v-model="form.partial"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            required
                        >
                            <option value="">Seleccionar parcial</option>
                            <option value="1">Parcial 1</option>
                            <option value="2">Parcial 2</option>
                            <option value="3">Parcial 3</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla de estudiantes -->
                <div v-if="students.length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Estudiante</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Código</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nota</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="(student, index) in students" :key="student.id">
                                <td class="px-4 py-2 text-gray-900 dark:text-white">{{ student.full_name }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ student.code }}</td>
                                <td class="px-4 py-2">
                                    <input 
                                        v-model="student.grade"
                                        type="number"
                                        min="0"
                                        max="100"
                                        class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="0-100"
                                    />
                                </td>
                                <td class="px-4 py-2">
                                    <input 
                                        v-model="student.observations"
                                        type="text"
                                        class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="Observaciones..."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else-if="form.course_id && !loading" class="text-center py-8 text-gray-400">
                    <p>No hay estudiantes inscritos en este curso</p>
                </div>

                <!-- BOTONES -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <router-link 
                        to="/academic/grades"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                    >
                        Cancelar
                    </router-link>
                    <button 
                        type="submit"
                        :disabled="loading || students.length === 0"
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin">⟳</span>
                        💾 Guardar Calificaciones
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
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);
const courses = ref([]);
const students = ref([]);
const form = ref({
    course_id: '',
    partial: '',
});

const loadCourses = async () => {
    try {
        const response = await axios.get('/academic/courses?per_page=100');
        courses.value = response.data.data || [];
    } catch (error) {
        console.error('Error:', error);
    }
};

const loadStudents = async () => {
    if (!form.value.course_id) {
        students.value = [];
        return;
    }
    
    loading.value = true;
    try {
        const response = await axios.get(`/academic/grades/course/${form.value.course_id}`);
        students.value = response.data.grades || [];
    } catch (error) {
        console.error('Error:', error);
    } finally {
        loading.value = false;
    }
};

const saveGrades = async () => {
    if (!form.value.course_id || !form.value.partial) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Selecciona un curso y un parcial',
        });
        return;
    }

    const gradesData = students.value.map(s => ({
        student_id: s.id,
        grade: s.grade || null,
        observations: s.observations || null,
    }));

    loading.value = true;
    try {
        await axios.post('/academic/grades', {
            course_id: form.value.course_id,
            partial: form.value.partial,
            grades: gradesData,
        });
        
        Swal.fire({
            icon: 'success',
            title: '✅ Calificaciones guardadas',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        router.push('/academic/grades');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudieron guardar las calificaciones',
        });
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadCourses();
});
</script>