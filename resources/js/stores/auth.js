import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token') || null,
        initialized: false,
        pdfsAvailable: [],
    }),

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
        
        isSuperAdmin: (state) => {
            if (!state.user) return false;
            const roles = state.user.roles || [];
            return roles.includes('super-admin');
        },
        
        isAdmin: (state) => {
            if (!state.user) return false;
            const roles = state.user.roles || [];
            return roles.includes('super-admin') || roles.includes('admin');
        },
        
        hasRole: (state) => (role) => {
            if (!state.user) return false;
            const roles = state.user.roles || [];
            return roles.includes(role);
        },
        
        can: (state) => (permission) => {
            if (!state.user) return false;
            const permissions = state.user.permissions || [];
            return permissions.includes(permission);
        },

        primaryRole: (state) => {
            if (!state.user) return null;
            
            if (state.user.role_name) {
                return state.user.role_name;
            }
            
            const roles = state.user.roles || [];
            const priority = ['super-admin', 'admin', 'academico', 'docente', 'estudiante'];
            for (const role of priority) {
                if (roles.includes(role)) {
                    return role;
                }
            }
            return roles[0] || null;
        },

        hasPdfAvailable: (state) => {
            const role = state.primaryRole;
            if (!role) return false;
            return state.pdfsAvailable.includes(role);
        },

        primaryRoleId: (state) => {
            return state.user?.role_id || null;
        },
    },

    actions: {
        setAuth(data) {
            this.user = {
                ...data.user,
                two_factor_secret: data.user?.two_factor_secret || null,
                two_factor_confirmed_at: data.user?.two_factor_confirmed_at || null,
                roles: data.user.roles || [],
                permissions: data.user.permissions || [],
                role_id: data.user?.role_id || null,
                role_name: data.user?.role_name || null,
            };
            this.token = data.token;
            this.initialized = true;
            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('user', JSON.stringify(this.user));
            axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
            
            this.loadAvailablePdfs();
        },

        async login(email, password) {
            try {
                const response = await axios.post('/api/login', { email, password });
                if (response.data.requires_2fa) {
                    localStorage.setItem('2fa_temp_token', response.data.temp_token);
                    window.location.href = '/2fa/verify';
                    return { success: false, requires_2fa: true };
                }
                this.setAuth(response.data);
                return { success: true, data: response.data };
            } catch (error) {
                return { success: false, message: error.response?.data?.message || 'Error al iniciar sesión' };
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
            } finally {
                this.user = null;
                this.token = null;
                this.initialized = false;
                this.pdfsAvailable = [];
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                delete axios.defaults.headers.common['Authorization'];
                window.location.href = '/login';
            }
        },

        async fetchUser() {
            if (!this.token) return;
            try {
                const response = await axios.get('/api/user');
                this.user = {
                    ...response.data.user,
                    two_factor_secret: response.data.user?.two_factor_secret || null,
                    two_factor_confirmed_at: response.data.user?.two_factor_confirmed_at || null,
                    roles: response.data.user.roles || [],
                    permissions: response.data.user.permissions || [],
                    role_id: response.data.user?.role_id || null,
                    role_name: response.data.user?.role_name || null,
                };
                this.initialized = true;
                localStorage.setItem('user', JSON.stringify(this.user));
                
                await this.loadAvailablePdfs();
            } catch {
                this.logout();
            }
        },

        async loadAvailablePdfs() {
            try {
                if (!this.isAuthenticated) return;
                
                // ✅ Solo cargar si es admin o super-admin
                if (!this.isAdmin && !this.isSuperAdmin) {
                    console.log('⏳ Usuario no admin, omitiendo carga de PDFs');
                    this.pdfsAvailable = [];
                    return;
                }
                
                const response = await axios.get('/api/admin/manual-pdfs');
                const roles = response.data.roles || [];
                
                this.pdfsAvailable = roles
                    .filter(role => role.has_pdf)
                    .map(role => role.name);
                    
                console.log('📄 PDFs disponibles:', this.pdfsAvailable);
            } catch (error) {
                console.error('Error cargando PDFs disponibles:', error);
                this.pdfsAvailable = [];
            }
        },

        async initialize() {
            if (this.initialized) {
                console.log('✅ Auth store ya inicializado');
                return;
            }
            
            const token = localStorage.getItem('auth_token');
            const user = localStorage.getItem('user');
            
            if (token) {
                this.token = token;
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                if (user) {
                    try {
                        this.user = JSON.parse(user);
                        this.initialized = true;
                        console.log('✅ Usuario restaurado desde localStorage:', this.user);
                        
                        await this.loadAvailablePdfs();
                        return;
                    } catch (e) {
                        console.error('Error parsing user:', e);
                    }
                }
                
                await this.fetchUser();
            } else {
                console.log('⏳ No hay token, usuario no autenticado');
                this.initialized = true;
            }
        },
    },
});