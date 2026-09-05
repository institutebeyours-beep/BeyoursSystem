<template>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <h1 class="text-3xl font-black">📊 Configuración de Calificaciones</h1>
            <p class="text-purple-100 mt-2">Define los componentes y porcentajes para cada curso</p>
        </div>

        <!-- CONFIGURACIÓN -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
            
            <!-- Seleccionar Curso -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Curso
                </label>
                <select 
                    v-model="selectedCourse"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    @change="onCourseChange"
                >
                    <option value="">Selecciona un curso</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">
                        {{ course.name }} ({{ course.code }})
                    </option>
                </select>
                
                <div v-if="loadingCourses" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    ⏳ Cargando cursos...
                </div>
            </div>

            <!-- Contenido principal -->
            <div v-if="selectedCourse">
                
                <!-- CONFIGURACIÓN EXISTENTE -->
                <div v-if="configuration">
                    
                    <!-- Encabezado -->
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            📋 {{ configuration.name }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                @click="openCloneModal"
                                :disabled="!hasComponents"
                                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                📋 Clonar
                            </button>
                            <button 
                                @click="openAddComponentModal"
                                class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm"
                            >
                                ➕ Agregar
                            </button>
                        </div>
                    </div>

                    <!-- Lista de componentes -->
                    <div class="space-y-2">
                        <div 
                            v-for="(component, index) in (configuration?.components || [])" 
                            :key="component.id || index"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900/70 transition"
                        >
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-medium text-gray-700 dark:text-gray-300 truncate">
                                        {{ component.name }}
                                    </span>
                                    <span 
                                        class="text-xs px-2 py-1 rounded-full whitespace-nowrap" 
                                        :class="getTypeClass(component.type_id)"
                                    >
                                        {{ getTypeLabel(component.type_id) }}
                                    </span>
                                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">
                                        {{ component.percentage }}%
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span>Máx: <strong>{{ component.max_grade }}</strong></span>
                                    <span v-if="component.description" class="truncate">
                                        📝 {{ component.description }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 ml-2">
                                <button 
                                    @click="openEditComponentModal(index)"
                                    class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition"
                                    title="Editar"
                                >
                                    ✏️
                                </button>
                                <button 
                                    @click="removeComponent(index)"
                                    class="p-1.5 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition"
                                    title="Eliminar"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>

                        <div v-if="!configuration?.components?.length" class="text-center py-4 text-gray-400 dark:text-gray-500">
                            No hay componentes agregados
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                Total porcentaje
                            </span>
                            <span 
                                class="text-lg font-bold"
                                :class="totalPercentage === 100 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                            >
                                {{ totalPercentage }}%
                            </span>
                        </div>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div 
                                class="h-2.5 rounded-full transition-all duration-500"
                                :class="totalPercentage === 100 ? 'bg-green-600' : 'bg-purple-600'"
                                :style="{ width: Math.min(totalPercentage, 100) + '%' }"
                            ></div>
                        </div>
                        
                        <div class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span>0%</span>
                            <span v-if="totalPercentage < 100">
                                Faltan <strong>{{ remainingPercentage }}%</strong>
                            </span>
                            <span v-else class="text-green-600 dark:text-green-400 font-medium">
                                ✅ Completado
                            </span>
                            <span>100%</span>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <button 
                            @click="saveConfiguration"
                            :disabled="loading || totalPercentage !== 100 || !hasComponents"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <span v-if="loading" class="animate-spin">⟳</span>
                            💾 Guardar Configuración
                        </button>
                    </div>
                </div>

                <!-- SIN CONFIGURACIÓN -->
                <div v-else class="text-center py-8">
                    <p class="text-gray-400 dark:text-gray-500">
                        No hay configuración para este curso
                    </p>
                    <button 
                        @click="createConfiguration"
                        :disabled="loading"
                        class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
                    >
                        <span v-if="loading" class="animate-spin inline-block mr-2">⟳</span>
                        Crear Configuración
                    </button>
                </div>
            </div>

            <!-- Sin curso seleccionado -->
            <div v-else class="text-center py-8">
                <p class="text-gray-400 dark:text-gray-500">
                    Selecciona un curso para configurar sus calificaciones
                </p>
            </div>
        </div>

        <!-- MODAL: Agregar/Editar Componente -->
        <div v-if="showComponentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    {{ isEditing ? '✏️ Editar Componente' : '➕ Agregar Componente' }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre *
                        </label>
                        <input 
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Ej: Parcial 1"
                            maxlength="255"
                        />
                    </div>
                    
                    <TypeSelector v-model="form.type_id" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Porcentaje (%) *
                        </label>
                        <input 
                            v-model.number="form.percentage"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="0"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Calificación máxima
                        </label>
                        <input 
                            v-model.number="form.max_grade"
                            type="number"
                            min="1"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="100"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Descripción (opcional)
                        </label>
                        <input 
                            v-model="form.description"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Descripción del componente"
                            maxlength="500"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        @click="closeComponentModal"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="saveComponent"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                    >
                        💾 Guardar
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Clonar Configuración -->
        <div v-if="showCloneModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    📋 Clonar Configuración
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Curso destino *
                        </label>
                        <select 
                            v-model="cloneTargetCourse"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            @change="checkTargetCourseConfiguration"
                        >
                            <option value="">Selecciona un curso</option>
                            <option 
                                v-for="course in availableCoursesForClone" 
                                :key="course.id" 
                                :value="course.id"
                            >
                                {{ course.name }} ({{ course.code }})
                                <span v-if="targetHasConfig" class="text-yellow-500 text-xs">
                                    ⚠️ Tiene configuración
                                </span>
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre de la configuración
                        </label>
                        <input 
                            v-model="cloneName"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Configuración clonada"
                            maxlength="255"
                        />
                    </div>

                    <div v-if="targetHasConfig" 
                         class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <span class="text-yellow-600 dark:text-yellow-400 text-lg">⚠️</span>
                            <div class="flex-1">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 font-medium">
                                    El curso destino ya tiene una configuración
                                </p>
                                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="replaceExisting"
                                        class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                    />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        Reemplazar configuración existente
                                    </span>
                                </label>
                                <p v-if="replaceExisting" class="text-xs text-red-600 dark:text-red-400 mt-1">
                                    ⚠️ Esta acción eliminará la configuración actual del curso destino
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        @click="closeCloneModal"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="executeClone"
                        :disabled="!cloneTargetCourse || loading"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="loading" class="animate-spin inline-block mr-2">⟳</span>
                        {{ replaceExisting && targetHasConfig ? '🔄 Reemplazar' : '📋 Clonar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import TypeSelector from '@/views/Academic/Grades/Configurations/TypeSelector.vue';

// =============================================
// STATE
// =============================================
const courses = ref([]);
const selectedCourse = ref('');
const configuration = ref(null);
const loading = ref(false);
const loadingCourses = ref(false);

// Modal: Componente
const showComponentModal = ref(false);
const isEditing = ref(false);
const editingIndex = ref(null);
const form = ref({
    name: '',
    type_id: null,
    percentage: 0,
    max_grade: 100,
    description: '',
});

// Modal: Clonar
const showCloneModal = ref(false);
const cloneTargetCourse = ref('');
const cloneName = ref('');
const replaceExisting = ref(false);
const targetHasConfig = ref(false);

// =============================================
// COMPUTED
// =============================================

const totalPercentage = computed(() => {
    if (!configuration.value?.components?.length) return 0;
    const total = configuration.value.components.reduce((sum, c) => {
        return sum + (parseFloat(c.percentage) || 0);
    }, 0);
    return Math.round(total * 100) / 100;
});

const remainingPercentage = computed(() => {
    const total = totalPercentage.value;
    if (total >= 100) return 0;
    return Math.round((100 - total) * 100) / 100;
});

const hasComponents = computed(() => {
    return configuration.value?.components?.length > 0;
});

const availableCoursesForClone = computed(() => {
    return courses.value.filter(course => course.id !== selectedCourse.value);
});

// =============================================
// FUNCIONES - CURSOS
// =============================================

const loadCourses = async () => {
    try {
        loadingCourses.value = true;
        const response = await axios.get('/api/academic/courses?per_page=100');
        
        let coursesData = [];
        if (response.data?.data && Array.isArray(response.data.data)) {
            coursesData = response.data.data;
        } else if (Array.isArray(response.data)) {
            coursesData = response.data;
        }
        
        courses.value = coursesData;
        console.log(`✅ ${coursesData.length} cursos cargados`);
    } catch (error) {
        console.error('Error cargando cursos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al cargar cursos',
            text: error.response?.data?.message || 'No se pudieron cargar los cursos',
        });
    } finally {
        loadingCourses.value = false;
    }
};

// =============================================
// FUNCIONES - CONFIGURACIÓN
// =============================================

const loadConfiguration = async () => {
    if (!selectedCourse.value) {
        configuration.value = null;
        return;
    }

    try {
        loading.value = true;
        const response = await axios.get(`/api/academic/grades/configuration/${selectedCourse.value}`);
        configuration.value = response.data.configuration || null;
        
        if (configuration.value?.components) {
            configuration.value.components = configuration.value.components.map(c => ({
                ...c,
                percentage: parseFloat(c.percentage) || 0,
                max_grade: parseFloat(c.max_grade) || 100,
                type_id: c.type_id || null,
            }));
            console.log(`✅ Configuración cargada: ${configuration.value.name} (${configuration.value.components.length} componentes)`);
        } else {
            console.log('ℹ️ Sin configuración para este curso');
        }
    } catch (error) {
        console.error('Error cargando configuración:', error);
        configuration.value = null;
    } finally {
        loading.value = false;
    }
};

const onCourseChange = () => {
    loadConfiguration();
};

const createConfiguration = async () => {
    if (!selectedCourse.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona un curso',
            text: 'Debes seleccionar un curso para crear su configuración',
        });
        return;
    }

    try {
        loading.value = true;
        const courseName = courses.value.find(c => c.id === selectedCourse.value)?.name || 'Curso';
        
        const response = await axios.post('/api/academic/grades/configuration', {
            course_id: selectedCourse.value,
            name: `Configuración - ${courseName}`,
        });
        
        configuration.value = response.data.configuration;
        await loadConfiguration();
        
        Swal.fire({
            icon: 'success',
            title: '✅ Configuración creada',
            text: 'Ahora puedes agregar componentes',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        console.error('Error creando configuración:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo crear la configuración',
        });
    } finally {
        loading.value = false;
    }
};

// =============================================
// FUNCIONES - COMPONENTES
// =============================================

const openAddComponentModal = () => {
    isEditing.value = false;
    editingIndex.value = null;
    form.value = {
        name: '',
        type_id: null,
        percentage: 0,
        max_grade: 100,
        description: '',
    };
    showComponentModal.value = true;
};

const openEditComponentModal = (index) => {
    isEditing.value = true;
    editingIndex.value = index;
    const component = configuration.value.components[index];
    form.value = {
        name: component.name || '',
        type_id: component.type_id || null,
        percentage: parseFloat(component.percentage) || 0,
        max_grade: parseFloat(component.max_grade) || 100,
        description: component.description || '',
    };
    showComponentModal.value = true;
};

const closeComponentModal = () => {
    showComponentModal.value = false;
    isEditing.value = false;
    editingIndex.value = null;
};

const saveComponent = () => {
    if (!form.value.name?.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'El nombre del componente es obligatorio',
        });
        return;
    }

    if (!form.value.type_id) {
        Swal.fire({
            icon: 'warning',
            title: 'Tipo no seleccionado',
            text: 'Debes seleccionar un tipo de componente',
        });
        return;
    }

    const percentage = parseFloat(form.value.percentage);
    if (isNaN(percentage) || percentage < 0 || percentage > 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Porcentaje inválido',
            text: 'El porcentaje debe ser un número entre 0 y 100',
        });
        return;
    }

    const newComponent = {
        name: form.value.name.trim(),
        type_id: form.value.type_id,
        percentage: Math.round(percentage * 100) / 100,
        max_grade: parseFloat(form.value.max_grade) || 100,
        description: form.value.description?.trim() || null,
        order: configuration.value.components.length,
    };

    if (isEditing.value && editingIndex.value !== null) {
        configuration.value.components[editingIndex.value] = newComponent;
    } else {
        configuration.value.components.push(newComponent);
    }

    closeComponentModal();
    
    Swal.fire({
        icon: 'success',
        title: '✅ Componente guardado',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

const removeComponent = (index) => {
    const componentName = configuration.value.components[index].name;
    
    Swal.fire({
        title: '¿Eliminar componente?',
        text: `¿Estás seguro de eliminar "${componentName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            configuration.value.components.splice(index, 1);
            Swal.fire({
                icon: 'success',
                title: 'Componente eliminado',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });
};

// =============================================
// FUNCIONES - GUARDAR CONFIGURACIÓN
// =============================================

const saveConfiguration = async () => {
    if (!hasComponents.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin componentes',
            text: 'Agrega al menos un componente a la configuración',
        });
        return;
    }

    if (totalPercentage.value !== 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Porcentaje incorrecto',
            text: `La suma de porcentajes debe ser 100% (actual: ${totalPercentage.value}%)`,
        });
        return;
    }

    try {
        loading.value = true;
        
        const componentsToSave = configuration.value.components.map(c => ({
            name: c.name,
            type_id: c.type_id,
            percentage: parseFloat(c.percentage) || 0,
            max_grade: parseFloat(c.max_grade) || 100,
            description: c.description || null,
            order: c.order || 0,
        }));

        await axios.put(`/api/academic/grades/configuration/${configuration.value.id}`, {
            name: configuration.value.name,
            components: componentsToSave,
        });
        
        Swal.fire({
            icon: 'success',
            title: '✅ Configuración guardada',
            text: `Se guardaron ${componentsToSave.length} componentes`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } catch (error) {
        console.error('Error guardando configuración:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo guardar la configuración',
        });
    } finally {
        loading.value = false;
    }
};

// =============================================
// FUNCIONES - CLONAR
// =============================================

const checkTargetCourseConfiguration = async () => {
    if (!cloneTargetCourse.value) {
        targetHasConfig.value = false;
        return;
    }

    try {
        const response = await axios.get(`/api/academic/grades/configuration/${cloneTargetCourse.value}`);
        targetHasConfig.value = response.data.configuration !== null;
        if (targetHasConfig.value) {
            replaceExisting.value = false;
        }
    } catch (error) {
        targetHasConfig.value = false;
    }
};

const openCloneModal = async () => {
    if (!hasComponents.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin componentes',
            text: 'Agrega componentes antes de clonar la configuración',
        });
        return;
    }
    
    cloneTargetCourse.value = '';
    cloneName.value = `${configuration.value.name} (clonado)`;
    replaceExisting.value = false;
    targetHasConfig.value = false;
    showCloneModal.value = true;
};

const closeCloneModal = () => {
    showCloneModal.value = false;
    cloneTargetCourse.value = '';
    cloneName.value = '';
    replaceExisting.value = false;
    targetHasConfig.value = false;
};

const executeClone = async () => {
    if (!cloneTargetCourse.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona un curso',
            text: 'Debes seleccionar un curso destino',
        });
        return;
    }

    try {
        loading.value = true;
        
        const payload = {
            source_course_id: selectedCourse.value,
            target_course_id: cloneTargetCourse.value,
            name: cloneName.value || `${configuration.value.name} (clonado)`,
            replace: replaceExisting.value,
        };

        const response = await axios.post('/api/academic/grades/configuration/clone', payload);

        closeCloneModal();
        
        const targetCourse = courses.value.find(c => c.id === cloneTargetCourse.value);
        
        Swal.fire({
            icon: 'success',
            title: response.data.replaced ? '🔄 Configuración reemplazada' : '📋 Configuración clonada',
            text: `Se ${response.data.replaced ? 'reemplazó' : 'clonó'} la configuración al curso "${targetCourse?.name || 'destino'}" con ${response.data.components_cloned || 0} componentes`,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        if (selectedCourse.value === cloneTargetCourse.value) {
            await loadConfiguration();
        }
    } catch (error) {
        console.error('Error clonando configuración:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo clonar la configuración',
        });
    } finally {
        loading.value = false;
    }
};

// =============================================
// FUNCIONES - UTILIDADES
// =============================================

// Mapeo de type_id a clases CSS
const getTypeClass = (typeId) => {
    const map = {
        1: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        2: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
        3: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        4: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
        5: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        6: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        7: 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
    };
    return map[typeId] || map[7];
};

// Mapeo de type_id a etiquetas
const getTypeLabel = (typeId) => {
    const map = {
        1: '📝 Parcial',
        2: '📊 Final',
        3: '📋 Asistencia',
        4: '🚀 Proyecto',
        5: '📚 Tarea',
        6: '🧪 Quiz',
        7: '📌 Otro',
    };
    return map[typeId] || '📌 Otro';
};

// =============================================
// WATCH
// =============================================

watch(selectedCourse, () => {
    loadConfiguration();
});

// =============================================
// LIFECYCLE
// =============================================

onMounted(() => {
    loadCourses();
});
</script>