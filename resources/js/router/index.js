import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';
import axios from 'axios';
import Dashboard from '@/views/Admin/Dashboard.vue';
import ManualPdfsIndex from '@/views/Admin/ManualPdfs/Index.vue';

// =============================================
// IMPORTACIONES DEL MÓDULO ADMINISTRATIVO
// =============================================
// Tipos de Enseñanza
import EducationTypesIndex from '@/views/Admin/EducationTypes/Index.vue';
import EducationTypesCreate from '@/views/Admin/EducationTypes/Create.vue';
import EducationTypesEdit from '@/views/Admin/EducationTypes/Edit.vue';

// Plantillas
import TemplatesIndex from '@/views/Admin/Templates/Index.vue';
import TemplatesCreate from '@/views/Admin/Templates/Create.vue';
import TemplatesEdit from '@/views/Admin/Templates/Edit.vue';
import TemplatesPreview from '@/views/Admin/Templates/Preview.vue';

// =============================================
// IMPORTACIONES DEL MÓDULO ACADÉMICO
// =============================================
import AcademicDashboard from '@/views/Academic/Dashboard.vue';

// Carreras
import CareersIndex from '@/views/Academic/Careers/Index.vue';
import CareersCreate from '@/views/Academic/Careers/Create.vue';
import CareersEdit from '@/views/Academic/Careers/Edit.vue';
import CareersShow from '@/views/Academic/Careers/Show.vue';
import CareersCreateFromTemplate from '@/views/Academic/Careers/CreateFromTemplate.vue';

// Cursos
import CoursesIndex from '@/views/Academic/Courses/Index.vue';
import CoursesCreate from '@/views/Academic/Courses/Create.vue';
import CoursesEdit from '@/views/Academic/Courses/Edit.vue';
import CoursesShow from '@/views/Academic/Courses/Show.vue';

// Estudiantes
import StudentsIndex from '@/views/Academic/Students/Index.vue';
import StudentsCreate from '@/views/Academic/Students/Create.vue';
import StudentsEdit from '@/views/Academic/Students/Edit.vue';
import StudentsShow from '@/views/Academic/Students/Show.vue';

// Asignaturas
import SubjectsIndex from '@/views/Academic/Subjects/Index.vue';

// Calificaciones - Configuraciones
import GradeConfigurationsIndex from '@/views/Academic/Grades/Configurations/Index.vue';

// Calificaciones - Registro
import GradeRegisterIndex from '@/views/Academic/Grades/Register/Index.vue';
import GradeRegisterCreate from '@/views/Academic/Grades/Register/Create.vue';

// Calificaciones - Reportes
import GradeReportsCourses from '@/views/Academic/Grades/Reports/Courses.vue';
import GradeReportsStudents from '@/views/Academic/Grades/Reports/Students.vue';

// Calificaciones - Tipos de Componente
import ComponentTypesIndex from '@/views/Academic/Grades/Configurations/ComponentTypes/Index.vue';
import ComponentTypesCreate from '@/views/Academic/Grades/Configurations/ComponentTypes/Create.vue';
import ComponentTypesEdit from '@/views/Academic/Grades/Configurations/ComponentTypes/Edit.vue';

// Manual de Usuario
import ManualIndex from '@/views/Manual/Index.vue';

// Manual PDF Viewer
import ManualPdfViewer from '@/views/ManualPdfViewer.vue';

