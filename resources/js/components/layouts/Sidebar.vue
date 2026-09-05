<template>
    <aside 
        class="fixed top-0 left-0 z-40 h-screen transition-all duration-300 border-r flex flex-col"
        :style="sidebarStyles"
        :class="[
            isOpen ? 'w-56' : 'w-14',
            isOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'
        ]"
    >
        <div class="h-full px-3 py-2 overflow-y-auto flex flex-col" :class="isOpen ? 'px-3' : 'px-2'">
            
            <!-- IMAGEN DEL SIDEBAR -->
            <div v-if="settings.sidebarImage" class="mb-2 rounded-xl overflow-hidden flex-shrink-0">
                <img 
                    :src="settings.sidebarImage" 
                    alt="Sidebar image" 
                    class="w-full object-cover transition-all duration-300"
                    :class="isOpen ? 'h-16' : 'h-8'"
                /> 
            </div>

            <!-- HEADER CON LOGO -->
            <div class="flex items-center justify-between mb-2" :class="isOpen ? '' : 'justify-center'">
                <router-link 
                    v-if="isOpen" 
                    to="/dashboard" 
                    class="text-sm font-bold transition whitespace-nowrap hover:opacity-80 truncate max-w-[120px]"
                    :style="{ color: sidebarTextColor }"
                >
                    {{ appName }}
                </router-link>
                <span v-else class="text-sm font-bold" :style="{ color: sidebarTextColor }">
                    {{ appName.charAt(0) || 'B' }}
                </span>

                <button 
                    @click="toggleSidebar" 
                    class="transition p-0.5 rounded-lg hover:bg-white/10 flex-shrink-0"
                    :style="{ color: sidebarTextColor }"
                    :title="isOpen ? 'Ocultar menú' : 'Mostrar menú'"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- ========================================== -->
            <!-- MENÚ DE NAVEGACIÓN                        -->
            <!-- ========================================== -->
            <ul class="space-y-0.5 font-medium flex-1">
                
                <!-- ========================================== -->
                <!-- SECCIÓN 1: DASHBOARD                      -->
                <!-- ========================================== -->
                <li>
                    <router-link 
                        to="/dashboard" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Dashboard' : 'Dashboard'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📊</span>
                        <span v-if="isOpen" class="flex-1">Dashboard</span>
                    </router-link>
                </li>

                <!-- ========================================== -->
                <!-- SECCIÓN 2: ADMINISTRACIÓN                  -->
                <!-- ========================================== -->
                <div v-if="isOpen && (authStore.isAdmin || authStore.isSuperAdmin)" 
                     class="text-[10px] uppercase text-gray-400 px-2 py-2 mt-2 border-t border-gray-700">
                    🔧 Administración
                </div>

                <!-- Usuarios -->
                <li v-if="authStore.can('view_users')">
                    <router-link 
                        to="/admin/users" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Usuarios' : 'Usuarios'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">👥</span>
                        <span v-if="isOpen" class="flex-1">Usuarios</span>
                    </router-link>
                </li>

                <!-- Roles -->
                <li v-if="authStore.can('view_roles')">
                    <router-link 
                        to="/admin/roles" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Roles' : 'Roles'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🔑</span>
                        <span v-if="isOpen" class="flex-1">Roles</span>
                    </router-link>
                </li>

                <!-- 2FA -->
                <li v-if="authStore.isAdmin || authStore.isSuperAdmin">
                    <router-link 
                        to="/2fa/setup" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Administrar 2FA' : '2FA'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🔐</span>
                        <span v-if="isOpen" class="flex-1">Administrar 2FA</span>
                    </router-link>
                </li>

                <!-- Configuración Global -->
                <li v-if="authStore.isAdmin || authStore.isSuperAdmin">
                    <router-link 
                        to="/admin/settings/global" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Configuración Global' : 'Configuración'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🌐</span>
                        <span v-if="isOpen" class="flex-1">Configuración Global</span>
                    </router-link>
                </li>

                <!-- ✅ TIPOS DE ENSEÑANZA (NUEVO) -->
                <li v-if="authStore.isAdmin || authStore.isSuperAdmin">
                    <router-link 
                        to="/admin/education-types" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Tipos de Enseñanza' : 'Enseñanza'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🏛️</span>
                        <span v-if="isOpen" class="flex-1">Tipos de Enseñanza</span>
                        <span v-if="isOpen" class="text-[8px] bg-indigo-500 text-white px-1.5 py-0.5 rounded-full">Admin</span>
                    </router-link>
                </li>

                <!-- ✅ PLANTILLAS (NUEVO) -->
                <li v-if="authStore.hasRole('academico') || authStore.isAdmin || authStore.isSuperAdmin">
                    <router-link 
                        to="/admin/templates" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Plantillas' : 'Plantillas'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📋</span>
                        <span v-if="isOpen" class="flex-1">Plantillas</span>
                        <span v-if="isOpen && !authStore.isAdmin && !authStore.isSuperAdmin" 
                              class="text-[8px] bg-purple-500 text-white px-1.5 py-0.5 rounded-full">
                            Personal
                        </span>
                    </router-link>
                </li>

                <!-- Gestión de PDFs -->
                <li v-if="authStore.isAdmin || authStore.isSuperAdmin">
                    <router-link 
                        to="/admin/manual-pdfs" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Gestión de PDFs' : 'PDFs'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📄</span>
                        <span v-if="isOpen" class="flex-1">Gestión de PDFs</span>
                        <span v-if="isOpen" class="text-[8px] bg-green-500 text-white px-1.5 py-0.5 rounded-full">Admin</span>
                    </router-link>
                </li>

                <!-- Mantenimiento (Super-Admin) -->
                <li v-if="authStore.isSuperAdmin">
                    <router-link 
                        to="/admin/maintenance" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Mantenimiento' : 'Mantenimiento'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🛠️</span>
                        <span v-if="isOpen" class="flex-1">Mantenimiento</span>
                        <span v-if="isOpen" class="text-[8px] bg-red-500 text-white px-1.5 py-0.5 rounded-full font-bold">Super</span>
                    </router-link>
                </li>

                <!-- Auditoría (Super-Admin) -->
                <li v-if="authStore.isSuperAdmin">
                    <router-link 
                        to="/admin/audit" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Auditoría' : 'Auditoría'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📋</span>
                        <span v-if="isOpen" class="flex-1">Auditoría</span>
                        <span v-if="isOpen" class="text-[8px] bg-purple-500 text-white px-1.5 py-0.5 rounded-full font-bold">Super</span>
                    </router-link>
                </li>

                <!-- ========================================== -->
                <!-- SECCIÓN 3: ACADÉMICO                       -->
                <!-- ========================================== -->
                <div v-if="isOpen && (authStore.hasRole('academico') || authStore.isSuperAdmin)" 
                     class="text-[10px] uppercase text-gray-400 px-2 py-2 mt-2 border-t border-gray-700">
                    📚 Académico
                </div>

                <!-- Dashboard Académico -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/dashboard"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Dashboard Académico' : 'Académico'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📊</span>
                        <span v-if="isOpen" class="flex-1">Dashboard Académico</span>
                    </router-link>
                </li>

                <!-- ✅ CARRERAS (NUEVO) -->
                <li v-if="authStore.hasRole('academico') || authStore.isAdmin || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/careers"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Carreras' : 'Carreras'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🎓</span>
                        <span v-if="isOpen" class="flex-1">Carreras</span>
                    </router-link>
                </li>

                <!-- Cursos -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/courses"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Cursos' : 'Cursos'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📚</span>
                        <span v-if="isOpen" class="flex-1">Cursos</span>
                    </router-link>
                </li>

                <!-- ✅ ASIGNATURAS (NUEVO) -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/subjects"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Asignaturas' : 'Asignaturas'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📖</span>
                        <span v-if="isOpen" class="flex-1">Asignaturas</span>
                    </router-link>
                </li>

                <!-- Estudiantes -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/students"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Estudiantes' : 'Estudiantes'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">👨‍🎓</span>
                        <span v-if="isOpen" class="flex-1">Estudiantes</span>
                    </router-link>
                </li>

                <!-- Configurar Notas -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/grades/configurations"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Configurar Notas' : 'Configurar'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">⚙️</span>
                        <span v-if="isOpen" class="flex-1">Configurar Notas</span>
                    </router-link>
                </li>

                <!-- Tipos de Componente -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/component-types"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Tipos de Componente' : 'Tipos'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🏷️</span>
                        <span v-if="isOpen" class="flex-1">Tipos de Componente</span>
                    </router-link>
                </li>

                <!-- Registrar Notas -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/grades"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Registrar Notas' : 'Notas'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📝</span>
                        <span v-if="isOpen" class="flex-1">Registrar Notas</span>
                    </router-link>
                </li>

                <!-- Reportes -->
                <li v-if="authStore.hasRole('academico') || authStore.isSuperAdmin">
                    <router-link 
                        to="/academic/grades/reports/courses"
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Reportes' : 'Reportes'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📊</span>
                        <span v-if="isOpen" class="flex-1">Reportes</span>
                    </router-link>
                </li>

                <!-- ========================================== -->
                <!-- SECCIÓN 4: MANUAL                          -->
                <!-- ========================================== -->
                <div v-if="isOpen && authStore.isAuthenticated" 
                     class="text-[10px] uppercase text-gray-400 px-2 py-2 mt-2 border-t border-gray-700">
                    📖 Documentación
                </div>

                <!-- Manual de Usuario (todos) -->
                <li>
                    <router-link 
                        to="/manual" 
                        class="flex items-center p-1.5 rounded-lg hover:bg-white/10 transition text-xs"
                        active-class="bg-white/20"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Manual de Usuario' : 'Manual'"
                        :style="{ color: sidebarTextColor }"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">📚</span>
                        <span v-if="isOpen" class="flex-1">Manual de Usuario</span>
                        <span 
                            v-if="isOpen" 
                            class="text-[8px] bg-purple-500 text-white px-1.5 py-0.5 rounded-full"
                        >
                            {{ manualStore.totalSections }}
                        </span>
                    </router-link>
                </li>

                <!-- VERSIÓN -->
                <li v-if="isOpen" class="text-center text-[10px] opacity-40 mt-2 pt-2 border-t" :style="{ color: sidebarTextColor, borderColor: sidebarBorderColor }">
                    v1.0.0
                </li>
            </ul>

            <!-- ========================================== -->
            <!-- PERFIL DE USUARIO (PARTE INFERIOR)        -->
            <!-- ========================================== -->
            <div class="border-t pt-2 mt-1" :style="{ borderColor: sidebarBorderColor }">
                <div class="relative">
                    <div 
                        class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/10 transition cursor-pointer"
                        :class="isOpen ? '' : 'justify-center'"
                        @click="toggleUserMenu"
                        :title="isOpen ? 'Opciones de perfil' : 'Perfil'"
                    >
                        <!-- Avatar -->
                        <div class="relative flex-shrink-0">
                            <img 
                                v-if="user?.profile_image" 
                                :src="getImageUrl(user.profile_image)" 
                                alt="Profile" 
                                class="w-7 h-7 rounded-full object-cover border border-white/20"
                            />
                            <div 
                                v-else 
                                class="w-7 h-7 rounded-full flex items-center justify-center text-white font-bold text-[10px] border border-white/20"
                                :style="{ background: avatarColor }"
                            >
                                {{ getInitials(user?.name) }}
                            </div>
                            <span class="absolute bottom-0 right-0 w-2 h-2 bg-green-500 border border-gray-900 rounded-full"></span>
                        </div>
                        
                        <div v-if="isOpen" class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ user?.name || 'Usuario' }}</p>
                            <p class="text-[9px] text-gray-400 truncate">{{ user?.email || 'Sin email' }}</p>
                        </div>

                        <svg 
                            v-if="isOpen" 
                            class="w-3 h-3 text-gray-400 transition-transform duration-200 flex-shrink-0"
                            :class="{ 'rotate-180': showUserMenu }"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>

                    <!-- Menú desplegable de usuario -->
                    <div 
                        v-if="showUserMenu && isOpen"
                        class="absolute bottom-full left-0 right-0 mb-1 bg-gray-800 border border-gray-700 rounded-lg shadow-xl overflow-hidden z-50"
                    >
                        <router-link 
                            to="/profile" 
                            class="flex items-center gap-2 px-3 py-2 hover:bg-white/10 transition text-xs text-gray-300 hover:text-white"
                            @click="showUserMenu = false"
                        >
                            <span>👤</span> Mi Perfil
                        </router-link>
                        <router-link 
                            to="/edit" 
                            class="flex items-center gap-2 px-3 py-2 hover:bg-white/10 transition text-xs text-gray-300 hover:text-white"
                            @click="showUserMenu = false"
                        >
                            <span>✏️</span> Editar Perfil
                        </router-link>
                        <router-link 
                            v-if="!has2FA" 
                            to="/2fa/setup" 
                            class="flex items-center gap-2 px-3 py-2 hover:bg-white/10 transition text-xs text-gray-300 hover:text-white"
                            @click="showUserMenu = false"
                        >
                            <span>🔐</span> Configurar 2FA
                        </router-link>
                    </div>

                    <button 
                        @click="handleLogout" 
                        class="flex items-center w-full mt-0.5 p-1.5 rounded-lg hover:bg-red-500/20 transition text-xs"
                        :class="isOpen ? '' : 'justify-center'"
                        :title="isOpen ? 'Cerrar Sesión' : 'Cerrar Sesión'"
                    >
                        <span class="text-base" :class="isOpen ? 'mr-2' : ''">🚪</span>
                        <span v-if="isOpen" class="flex-1 text-left text-red-400 hover:text-red-300">Cerrar Sesión</span>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- OVERLAY MÓVIL -->
    <div 
        v-if="isOpenMobile && windowWidth < 640" 
        @click="closeMobile"
        class="fixed inset-0 z-30 bg-black/50 sm:hidden"
    ></div>

    <button 
        v-if="!isOpenMobile && windowWidth < 640"
        @click="openMobile"
        class="fixed top-3 left-3 z-50 p-1.5 rounded-lg shadow-lg transition sm:hidden"
        :style="{ backgroundColor: sidebarBgColor, color: sidebarTextColor }"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';
