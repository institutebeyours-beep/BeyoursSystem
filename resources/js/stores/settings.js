import { defineStore } from 'pinia';
import axios from 'axios';

export const useSettingsStore = defineStore('settings', {
    state: () => ({
        darkMode: localStorage.getItem('dark_mode') === 'true',
        sidebarImage: localStorage.getItem('sidebar_image') || null,
        settings: {},
        loading: false,
        initialized: false,
    }),

    getters: {
        isDarkMode: (state) => state.darkMode,
        getSidebarImage: (state) => state.sidebarImage,
        getSettings: (state) => state.settings,
        getSetting: (state) => (key, defaultValue = null) => {
            const setting = state.settings[key];
            if (setting && setting.value !== undefined && setting.value !== null && setting.value !== '') {
                // ✅ Si el tipo es boolean, convertir correctamente
                if (setting.type === 'boolean') {
                    return setting.value === '1' || setting.value === 1 || setting.value === true;
                }
                return setting.value;
            }
            return defaultValue;
        },
    },

    actions: {
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('dark_mode', String(this.darkMode));
            this.applyTheme();
        },

        applyTheme() {
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        setSidebarImage(imageUrl) {
            this.sidebarImage = imageUrl;
            localStorage.setItem('sidebar_image', imageUrl);
        },

        removeSidebarImage() {
            this.sidebarImage = null;
            localStorage.removeItem('sidebar_image');
        },

       async fetchSettings() {
    this.loading = true;
    try {
        const response = await axios.get('/api/settings/public');
        
        // ✅ Transformar la respuesta para que los settings tengan type
        const settingsData = {};
        for (const [key, value] of Object.entries(response.data)) {
            settingsData[key] = {
                value: value.value,
                type: value.type || 'string',
            };
        }
        this.settings = settingsData;
        
        console.log('✅ Configuraciones cargadas:', this.settings);
        console.log('🔍 2fa_required:', this.settings['2fa_required']); // ✅ VERIFICAR
        return this.settings;
    } catch (error) {
        console.error('❌ Error al cargar configuraciones:', error);
        return null;
    } finally {
        this.loading = false;
    }
},

        async updateSetting(key, value) {
            try {
                await axios.put(`/api/admin/settings/${key}`, { value });
                
                if (this.settings[key]) {
                    this.settings[key].value = value;
                } else {
                    this.settings[key] = { value: value };
                }
                
                this.settings = { ...this.settings };
                
                if (key === 'app_name') {
                    document.title = value;
                }
                
                console.log(`✅ Configuración ${key} actualizada a:`, value);
                return { success: true };
            } catch (error) {
                console.error('Error al actualizar:', error);
                return { 
                    success: false, 
                    error: error.response?.data?.message || 'Error al actualizar'
                };
            }
        },

        async initialize() {
            if (this.initialized) return;
            
            this.applyTheme();
            
            const savedImage = localStorage.getItem('sidebar_image');
            if (savedImage) {
                this.sidebarImage = savedImage;
            }
            
            await this.fetchSettings();
            
            this.initialized = true;
            console.log('✅ Settings store inicializado');
        },
    },
});