// =============================================
// RUTAS
// =============================================
const routes = [
    // =============================================
    // RUTAS PÚBLICAS (sin autenticación)
    // =============================================
    { path: '/', redirect: '/login' },
    { path: '/login', name: 'login', component: () => import('@/views/Login.vue'), meta: { guest: true } },
    { path: '/register', name: 'register', component: () => import('@/views/Register.vue'), meta: { guest: true } },
    { path: '/password/forgot', name: 'password.forgot', component: () => import('@/views/ForgotPassword.vue'), meta: { guest: true } },
    { path: '/2fa/verify', name: '2fa.verify', component: () => import('@/views/TwoFactorLogin.vue'), meta: { guest: true } },

    // =============================================
    // RUTAS PRINCIPALES (requieren autenticación)
    // =============================================
    { 
        path: '/dashboard', 
        name: 'dashboard', 
        component: () => import('@/views/Dashboard.vue'), 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/profile', 
        name: 'profile', 
        component: () => import('@/views/Profile.vue'), 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/edit', 
        name: 'Edit', 
        component: () => import('@/views/Edit.vue'), 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/2fa/setup', 
        name: '2fa.setup', 
        component: () => import('@/components/TwoFactorSetup.vue'), 
        meta: { requiresAuth: true, skip2FA: true } 
    },
    { 
        path: '/settings', 
        name: 'settings', 
        component: () => import('@/views/Settings.vue'), 
        meta: { requiresAuth: true } 
    },

    // =============================================
    // MÓDULO ADMINISTRATIVO
    // =============================================
    
    // ADMIN - USUARIOS
    { 
        path: '/admin/users', 
        name: 'admin.users', 
        component: () => import('@/views/Admin/Users.vue'), 
        meta: { 
            requiresAuth: true, 
            permission: 'view_users' 
        } 
    },

    // ADMIN - ROLES
    { 
        path: '/admin/roles', 
        name: 'admin.roles', 
        component: () => import('@/views/Admin/Roles.vue'), 
        meta: { 
            requiresAuth: true, 
            permission: 'view_roles' 
        } 
    },

    // ADMIN - SETTINGS
    { 
        path: '/admin/settings', 
        name: 'admin.settings', 
        component: () => import('@/views/Admin/SettingsManager.vue'), 
        meta: { 
            requiresAuth: true, 
            roles: ['super-admin', 'admin'] 
        } 
    },
    { 
        path: '/admin/settings/global', 
        name: 'admin.settings.global', 
        component: () => import('@/views/Admin/SettingsGlobal.vue'), 
        meta: { 
            requiresAuth: true, 
            roles: ['super-admin', 'admin'] 
        } 
    },

    // ADMIN - DASHBOARD
    { 
        path: '/admin/dashboard', 
        name: 'admin.dashboard', 
        component: Dashboard, 
        meta: { 
            requiresAuth: true, 
            roles: ['super-admin'] 
        } 
    },

    // ADMIN - MANTENIMIENTO (SOLO SUPER-ADMIN)
    { 
        path: '/admin/maintenance', 
        name: 'admin.maintenance', 
        component: () => import('@/views/Admin/Maintenance.vue'), 
        meta: { 
            requiresAuth: true, 
            roles: ['super-admin']
        } 
    },

    // ADMIN - AUDITORÍA (SOLO SUPER-ADMIN)
    { 
        path: '/admin/audit', 
        name: 'admin.audit', 
        component: () => import('@/views/Admin/Audit.vue'), 
        meta: { 
            requiresAuth: true, 
            roles: ['super-admin'] 
        } 
    },

    // ADMIN - TIPOS DE ENSEÑANZA
    {
        path: '/admin/education-types',
        name: 'admin.education-types.index',
        component: EducationTypesIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin'] 
        }
    },
    {
        path: '/admin/education-types/create',
        name: 'admin.education-types.create',
        component: EducationTypesCreate,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin'] 
        }
    },
    {
        path: '/admin/education-types/:id/edit',
        name: 'admin.education-types.edit',
        component: EducationTypesEdit,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin'] 
        }
    },

    // ADMIN - PLANTILLAS
    {
        path: '/admin/templates',
        name: 'admin.templates.index',
        component: TemplatesIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin', 'academico'] 
        }
    },
    {
        path: '/admin/templates/create',
        name: 'admin.templates.create',
        component: TemplatesCreate,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin', 'academico'] 
        }
    },
    {
        path: '/admin/templates/:id/edit',
        name: 'admin.templates.edit',
        component: TemplatesEdit,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin', 'academico'] 
        }
    },
    {
        path: '/admin/templates/:id/preview',
        name: 'admin.templates.preview',
        component: TemplatesPreview,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin', 'academico'] 
        }
    },

    // ADMIN - PDFs DEL MANUAL
    {
        path: '/admin/manual-pdfs',
        name: 'admin.manual-pdfs',
        component: ManualPdfsIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['admin', 'super-admin'] 
        }
    },

    // MANUAL PDF VIEWER
    {
        path: '/manual-pdf/:roleId?',
        name: 'manual-pdf-viewer',
        component: ManualPdfViewer,
        meta: { requiresAuth: true }
    },

    // =============================================
    // MÓDULO ACADÉMICO
    // =============================================
    
    // 1. DASHBOARD ACADÉMICO
    { 
        path: '/academic/dashboard', 
        name: 'academic.dashboard', 
        component: AcademicDashboard, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },

    // 2. CARRERAS
    {
        path: '/academic/careers',
        name: 'academic.careers.index',
        component: CareersIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'admin', 'super-admin'] 
        }
    },
    {
        path: '/academic/careers/create',
        name: 'academic.careers.create',
        component: CareersCreate,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'admin', 'super-admin'] 
        }
    },
    {
        path: '/academic/careers/create-from-template',
        name: 'academic.careers.create-from-template',
        component: CareersCreateFromTemplate,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'admin', 'super-admin'] 
        }
    },
    {
        path: '/academic/careers/:id/edit',
        name: 'academic.careers.edit',
        component: CareersEdit,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'admin', 'super-admin'] 
        }
    },
    {
        path: '/academic/careers/:id',
        name: 'academic.careers.show',
        component: CareersShow,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'admin', 'super-admin'] 
        }
    },

    // 3. CURSOS
    { 
        path: '/academic/courses', 
        name: 'academic.courses.index', 
        component: CoursesIndex, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },
    { 
        path: '/academic/courses/create', 
        name: 'academic.courses.create', 
        component: CoursesCreate, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },
    { 
        path: '/academic/courses/:id/edit', 
        name: 'academic.courses.edit', 
        component: CoursesEdit, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },
    { 
        path: '/academic/courses/:id', 
        name: 'academic.courses.show', 
        component: CoursesShow, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },

    // 4. ESTUDIANTES
    { 
        path: '/academic/students', 
        name: 'academic.students.index', 
        component: StudentsIndex, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },
    { 
        path: '/academic/students/create', 
        name: 'academic.students.create', 
        component: StudentsCreate, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },
    { 
        path: '/academic/students/:id/edit', 
        name: 'academic.students.edit', 
        component: StudentsEdit, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },
    { 
        path: '/academic/students/:id', 
        name: 'academic.students.show', 
        component: StudentsShow, 
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        } 
    },

    // 5. ASIGNATURAS
    {
        path: '/academic/subjects',
        name: 'academic.subjects.index',
        component: SubjectsIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'] 
        }
    },

    // 6. CALIFICACIONES - CONFIGURACIONES
    {
        path: '/academic/grades/configurations',
        name: 'academic.grades.configurations',
        component: GradeConfigurationsIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_view'
        }
    },

    // 7. CALIFICACIONES - TIPOS DE COMPONENTE
    {
        path: '/academic/component-types',
        name: 'academic.component-types.index',
        component: ComponentTypesIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_manage'
        }
    },
    {
        path: '/academic/component-types/create',
        name: 'academic.component-types.create',
        component: ComponentTypesCreate,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_manage'
        }
    },
    {
        path: '/academic/component-types/:id/edit',
        name: 'academic.component-types.edit',
        component: ComponentTypesEdit,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_manage'
        }
    },

    // 8. CALIFICACIONES - REGISTRO
    {
        path: '/academic/grades',
        name: 'academic.grades.index',
        component: GradeRegisterIndex,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_view'
        }
    },
    {
        path: '/academic/grades/register',
        name: 'academic.grades.register',
        component: GradeRegisterCreate,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_manage'
        }
    },
    
    // 9. CALIFICACIONES - REPORTES
    {
        path: '/academic/grades/reports/courses',
        name: 'academic.grades.reports.courses',
        component: GradeReportsCourses,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_view'
        }
    },
    {
        path: '/academic/grades/reports/students',
        name: 'academic.grades.reports.students',
        component: GradeReportsStudents,
        meta: { 
            requiresAuth: true, 
            roles: ['academico', 'super-admin'],
            permission: 'academic_grades_view'
        }
    },

    // 10. MANUAL DE USUARIO
    {
        path: '/manual',
        name: 'manual.index',
        component: ManualIndex,
        meta: { 
            requiresAuth: true
        }
    },
];