import { useManualStore } from '@/stores/manual';

const router = useRouter();
const authStore = useAuthStore();
const settings = useSettingsStore();
const manualStore = useManualStore();

// =============================================
// ESTADO DEL SIDEBAR
// =============================================
const isOpen = ref(true);
const isOpenMobile = ref(false);
const windowWidth = ref(window.innerWidth);
const isReady = ref(false);
const showUserMenu = ref(false);

// =============================================
// FUNCIÓN PARA OBTENER URL DE IMAGEN
// =============================================
const getImageUrl = (path) => {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    if (path.startsWith('storage/')) return `/${path}`;
    const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin;
    return `${baseUrl}/storage/${path}`;
};

// =============================================
// COMPUTED
// =============================================
const user = computed(() => authStore.user);

const has2FA = computed(() => authStore.user?.two_factor_secret !== null);

const sidebarBgColor = computed(() => settings.getSetting('sidebar_color') || '#1a202c');

const sidebarTextColor = computed(() => settings.getSetting('sidebar_text_color') || '#ffffff');

const sidebarBorderColor = computed(() => {
    const bg = sidebarBgColor.value;
    return adjustColor(bg, -20);
});

const sidebarStyles = computed(() => ({
    backgroundColor: sidebarBgColor.value,
    color: sidebarTextColor.value,
    borderColor: sidebarBorderColor.value,
}));

