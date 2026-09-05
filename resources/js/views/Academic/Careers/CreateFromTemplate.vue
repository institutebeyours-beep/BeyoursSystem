<template>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">🚀 Crear Carrera desde Plantilla</h1>
            <p class="text-emerald-100 mt-2">Selecciona una plantilla y personaliza los datos para crear una nueva carrera</p>
        </div>

        <!-- ✅ ALERTA DE ERRORES GENERALES -->
        <div v-if="generalError" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            <div class="flex items-start gap-3">
                <span class="text-red-500 text-xl">❌</span>
                <div>
                    <h4 class="font-semibold text-red-700 dark:text-red-400">Error al crear la carrera</h4>
                    <p class="text-sm text-red-600 dark:text-red-300">{{ generalError }}</p>
                </div>
                <button @click="generalError = null" class="ml-auto text-red-400 hover:text-red-600">✕</button>
            </div>
        </div>

        <!-- FORMULARIO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            <form @submit.prevent="createCareer">
                
                <!-- SELECCIÓN DE PLANTILLA -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        📋 Selecciona una Plantilla
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Plantilla *
                            </label>
                            <select 
                                v-model="form.template_id"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                :class="errors.template_id ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600'"
                                @change="loadTemplateData"
                                required
                            >
                                <option value="">Selecciona una plantilla</option>
                                <option 
                                    v-for="template in templates" 
                                    :key="template.id" 
                                    :value="template.id"
                                >
                                    {{ template.name }} ({{ template.code }})
                                    <span v-if="template.is_default" class="text-yellow-500">⭐</span>
                                </option>
                            </select>
                            <p v-if="errors.template_id" class="text-red-500 text-sm mt-1">{{ errors.template_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tipo de Enseñanza *
                            </label>
                            <select 
                                v-model="form.education_type_id"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                :class="errors.education_type_id ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600'"
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
                            <p v-if="errors.education_type_id" class="text-red-500 text-sm mt-1">{{ errors.education_type_id }}</p>
                        </div>
                    </div>

                    <!-- Resumen de la plantilla seleccionada -->
                    <div v-if="selectedTemplate" class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <!-- ... contenido del resumen ... -->
                    </div>
                </div>

                <!-- DATOS DE LA CARRERA -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        📋 Datos de la Carrera
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre de la Carrera *
                            </label>
                            <input 
                                v-model="form.name"
                                type="text"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                :class="errors.name ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600'"
                                placeholder="Ej: Técnico Superior en Idiomas"
                                required
                            />
                            <p v-if="errors.name" class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <span>❌</span> {{ errors.name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Código *
                            </label>
                            <input 
                                v-model="form.code"
                                type="text"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                :class="errors.code ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600'"
                                placeholder="Ej: TS-IDIOMAS"
                                required
                            />
                            <p v-if="errors.code" class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <span>❌</span> {{ errors.code }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Descripción
                        </label>
                        <textarea 
                            v-model="form.description"
                            rows="2"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Descripción de la carrera..."
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Créditos totales
                            </label>
                            <input 
                                v-model.number="form.total_credits"
                                type="number"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Duración (años)
                            </label>
                            <input 
                                v-model.number="form.duration_years"
                                type="number"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Semestres
                            </label>
                            <input 
                                v-model.number="form.duration_semesters"
                                type="number"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0"
                            />
                            <p class="text-xs text-gray-400">Se calculará automáticamente si se deja en 0</p>
                        </div>
                    </div>
                </div>

                <!-- ESTRUCTURA PREVIEW -->
                <div v-if="showStructure" class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
                    <!-- ... contenido de estructura ... -->
                </div>

                <!-- BOTONES -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <router-link 
                        to="/academic/careers"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                    >
                        Cancelar
                    </router-link>
                    <button 
                        type="submit"
                        :disabled="creating"
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="creating" class="animate-spin">⟳</span>
                        {{ creating ? 'Creando...' : '🚀 Crear Carrera' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import templatesApi from '@/api/academic/templates';

const router = useRouter();
const route = useRoute();

// =============================================
// STATE
// =============================================
const templates = ref([]);
const educationTypes = ref([]);
const selectedTemplate = ref(null);
const structureData = ref([]);
const showStructure = ref(false);
const creating = ref(false);
const errors = ref({});
const loading = ref(false);
const generalError = ref(null);

const form = ref({
    template_id: '',
    education_type_id: '',
    name: '',
    code: '',
    description: '',
    total_credits: 0,
    theoretical_hours: 0,
    practical_hours: 0,
    duration_years: 0,
    duration_semesters: 0,
});

// =============================================
// COMPUTED
// =============================================

const totalSubjects = computed(() => {
    if (!selectedTemplate.value?.semesters) return 0;
    return selectedTemplate.value.semesters.reduce((acc, s) => acc + (s.subjects?.length || 0), 0);
});

// =============================================
// FUNCIONES
// =============================================

const loadTemplates = async () => {
    try {
        loading.value = true;
        const response = await templatesApi.getAllActive();
        templates.value = response.data.templates || [];
        console.log('✅ Plantillas cargadas:', templates.value.length);
    } catch (error) {
        console.error('Error cargando plantillas:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las plantillas'
        });
    } finally {
        loading.value = false;
    }
};

const loadEducationTypes = async () => {
    try {
        const response = await axios.get('/api/education-types/public');
        educationTypes.value = response.data.types || [];
        console.log('✅ Tipos de enseñanza cargados:', educationTypes.value.length);
    } catch (error) {
        console.error('Error cargando tipos de enseñanza:', error);
        educationTypes.value = [];
    }
};

const loadTemplateData = async () => {
    if (!form.value.template_id) {
        selectedTemplate.value = null;
        structureData.value = [];
        showStructure.value = false;
        return;
    }

    try {
        loading.value = true;
        const response = await templatesApi.preview(form.value.template_id);
        selectedTemplate.value = response.data.template;
        
        console.log('📋 Plantilla seleccionada:', selectedTemplate.value);
        
        // ✅ EXTRAER HORAS DE CADA ASIGNATURA
        let totalTheoretical = 0;
        let totalPractical = 0;
        let totalCredits = 0;
        
        if (selectedTemplate.value.semesters) {
            selectedTemplate.value.semesters.forEach(semester => {
                if (semester.subjects) {
                    semester.subjects.forEach(subject => {
                        totalTheoretical += parseFloat(subject.theoretical_hours) || 0;
                        totalPractical += parseFloat(subject.practical_hours) || 0;
                        totalCredits += parseFloat(subject.credits) || 0;
                    });
                }
            });
        }

        console.log('📊 Horas calculadas:', {
            theoretical: totalTheoretical,
            practical: totalPractical,
            total: totalTheoretical + totalPractical,
            credits: totalCredits
        });

        // Preparar estructura para mostrar
        structureData.value = selectedTemplate.value.semesters.map(s => ({
            number: s.semester_number,
            credits: s.total_credits || 0,
            hours: s.total_hours || 0,
            subjects: s.subjects.map(sub => ({
                name: sub.name,
                credits: sub.credits || 0,
                theoretical_hours: sub.theoretical_hours || 0,
                practical_hours: sub.practical_hours || 0,
            })),
        }));

        // ✅ AUTOFILL DE DATOS
        form.value.name = selectedTemplate.value.name;
        form.value.code = selectedTemplate.value.code;
        form.value.description = selectedTemplate.value.description || '';
        form.value.total_credits = selectedTemplate.value.total_credits || totalCredits || 0;
        form.value.duration_semesters = selectedTemplate.value.semesters?.length || 0;
        form.value.duration_years = Math.ceil((selectedTemplate.value.semesters?.length || 0) / 2);
        
        // ✅ ASIGNAR HORAS CALCULADAS
        form.value.theoretical_hours = totalTheoretical;
        form.value.practical_hours = totalPractical;

        console.log('✅ Formulario actualizado:', form.value);

        showStructure.value = true;
    } catch (error) {
        console.error('Error cargando datos de plantilla:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los datos de la plantilla'
        });
    } finally {
        loading.value = false;
    }
};

const previewTemplate = () => {
    showStructure.value = !showStructure.value;
};

const createCareer = async () => {
    errors.value = {};
    generalError.value = null;

    // Validaciones
    if (!form.value.template_id) {
        errors.value.template_id = 'Debes seleccionar una plantilla';
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona una plantilla',
            text: 'Debes seleccionar una plantilla para crear la carrera'
        });
        return;
    }

    if (!form.value.name) {
        errors.value.name = 'El nombre de la carrera es obligatorio';
        return;
    }

    if (!form.value.code) {
        errors.value.code = 'El código de la carrera es obligatorio';
        return;
    }

    if (!form.value.education_type_id) {
        errors.value.education_type_id = 'Debes seleccionar un tipo de enseñanza';
        return;
    }

    try {
        creating.value = true;

        const payload = {
            template_id: form.value.template_id,
            name: form.value.name,
            code: form.value.code,
            description: form.value.description,
            education_type_id: form.value.education_type_id,
            total_credits: form.value.total_credits || 0,
            theoretical_hours: form.value.theoretical_hours || 0,
            practical_hours: form.value.practical_hours || 0,
            duration_years: form.value.duration_years || 0,
            duration_semesters: form.value.duration_semesters || 0,
        };

        console.log('📤 Enviando payload:', payload);

        await axios.post('/api/academic/careers/create-from-template', payload);

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
            const firstErrorKey = Object.keys(errors.value)[0];
            const firstErrorMessage = errors.value[firstErrorKey];
            
            Swal.fire({
                icon: 'error',
                title: '❌ Error de validación',
                text: Array.isArray(firstErrorMessage) ? firstErrorMessage[0] : firstErrorMessage,
                confirmButtonColor: '#10B981'
            });
        } else if (error.response?.data?.message) {
            generalError.value = error.response.data.message;
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response.data.message,
                confirmButtonColor: '#10B981'
            });
        } else {
            generalError.value = 'No se pudo crear la carrera. Intenta nuevamente.';
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: 'No se pudo crear la carrera. Intenta nuevamente.',
                confirmButtonColor: '#10B981'
            });
        }
    } finally {
        creating.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadTemplates();
    loadEducationTypes();

    if (route.query.template_id) {
        form.value.template_id = parseInt(route.query.template_id);
        loadTemplateData();
    }
});
</script>