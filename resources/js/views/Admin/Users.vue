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
                            👥 Gestión de Usuarios
                        </h1>
                        <p class="text-gray-300 text-xs sm:text-sm font-medium mt-0.5 sm:mt-1">
                            Administra los usuarios del sistema
                        </p>
                    </div>
                    <button @click="openCreateModal" class="px-4 py-2 sm:px-6 sm:py-2.5 bg-white/20 hover:bg-white/30 rounded-xl font-bold text-sm sm:text-base transition-all hover:scale-105 backdrop-blur-sm flex items-center gap-2">
                        <span>➕</span> Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ALERTAS                                   -->
            <!-- ========================================== -->
            <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                <div v-if="users.length === 0 && !loading" class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 p-3 sm:p-4 bg-amber-500/10 border-l-4 border-amber-500 rounded-xl backdrop-blur-sm">
                    <span class="text-2xl sm:text-3xl">⚠️</span>
                    <div class="flex-1 w-full sm:w-auto">
                        <p class="font-bold text-sm sm:text-base text-gray-800 dark:text-white">No hay usuarios registrados</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Comienza creando tu primer usuario.</p>
                    </div>
                    <button @click="openCreateModal" class="px-4 py-1.5 sm:px-4 sm:py-2 bg-amber-500 text-white rounded-full text-[10px] sm:text-xs font-bold hover:bg-amber-600 transition-all hover:scale-105 whitespace-nowrap">
                        Crear Usuario
                    </button>
                </div>

                <div v-if="isEditing" class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 p-3 sm:p-4 bg-blue-500/10 border-l-4 border-blue-500 rounded-xl backdrop-blur-sm">
                    <span class="text-2xl sm:text-3xl">✏️</span>
                    <div class="flex-1 w-full sm:w-auto">
                        <p class="font-bold text-sm sm:text-base text-gray-800 dark:text-white">Editando Usuario</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Modifica solo los campos que necesites cambiar.</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- FILTROS                                   -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">🔍 Buscar</label>
                        <input 
                            type="text" 
                            v-model="filters.search" 
                            placeholder="Nombre o email..."
                            @input="loadUsers"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">🎯 Rol</label>
                        <select v-model="filters.role" @change="loadUsers" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">📌 Estado</label>
                        <select v-model="filters.is_active" @change="loadUsers" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="1">✅ Activos</option>
                            <option value="0">❌ Inactivos</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button @click="resetFilters" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition whitespace-nowrap">
                            🔄 Limpiar
                        </button>
                        <span class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg text-sm font-bold whitespace-nowrap">
                            {{ users.length }} usuario{{ users.length !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TABLA DE USUARIOS                         -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuario</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Contacto</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rol</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Estado</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Último acceso</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="loading">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-indigo-500 border-t-transparent"></div>
                                    <span class="ml-2">Cargando usuarios...</span>
                                </td>
                            </tr>
                            <tr v-else-if="!users || users.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <div class="text-4xl mb-2">📭</div>
                                    <p class="text-gray-500 dark:text-gray-400">No hay usuarios registrados</p>
                                    <button @click="openCreateModal" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                                        Crear primer usuario
                                    </button>
                                </td>
                            </tr>
                            <tr v-else v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-3 sm:px-4 py-3">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-bold text-xs sm:text-sm flex-shrink-0" :style="{ background: getAvatarColor(user.name) }">
                                            {{ getInitials(user.name) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-white text-sm sm:text-base">{{ user.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 py-3 hidden sm:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <p v-if="user.phone" class="text-xs">📞 {{ user.phone }}</p>
                                        <p v-if="user.cellphone" class="text-xs">📱 {{ user.cellphone }}</p>
                                        <p v-if="!user.phone && !user.cellphone" class="text-xs text-gray-400">Sin contacto</p>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold" :class="getRoleClass(user.roles)">
                                        {{ getRoleName(user.roles) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 hidden md:table-cell">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold" :class="user.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'">
                                        {{ user.is_active ? '✅ Activo' : '❌ Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 hidden lg:table-cell">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(user.last_login_at) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3">
                                    <div class="flex items-center justify-center gap-1 sm:gap-2">
                                        <button @click="openEditModal(user)" class="p-1.5 sm:p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-900/50 transition" title="Editar">
                                            ✏️
                                        </button>
                                        <button @click="deleteUser(user)" class="p-1.5 sm:p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Eliminar">
                                            🗑️
                                        </button>
                                        <button @click="openChangePasswordModal(user)" class="p-1.5 sm:p-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition" title="Cambiar contraseña">
                                            🔑
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL CREAR / EDITAR USUARIO               -->
        <!-- ========================================== -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="closeModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
                            {{ isEditing ? '✏️ Editar Usuario' : '➕ Nuevo Usuario' }}
                        </h2>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl">×</button>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <div v-if="isEditing" class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl mb-4">
                        <span class="text-xl">ℹ️</span>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Modifica solo los campos que necesites cambiar.</p>
                    </div>

                    <form @submit.prevent="saveUser">
                        <!-- DATOS PERSONALES -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">👤 Datos Personales</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre <span class="text-red-500">*</span></label>
                                    <input type="text" v-model="form.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ej: Gonzalo">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellido Paterno</label>
                                    <input type="text" v-model="form.lastname" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ej: Pérez">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellido Materno</label>
                                    <input type="text" v-model="form.second_lastname" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ej: Rueda">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Nacimiento</label>
                                    <input type="date" v-model="form.birth_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección</label>
                                    <input type="text" v-model="form.address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Dirección completa">
                                </div>
                            </div>
                        </div>

                        <!-- DATOS DE CONTACTO -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">📱 Contacto</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" v-model="form.email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="ejemplo@correo.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                                    <input type="text" v-model="form.phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="(123) 456-7890">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Celular</label>
                                <input type="text" v-model="form.cellphone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="(123) 456-7890">
                            </div>
                        </div>

                        <!-- DATOS DE ACCESO -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">🔐 Datos de Acceso</h4>
                            <div v-if="!isEditing" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña <span class="text-red-500">*</span></label>
                                    <input type="password" v-model="form.password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Mínimo 8 caracteres">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar <span class="text-red-500">*</span></label>
                                    <input type="password" v-model="form.password_confirmation" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Repite la contraseña">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rol <span class="text-red-500">*</span></label>
                                    <select v-model="form.role" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        <option value="">Seleccionar rol</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="flex items-center pt-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" v-model="form.is_active" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">✅ Usuario activo</span>
                                    </label>
                                </div>
                            </div>
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

        <!-- ========================================== -->
        <!-- MODAL CAMBIAR CONTRASEÑA                   -->
        <!-- ========================================== -->
        <div v-if="showPasswordModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="closePasswordModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">🔑 Cambiar Contraseña</h2>
                        <button @click="closePasswordModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl">×</button>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl mb-4">
                        <span class="text-xl">ℹ️</span>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Cambiando contraseña para <strong>{{ passwordUser?.name }}</strong></p>
                    </div>

                    <form @submit.prevent="changePassword">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva Contraseña <span class="text-red-500">*</span></label>
                                <input type="password" v-model="passwordForm.password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Mínimo 8 caracteres">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar <span class="text-red-500">*</span></label>
                                <input type="password" v-model="passwordForm.password_confirmation" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Repite la contraseña">
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="closePasswordModal" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition disabled:opacity-50" :disabled="saving">
                                {{ saving ? '⏳ Guardando...' : '🔑 Cambiar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
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
const users = ref([]);
const roles = ref([]);
const loading = ref(true);
const saving = ref(false);

const filters = reactive({
    search: '',
    role: '',
    is_active: ''
});

const showModal = ref(false);
const isEditing = ref(false);
const showPasswordModal = ref(false);
const passwordUser = ref(null);

const form = reactive({
    id: null,
    name: '',
    lastname: '',
    second_lastname: '',
    email: '',
    phone: '',
    cellphone: '',
    birth_date: '',
    address: '',
    password: '',
    password_confirmation: '',
    role: '',
    is_active: true
});

const passwordForm = reactive({
    password: '',
    password_confirmation: ''
});

// =============================================
// FUNCIONES AUXILIARES
// =============================================
const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const getAvatarColor = (name) => {
    const colors = [
        '#4299e1', '#48bb78', '#ed8936', '#9f7aea',
        '#fc8181', '#68d391', '#63b3ed', '#f6ad55'
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
};

const getRoleName = (roles) => {
    if (!roles) return 'Sin rol';
    if (Array.isArray(roles)) {
        if (roles.length === 0) return 'Sin rol';
        if (typeof roles[0] === 'object' && roles[0] !== null) {
            return roles.map(r => r.name || 'Sin nombre').join(', ');
        }
        if (typeof roles[0] === 'string') {
            return roles.join(', ');
        }
        return 'Sin rol';
    }
    if (typeof roles === 'object' && roles !== null) {
        const values = Object.values(roles);
        if (values.length === 0) return 'Sin rol';
        if (typeof values[0] === 'object' && values[0] !== null) {
            return values.map(r => r.name || 'Sin nombre').join(', ');
        }
        return values.join(', ');
    }
    return String(roles) || 'Sin rol';
};

const getRoleClass = (roles) => {
    const role = getRoleName(roles);
    if (role.includes('super-admin')) return 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400';
    if (role.includes('admin')) return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
    if (role.includes('manager')) return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
    return 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
};

const formatDate = (date) => {
    if (!date) return 'Nunca';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// =============================================
// CRUD
// =============================================
const loadUsers = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        if (filters.search) params.append('search', filters.search);
        if (filters.role) params.append('role', filters.role);
        if (filters.is_active !== '') params.append('is_active', filters.is_active);
        
        const response = await axios.get('/api/admin/users?' + params.toString());
        
        if (response.data.users?.data) {
            users.value = response.data.users.data;
        } else if (Array.isArray(response.data.users)) {
            users.value = response.data.users;
        } else {
            users.value = [];
        }
        
        roles.value = response.data.roles || [];
    } catch (error) {
        console.error('❌ Error:', error);
        users.value = [];
    }
    loading.value = false;
};

const saveUser = async () => {
    saving.value = true;
    try {
        const data = {
            name: form.name,
            lastname: form.lastname,
            second_lastname: form.second_lastname,
            email: form.email,
            phone: form.phone,
            cellphone: form.cellphone,
            birth_date: form.birth_date,
            address: form.address,
            role: form.role,
            is_active: form.is_active ? 1 : 0,
        };
        
        if (!isEditing.value) {
            data.password = form.password;
            data.password_confirmation = form.password_confirmation;
        }
        
        if (isEditing.value) {
            await axios.put(`/api/admin/users/${form.id}`, data);
            Swal.fire({
                icon: 'success',
                title: '✅ Usuario actualizado',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            await axios.post('/api/admin/users', data);
            Swal.fire({
                icon: 'success',
                title: '✅ Usuario creado',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
        closeModal();
        loadUsers();
    } catch (error) {
        console.error('❌ Error:', error);
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            const messages = Object.values(errors).flat().join('\n');
            Swal.fire({
                icon: 'error',
                title: '❌ Error de validación',
                text: messages,
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'Error al guardar',
            });
        }
    }
    saving.value = false;
};

const deleteUser = async (user) => {
    const result = await Swal.fire({
        title: '¿Eliminar usuario?',
        text: `¿Estás seguro de eliminar a "${user.name}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });
    
    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/admin/users/${user.id}`);
            Swal.fire({
                icon: 'success',
                title: '✅ Usuario eliminado',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            loadUsers();
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'Error al eliminar',
            });
        }
    }
};

const changePassword = async () => {
    if (!passwordForm.password || passwordForm.password.length < 8) {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña muy corta',
            text: 'La contraseña debe tener al menos 8 caracteres',
        });
        return;
    }
    
    if (passwordForm.password !== passwordForm.password_confirmation) {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseñas no coinciden',
            text: 'Las contraseñas no coinciden',
        });
        return;
    }
    
    saving.value = true;
    try {
        await axios.post(`/api/admin/users/${passwordUser.value.id}/password`, {
            password: passwordForm.password,
            password_confirmation: passwordForm.password_confirmation
        });
        Swal.fire({
            icon: 'success',
            title: '✅ Contraseña actualizada',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        closePasswordModal();
    } catch (error) {
        console.error('❌ Error:', error);
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            const messages = Object.values(errors).flat().join('\n');
            Swal.fire({
                icon: 'error',
                title: '❌ Error de validación',
                text: messages,
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: error.response?.data?.message || 'Error al cambiar contraseña',
            });
        }
    }
    saving.value = false;
};

const resetFilters = () => {
    filters.search = '';
    filters.role = '';
    filters.is_active = '';
    loadUsers();
};

// =============================================
// MODALES
// =============================================
const openCreateModal = () => {
    isEditing.value = false;
    form.id = null;
    form.name = '';
    form.lastname = '';
    form.second_lastname = '';
    form.email = '';
    form.phone = '';
    form.cellphone = '';
    form.birth_date = '';
    form.address = '';
    form.password = '';
    form.password_confirmation = '';
    form.role = roles.value.length > 0 ? roles.value[0].name : '';
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (user) => {
    isEditing.value = true;
    form.id = user.id;
    form.name = user.name || '';
    form.lastname = user.lastname || '';
    form.second_lastname = user.second_lastname || '';
    form.email = user.email || '';
    form.phone = user.phone || '';
    form.cellphone = user.cellphone || '';
    form.birth_date = user.birth_date || '';
    form.address = user.address || '';
    form.role = getRoleName(user.roles);
    form.is_active = user.is_active;
    showModal.value = true;
};

const openChangePasswordModal = (user) => {
    passwordUser.value = user;
    passwordForm.password = '';
    passwordForm.password_confirmation = '';
    showPasswordModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const closePasswordModal = () => {
    showPasswordModal.value = false;
    passwordUser.value = null;
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(loadUsers);
</script>