<template>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">➕ Nueva Plantilla Académica</h1>
            <p class="text-purple-100 mt-2">Crea una plantilla para carreras con su estructura completa</p>
        </div>

        <!-- FORMULARIO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <form @submit.prevent="saveTemplate">
                <!-- ========================================== -->
                <!-- DATOS GENERALES -->
                <!-- ========================================== -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        📋 Datos Generales
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre *
                            </label>
                            <input 
                                v-model="form.name"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="Ej: Técnico Superior en Idiomas"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Código *
                            </label>
                            <input 
                                v-model="form.code"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="Ej: TS-IDIOMAS"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tipo de Enseñanza *
                            </label>
                            <select 
                                v-model="form.education_type_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                required
                            >
                                <option value="">Selecciona un tipo</option>
                                <option 
                                    v-for="type in educationTypes" 
                                    :key="type.id" 
                                    :value="type.id"
                                >
                                    {{ type.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                ¿Predeterminada?
                            </label>
                            <div class="flex items-center gap-2 mt-2">
                                <input 
                                    type="checkbox" 
                                    v-model="form.is_default"
                                    :disabled="!canSetDefault"
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                />
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ canSetDefault ? 'Marcar como plantilla predeterminada' : 'Solo administradores pueden crear plantillas predeterminadas' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Descripción
                        </label>
                        <textarea 
                            v-model="form.description"
                            rows="2"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Descripción de la plantilla..."
                        ></textarea>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SEMESTRES -->
                <!-- ========================================== -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            📚 Semestres ({{ form.semesters.length }})
                        </h3>
                        <button 
                            type="button"
                            @click="addSemester"
                            class="px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm flex items-center gap-1"
                        >
                            ➕ Agregar Semestre
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div 
                            v-for="(semester, sIndex) in form.semesters" 
                            :key="sIndex"
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ semester.number }}° Semestre
                                </h4>
                                <button 
                                    type="button"
                                    @click="removeSemester(sIndex)"
                                    class="text-red-500 hover:text-red-700 transition"
                                    title="Eliminar semestre"
                                >
                                    🗑️
                                </button>
                            </div>

                            <!-- Datos del semestre -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                        Número *
                                    </label>
                                    <input 
                                        v-model.number="semester.number"
                                        type="number"
                                        min="1"
                                        class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                        Horas
                                    </label>
                                    <input 
                                        v-model.number="semester.hours"
                                        type="number"
                                        min="0"
                                        class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                        placeholder="450"
                                    />
                                </div>
                            </div>

                            <!-- Asignaturas del semestre -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        Asignaturas ({{ semester.subjects.length }})
                                    </span>
                                    <button 
                                        type="button"
                                        @click="addSubject(sIndex)"
                                        class="text-xs text-purple-600 hover:text-purple-700 transition"
                                    >
                                        ➕ Agregar
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div 
                                        v-for="(subject, subIndex) in semester.subjects" 
                                        :key="subIndex"
                                        class="flex items-center gap-2"
                                    >
                                        <span class="text-xs text-gray-400 font-mono w-6">{{ subIndex + 1 }}</span>
                                        <input 
                                            v-model="subject.name"
                                            type="text"
                                            class="flex-1 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                            placeholder="Nombre de la asignatura"
                                        />
                                        <input 
                                            v-model.number="subject.credits"
                                            type="number"
                                            min="0"
                                            class="w-16 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                            placeholder="Créd."
                                        />
                                        <input 
                                            v-model.number="subject.theoretical_hours"
                                            type="number"
                                            min="0"
                                            class="w-14 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                            placeholder="T"
                                        />
                                        <input 
                                            v-model.number="subject.practical_hours"
                                            type="number"
                                            min="0"
                                            class="w-14 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                            placeholder="P"
                                        />
                                        <button 
                                            type="button"
                                            @click="removeSubject(sIndex, subIndex)"
                                            class="text-red-500 hover:text-red-700 transition text-sm"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                    <div v-if="semester.subjects.length === 0" class="text-sm text-gray-400 text-center py-2">
                                        No hay asignaturas en este semestre
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="form.semesters.length === 0" class="text-center py-4 text-gray-400">
                        No hay semestres agregados. Haz clic en "Agregar Semestre" para comenzar.
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BOTONES -->
                <!-- ========================================== -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <router-link 
                        to="/admin/templates"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                    >
                        Cancelar
                    </router-link>
                    <button 
                        type="submit"
                        :disabled="saving"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="saving" class="animate-spin">⟳</span>
                        {{ saving ? 'Guardando...' : '💾 Guardar Plantilla' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import Swal from 'sweetalert2';
import templatesApi from '@/api/academic/templates';

const router = useRouter();
const authStore = useAuthStore();

// =============================================
// STATE
// =============================================
const educationTypes = ref([]);
const saving = ref(false);

const form = ref({
    name: '',
    code: '',
    description: '',
    education_type_id: '',
    is_default: false,
    semesters: [],
});

// =============================================
// COMPUTED - PERMISOS
// =============================================

const canSetDefault = computed(() => {
    return authStore.hasRole('admin') || authStore.hasRole('super-admin');
});

// =============================================
// FUNCIONES
// =============================================

const loadEducationTypes = async () => {
    try {
        const response = await axios.get('/api/admin/education-types/all');
        educationTypes.value = response.data.types || [];
    } catch (error) {
        console.error('Error cargando tipos de enseñanza:', error);
    }
};

const addSemester = () => {
    const number = form.value.semesters.length + 1;
    form.value.semesters.push({
        number: number,
        hours: 0,
        credits: 0,
        subjects: [],
    });
};

const removeSemester = (index) => {
    if (form.value.semesters.length <= 1) {
        Swal.fire({
            icon: 'warning',
            title: 'No se puede eliminar',
            text: 'La plantilla debe tener al menos un semestre'
        });
        return;
    }
    form.value.semesters.splice(index, 1);
    // Reordenar números
    form.value.semesters.forEach((s, i) => {
        s.number = i + 1;
    });
};

const addSubject = (semesterIndex) => {
    form.value.semesters[semesterIndex].subjects.push({
        name: '',
        code: '',
        credits: 0,
        theoretical_hours: 0,
        practical_hours: 0,
        description: '',
        order: form.value.semesters[semesterIndex].subjects.length,
    });
};

const removeSubject = (semesterIndex, subjectIndex) => {
    form.value.semesters[semesterIndex].subjects.splice(subjectIndex, 1);
};

const saveTemplate = async () => {
    // ✅ Validaciones
    if (!form.value.name || !form.value.code || !form.value.education_type_id) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Nombre, código y tipo de enseñanza son obligatorios'
        });
        return;
    }

    if (form.value.semesters.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin semestres',
            text: 'Agrega al menos un semestre a la plantilla'
        });
        return;
    }

    // Validar que cada semestre tenga asignaturas
    let hasEmptySemester = false;
    form.value.semesters.forEach(semester => {
        if (semester.subjects.length === 0) {
            hasEmptySemester = true;
        }
    });

    if (hasEmptySemester) {
        Swal.fire({
            icon: 'warning',
            title: 'Semestres sin asignaturas',
            text: 'Cada semestre debe tener al menos una asignatura'
        });
        return;
    }

    try {
        saving.value = true;

        // Calcular créditos y horas automáticamente
        const semesters = form.value.semesters.map(s => {
            const totalCredits = s.subjects.reduce((sum, sub) => sum + (sub.credits || 0), 0);
            const totalHours = s.subjects.reduce((sum, sub) => sum + (sub.theoretical_hours || 0) + (sub.practical_hours || 0), 0);
            
            return {
                number: s.number,
                hours: s.hours || totalHours,
                credits: s.credits || totalCredits,
                subjects: s.subjects.map(sub => ({
                    name: sub.name,
                    code: sub.code || null,
                    credits: sub.credits || 0,
                    theoretical_hours: sub.theoretical_hours || 0,
                    practical_hours: sub.practical_hours || 0,
                    description: sub.description || null,
                    order: sub.order || 0,
                })),
            };
        });

        const payload = {
            name: form.value.name,
            code: form.value.code,
            description: form.value.description,
            education_type_id: form.value.education_type_id,
            is_default: canSetDefault.value ? form.value.is_default : false,
            semesters: semesters,
        };

        await templatesApi.create(payload);

        Swal.fire({
            icon: 'success',
            title: '✅ Plantilla creada exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        router.push('/admin/templates');
    } catch (error) {
        console.error('Error creando plantilla:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo crear la plantilla'
        });
    } finally {
        saving.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadEducationTypes();
    // Agregar un semestre por defecto
    addSemester();
});
</script>