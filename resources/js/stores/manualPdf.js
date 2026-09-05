import { defineStore } from 'pinia';
import axios from 'axios';

export const useManualPdfStore = defineStore('manualPdf', {
    state: () => ({
        manual: null,
        loading: false,
        error: null,
    }),

    getters: {
        hasManual: (state) => state.manual !== null && state.manual !== undefined,
        manualVersion: (state) => state.manual?.version || null,
        isManualActive: (state) => state.manual?.is_active === true || state.manual?.is_active === 1,
        manualUrl: (state) => {
            if (!state.manual?.file_path) return null;
            let path = state.manual.file_path;
            path = path.replace(/^(public\/|storage\/)/, '');
            const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin;
            return `${baseUrl}/storage/${path}`;
        }
    },

    actions: {
        async fetchManualByRoleId(roleId) {
            this.loading = true;
            this.error = null;
            this.manual = null;
            
            try {
                console.log('🔍 Buscando manual para role_id:', roleId);
                const response = await axios.get(`/api/manual/pdf/${roleId}`);
                
                if (response.data && response.data.id) {
                    this.manual = response.data;
                    console.log('✅ Manual encontrado:', this.manual.file_name);
                }
                return response.data;
            } catch (error) {
                console.error('❌ Error:', error);
                this.manual = null;
                throw error;
            } finally {
                this.loading = false;
            }
        }
    }
});