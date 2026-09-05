<template>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black">{{ template?.name || 'Cargando...' }}</h1>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-mono">
                            {{ template?.code }}
                        </span>
                    </div>
                    <p class="text-purple-100 mt-2">{{ template?.description || 'Sin descripción' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        v-if="canEdit"
                        @click="editTemplate"
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        ✏️ Editar
                    </button>
                    <button 
                        @click="cloneTemplate"
                        class="bg-purple-500/30 hover:bg-purple-500/40 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        📋 Clonar
                    </button>
                    <router-link 
                        to="/admin/templates"
                        class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition text-sm flex items-center gap-2"
                    >
                        📋 Volver
                    </router-link>
                </div>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div v-if="loading" class="text-center py-12">
            <span class="animate-spin inline-block text-4xl">⟳</span>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando plantilla...</p>
        </div>

        <div v-else-if="template" class="space-y-6">
            <!-- Resumen -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 Resumen</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tipo</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ template.education_type?.name || 'No especificado' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Semestres</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ summary.total_semesters || 0 }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Créditos totales</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ summary.total_credits || 0 }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Asignaturas totales</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ summary.total_subjects || 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estructura de Semestres -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">📚 Estructura Académica</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ template.semesters?.length || 0 }} semestres
                    </span>
                </div>

                <div class="space-y-4">
                    <div 
                        v-for="semester in template.semesters" 
                        :key="semester.id"
                        class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                    >
                        <!-- Encabezado del semestre -->
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                    {{ semester.formatted_name || `${semester.semester_number}° Semestre` }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ semester.total_hours || 0 }} horas · {{ semester.total_credits || 0 }} créditos
                                </p>
                            </div>
                            <span class="text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 px-2 py-1 rounded-full">
                                {{ semester.subjects?.length || 0 }} asignaturas
                            </span>
                        </div>

                        <!-- Lista de asignaturas -->
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div 
                                    v-for="(subject, index) in semester.subjects" 
                                    :key="subject.id"
                                    class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-900/30 rounded-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400 font-mono w-6">{{ index + 1 }}</span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ subject.name }}</span>
                                        <span v-if="subject.code" class="text-xs text-gray-400 font-mono">
                                            ({{ subject.code }})
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span>📚 {{ subject.credits || 0 }} créditos</span>
                                        <span v-if="subject.total_hours > 0">⏱️ {{ subject.total_hours }}h</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!semester.subjects?.length" class="text-sm text-gray-400 text-center py-2">
                                No hay asignaturas en este semestre
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Información Adicional</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Creado por</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ template.creator?.name || 'Sistema' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Fecha de creación</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ formatDate(template.created_at) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                        <span 
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                            :class="template.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'"
                        >
                            {{ template.is_active ? '✅ Activa' : '⬜ Inactiva' }}
                        </span>
                        <span 
                            v-if="template.is_default"
                            class="inline-flex items-center ml-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400"
                        >
                            ⭐ Predeterminada
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Última actualización</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ formatDate(template.updated_at) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <router-link 
                    v-if="canEdit"
                    :to="`/admin/templates/${template.id}/edit`"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm flex items-center gap-2"
                >
                    ✏️ Editar Plantilla
                </router-link>
                <button 
                    @click="useTemplate"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm flex items-center gap-2"
                >
                    🚀 Usar Plantilla
                </button>
                <router-link 
                    to="/admin/templates"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition text-sm flex items-center gap-2"
                >
                    📋 Volver a Plantillas
                </router-link>
            </div>
        </div>
    </div>

    <!-- MODAL: CLONAR PLANTILLA -->
    <div v-if="showCloneModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                📋 Clonar Plantilla
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre *
                    </label>
                    <input 
                        v-model="cloneForm.name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Ej: Mi Plantilla Personalizada"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Código *
                    </label>
                    <input 
                        v-model="cloneForm.code"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Ej: MI-PLANTILLA"
                    />
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        ⚠️ Se clonarán todos los semestres y asignaturas de la plantilla original.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    @click="closeCloneModal"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 transition"
                >
                    Cancelar
                </button>
                <button 
                    @click="executeClone"
                    :disabled="!cloneForm.name || !cloneForm.code || cloning"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
                >
                    <span v-if="cloning" class="animate-spin inline-block mr-2">⟳</span>
                    📋 Clonar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Swal from 'sweetalert2';
import templatesApi from '@/api/academic/templates';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// =============================================
// STATE
// =============================================
const template = ref(null);
const summary = ref({
    total_semesters: 0,
    total_credits: 0,
    total_subjects: 0,
});
const loading = ref(true);
const cloning = ref(false);

// Modal clonar
const showCloneModal = ref(false);
const cloneForm = ref({
    id: null,
    name: '',
    code: '',
});

// =============================================
// COMPUTED - PERMISOS
// =============================================

const canEdit = computed(() => {
    // Admin puede editar todo
    if (authStore.hasRole('admin') || authStore.hasRole('super-admin')) {
        return true;
    }
    // Académico solo puede editar sus propias plantillas (no predeterminadas)
    if (authStore.hasRole('academico')) {
        return template.value?.created_by === authStore.user?.id && !template.value?.is_default;
    }
    return false;
});

// =============================================
// FUNCIONES
// =============================================

const loadTemplate = async () => {
    try {
        loading.value = true;
        const id = route.params.id;
        const response = await templatesApi.preview(id);
        
        template.value = response.data.template;
        summary.value = response.data.summary || {
            total_semesters: template.value?.semesters?.length || 0,
            total_credits: template.value?.total_credits || 0,
            total_subjects: template.value?.semesters?.reduce((acc, s) => acc + (s.subjects?.length || 0), 0) || 0,
        };
    } catch (error) {
        console.error('Error cargando plantilla:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la plantilla'
        }).then(() => {
            router.push('/admin/templates');
        });
    } finally {
        loading.value = false;
    }
};

const editTemplate = () => {
    router.push(`/admin/templates/${template.value.id}/edit`);
};

const useTemplate = () => {
    router.push(`/academic/careers/create-from-template?template_id=${template.value.id}`);
};

const cloneTemplate = () => {
    cloneForm.value = {
        id: template.value.id,
        name: `${template.value.name} (clonado)`,
        code: `${template.value.code}-CLONE`,
    };
    showCloneModal.value = true;
};

const closeCloneModal = () => {
    showCloneModal.value = false;
    cloneForm.value = {
        id: null,
        name: '',
        code: '',
    };
};

const executeClone = async () => {
    if (!cloneForm.value.name || !cloneForm.value.code) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Nombre y código son obligatorios'
        });
        return;
    }

    try {
        cloning.value = true;
        await templatesApi.clone(cloneForm.value.id, {
            name: cloneForm.value.name,
            code: cloneForm.value.code,
        });

        Swal.fire({
            icon: 'success',
            title: '✅ Plantilla clonada',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        closeCloneModal();
        await loadTemplate();
    } catch (error) {
        console.error('Error clonando plantilla:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'No se pudo clonar la plantilla'
        });
    } finally {
        cloning.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return 'No disponible';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    loadTemplate();
});
</script>