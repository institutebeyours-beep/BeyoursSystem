<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
        <form @submit.prevent="handleSubmit">
            <div class="space-y-4">
                <!-- Tipo de Enseñanza -->
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
                    <p v-if="errors.education_type_id" class="text-red-500 text-sm mt-1">{{ errors.education_type_id }}</p>
                </div>

                <!-- Nombre y Código -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre de la Carrera *
                        </label>
                        <input 
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Ej: Bachillerato en Ciencias"
                            required
                        />
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Código *
                        </label>
                        <input 
                            v-model="form.code"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Ej: BACH-C"
                            required
                        />
                        <p v-if="errors.code" class="text-red-500 text-sm mt-1">{{ errors.code }}</p>
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Descripción
                    </label>
                    <textarea 
                        v-model="form.description"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Descripción de la carrera..."
                    ></textarea>
                </div>

                <!-- Créditos y Horas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Créditos totales
                        </label>
                        <input 
                            v-model.number="form.total_credits"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="120"
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
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="2"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Horas Teóricas
                        </label>
                        <input 
                            v-model.number="form.theoretical_hours"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="800"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Horas Prácticas
                        </label>
                        <input 
                            v-model.number="form.practical_hours"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="400"
                        />
                    </div>
                </div>

                <!-- Semestres -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Número de Semestres
                    </label>
                    <input 
                        v-model.number="form.duration_semesters"
                        type="number"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="4"
                    />
                    <p class="text-xs text-gray-400 mt-1">Se calculará automáticamente si se deja en 0</p>
                </div>

                <!-- Activo -->
                <div>
                    <label class="flex items-center gap-2">
                        <input 
                            type="checkbox" 
                            v-model="form.is_active"
                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300">Activo</span>
                    </label>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    type="button"
                    @click="$emit('cancel')"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    :disabled="loading"
                    class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50 flex items-center gap-2"
                >
                    <span v-if="loading" class="animate-spin">⟳</span>
                    {{ isEditing ? '💾 Actualizar' : '💾 Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
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
        })
    },
    isEditing: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['submit', 'cancel']);
const authStore = useAuthStore();

const form = ref({
    education_type_id: props.initialData.education_type_id || '',
    name: props.initialData.name || '',
    code: props.initialData.code || '',
    description: props.initialData.description || '',
    total_credits: props.initialData.total_credits || 0,
    theoretical_hours: props.initialData.theoretical_hours || 0,
    practical_hours: props.initialData.practical_hours || 0,
    duration_years: props.initialData.duration_years || 0,
    duration_semesters: props.initialData.duration_semesters || 0,
    is_active: props.initialData.is_active ?? true,
});
const educationTypes = ref([]);

// =============================================
// CARGAR TIPOS DE ENSEÑANZA
// =============================================

const loadEducationTypes = async () => {
    try {
        // ✅ Endpoint público para todos los usuarios autenticados
        const response = await axios.get('/api/education-types/public');
        educationTypes.value = response.data.types || [];
        console.log('✅ Tipos de enseñanza cargados en Form:', educationTypes.value.length);
    } catch (error) {
        console.error('Error cargando tipos de enseñanza desde endpoint público:', error);
        educationTypes.value = [];
        
        // ✅ Fallback: si el usuario es admin, intentar con endpoint admin
        if (authStore.isAdmin || authStore.isSuperAdmin) {
            try {
                const response = await axios.get('/api/admin/education-types/all');
                educationTypes.value = response.data.types || [];
                console.log('✅ Tipos de enseñanza cargados desde admin:', educationTypes.value.length);
            } catch (e) {
                console.error('Error cargando tipos desde admin:', e);
            }
        }
    }
};

// =============================================
// WATCH
// =============================================

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
}, { deep: true });

// =============================================
// SUBMIT
// =============================================

const handleSubmit = () => {
    emit('submit', form.value);
};

// =============================================
// LIFECYCLE
// =============================================

onMounted(() => {
    loadEducationTypes();
});
</script>