const appName = computed(() => {
    try {
        return settings.getSetting('app_name') || 'Beyours';
    } catch {
        return 'Beyours';
    }
});

const avatarColor = computed(() => {
    const name = user.value?.name || 'Usuario';
    const colors = ['#4299e1', '#48bb78', '#ed8936', '#9f7aea', '#fc8181', '#68d391', '#63b3ed', '#f6ad55', '#4fd1c5', '#b794f4', '#f687b3', '#f6ad55'];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
});

// =============================================
// FUNCIONES
// =============================================
const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

function adjustColor(hex, percent) {
    if (!hex) return '#4a5568';
    try {
        const num = parseInt(hex.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = Math.max(0, Math.min(255, (num >> 16) + amt));
        const G = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amt));
        const B = Math.max(0, Math.min(255, (num & 0x0000FF) + amt));
        return `#${(1 << 24 | R << 16 | G << 8 | B).toString(16).slice(1)}`;
    } catch {
        return '#4a5568';
    }
}

// =============================================
// FUNCIONES DEL SIDEBAR
// =============================================
const toggleSidebar = () => {
    isOpen.value = !isOpen.value;
    if (showUserMenu.value) showUserMenu.value = false;
};

const toggleUserMenu = () => {
    if (isOpen.value) {
        showUserMenu.value = !showUserMenu.value;
    }
};

const openMobile = () => {
    isOpenMobile.value = true;
    showUserMenu.value = false;
};

const closeMobile = () => {
    isOpenMobile.value = false;
};

const handleLogout = async () => {
    showUserMenu.value = false;
    await authStore.logout();
    isOpen.value = true;
    isOpenMobile.value = false;
    router.push('/login');
};

const handleResize = () => {
    windowWidth.value = window.innerWidth;
    if (windowWidth.value >= 640) {
        isOpenMobile.value = false;
    }
};

watch(isOpenMobile, (newVal) => {
    if (windowWidth.value < 640) {
        isOpen.value = newVal;
    }
});

const handleClickOutside = (event) => {
    const userMenu = document.querySelector('.relative');
    if (userMenu && !userMenu.contains(event.target)) {
        showUserMenu.value = false;
    }
};

// =============================================
// LIFECYCLE
// =============================================
onMounted(() => {
    handleResize();
    window.addEventListener('resize', handleResize);
    document.addEventListener('click', handleClickOutside);
    settings.initialize();
    isReady.value = true;
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
::-webkit-scrollbar {
    width: 3px;
}
::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 2px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25);
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 250ms;
}

aside {
    transition-duration: 250ms;
}
</style>