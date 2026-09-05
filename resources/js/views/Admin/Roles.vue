<template>
    <div class="min-h-screen" :style="{ backgroundColor: bgColor }">
        <div class="container mx-auto px-3 sm:px-4 md:px-6 py-4 sm:py-6 max-w-7xl">
            <!-- ========================================== -->
            <!-- HEADER                                    -->
            <!-- ========================================== -->
            <div class="rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 mb-4 sm:mb-6 md:mb-8 text-white shadow-2xl" :style="{ background: headerGradient }">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
                    <div>
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-black leading-tight">
                            🔑 Gestión de Roles
                        </h1>
                        <p class="text-gray-300 text-xs sm:text-sm font-medium mt-0.5 sm:mt-1">
                            Administra los roles y permisos del sistema
                        </p>
                    </div>
                    <button @click="openCreateModal" class="px-4 py-2 sm:px-6 sm:py-2.5 bg-white/20 hover:bg-white/30 rounded-xl font-bold text-sm sm:text-base transition-all hover:scale-105 backdrop-blur-sm flex items-center gap-2">
                        <span>➕</span> Nuevo Rol
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ALERTA DE ROLES PROTEGIDOS                -->
            <!-- ========================================== -->
            <div class="mb-4 sm:mb-6">
                <div class="flex items-center gap-3 p-3 sm:p-4 bg-amber-500/10 border-l-4 border-amber-500 rounded-xl">
                    <span class="text-2xl">⚠️</span>
                    <div>
                        <p class="font-bold text-sm text-gray-800 dark:text-white">Roles Protegidos</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Los roles "super-admin" y "admin" no se pueden eliminar.</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- LISTA DE ROLES                            -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rol</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permisos</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Usuarios</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="roles.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center">
                                    <div class="text-4xl mb-2">📭</div>
                                    <p class="text-gray-500 dark:text-gray-400">No hay roles registrados</p>
                                    <button @click="openCreateModal" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                                        Crear primer rol
                                    </button>
                                </td>
                            </tr>
                            <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-3 sm:px-4 py-3">
                                    <span class="px-3 py-1.5 rounded-full text-xs font-bold" :class="getRoleBadgeClass(role.name)">
                                        {{ role.name }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="perm in role.permissions" :key="perm.id" class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-[10px] font-medium">
                                            {{ perm.name }}
                                        </span>
                                        <span v-if="!role.permissions || role.permissions.length === 0" class="text-xs text-gray-400 dark:text-gray-500">
                                            Sin permisos
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 py-3 text-center hidden sm:table-cell">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-bold">
                                        {{ role.users_count || 0 }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3">
                                    <div class="flex items-center justify-center gap-1 sm:gap-2">
                                        <button @click="editRole(role)" class="p-1.5 sm:p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-900/50 transition" title="Editar">
                                            ✏️
                                        </button>
                                        <button 
                                            @click="deleteRole(role)" 
                                            class="p-1.5 sm:p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition disabled:opacity-50 disabled:cursor-not-allowed" 
                                            title="Eliminar"
                                            :disabled="isProtectedRole(role.name)"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CONTADOR DE ROLES                         -->
            <!-- ========================================== -->
            <div class="mt-4 text-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Total: <strong class="text-gray-700 dark:text-white">{{ roles.length }}</strong> roles
                </span>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL CREAR / EDITAR ROL                   -->
        <!-- ========================================== -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="closeModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
                            {{ isEditing ? '✏️ Editar Rol' : '➕ Nuevo Rol' }}
                        </h2>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl">×</button>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <form @submit.prevent="saveRole">
                        <!-- NOMBRE DEL ROL -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre del Rol <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                v-model="form.name" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Ej: editor, viewer, manager"
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Usa minúsculas y guiones para separar palabras.</p>
                        </div>

                        <!-- PERMISOS -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Permisos
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <label v-for="perm in allPermissions" :key="perm.id" class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        :value="perm.name" 
                                        v-model="form.permissions"
                                        class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                    >
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ perm.name }}</span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Selecciona los permisos que tendrá este rol.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition disabled:opacity-50" :disabled="saving">
                                {{ saving ? '⏳ Guardando...' : '💾 Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';

const authStore = useAuthStore();
const settingsStore = useSettingsStore();

// =============================================
// COMPUTED
// =============================================
const bgColor = computed(() => {
    return settingsStore.getSetting('background_color') || '#f3f4f6';
});

const headerGradient = computed(() => {
    const primaryColor = settingsStore.getSetting('primary_color') || '#6366f1';
    const secondaryColor = settingsStore.getSetting('secondary_color') || '#8b5cf6';
    return `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)`;
});

// =============================================
// STATE
// =============================================
const roles = ref([]);
const allPermissions = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const saving = ref(false);

const form = ref({
    id: null,
    name: '',
    permissions: [],
});

const protectedRoles = ['super-admin', 'admin'];

// =============================================
// FUNCIONES
// =============================================
const getRoleBadgeClass = (roleName) => {
    const classes = {
        'super-admin': 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
        'admin': 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
        'manager': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
        'editor': 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
        'viewer': 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    };
    return classes[roleName] || 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
};

const isProtectedRole = (roleName) => {
    return protectedRoles.includes(roleName);
};

// =============================================
// CRUD
// =============================================
const loadRoles = async () => {
    try {
        const response = await axios.get('/api/admin/roles');
        roles.value = response.data.roles || [];
        allPermissions.value = response.data.permissions || [];
    } catch (error) {
        console.error('❌ Error al cargar roles:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: 'No se pudieron cargar los roles',
        });
    }
};

const saveRole = async () => {
    saving.value = true;
    try {
        if (isEditing.value) {
            await axios.put(`/api/admin/roles/${form.value.id}`, form.value);
            Swal.fire({
                icon: 'success',
                title: '✅ Rol actualizado',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            await axios.post('/api/admin/roles', form.value);
            Swal.fire({
                icon: 'success',
                title: '✅ Rol creado',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
        closeModal();
        loadRoles();
    } catch (error) {
        console.error('❌ Error:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.response?.data?.message || 'Error al guardar el rol',
        });
    }
    saving.value = false;
};

const editRole = (role) => {
    form.value = {
        id: role.id,
        name: role.name,
        permissions: role.permissions.map(p => p.name),
    };
    isEditing.value = true;
    showModal.value = true;
};

const deleteRole = async (role) => {
    // Verificar si es un rol protegido
    if (isProtectedRole(role.name)) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Rol Protegido',
            text: `El rol "${role.name}" no se puede eliminar porque es un rol del sistema.`,
            confirmButtonColor: '#6b7280',
        });
        return;
    }

    const result = await Swal.fire({
        title: '¿Eliminar rol?',
        text: `¿Estás seguro de eliminar el rol "${role.name}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });
    
    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/admin/roles/${role.id}`);
            Swal.fire({
                icon: 'success',
                title: '✅ Rol eliminado',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            loadRoles();
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'Error al eliminar el rol',
            });
        }
    }
};

const openCreateModal = () => {
    form.value = {
        id: null,
        name: '',
        permissions: [],
    };
    isEditing.value = false;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.value = {
        id: null,
        name: '',
        permissions: [],
    };
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(loadRoles);
</script>