// =============================================
// ROUTER INSTANCE
// =============================================
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// =============================================
// GUARD DE NAVEGACIÓN
// =============================================
router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('auth_token');
    const authStore = useAuthStore();
    const settingsStore = useSettingsStore();

    // Cargar settings
    if (!settingsStore.initialized) {
        await settingsStore.initialize();
    }

    // Restaurar sesión
    if (token && !authStore.user) {
        authStore.token = token;
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await authStore.fetchUser();
    }

    // ✅ SOLO VERIFICAR AUTENTICACIÓN
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
        return;
    }

    // ✅ SI ESTÁ AUTENTICADO Y VA A UNA RUTA PÚBLICA
    if (to.meta.guest && authStore.isAuthenticated) {
        next('/dashboard');
        return;
    }

    // ✅ PERMISOS
    if (to.meta.permission) {
        const permissions = authStore.user?.permissions || [];
        if (!permissions.includes(to.meta.permission)) {
            next('/dashboard');
            return;
        }
    }

    // ✅ ROLES
    if (to.meta.roles) {
        const userRoles = authStore.user?.roles || [];
        const hasRole = to.meta.roles.some((role) => userRoles.includes(role));
        if (!hasRole) {
            next('/dashboard');
            return;
        }
    }

    // ✅ ACCESO PERMITIDO
    next();
});

export default router;