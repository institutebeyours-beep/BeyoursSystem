import { createPinia } from 'pinia';

// =============================================
// IMPORTAR TODOS LOS STORES
// =============================================
import { useAuthStore } from './auth';
import { useSettingsStore } from './settings';
import { useManualStore } from './manual';

// =============================================
// EXPORTAR STORES PARA USO EN OTROS ARCHIVOS
// =============================================
export {
    useAuthStore,
    useSettingsStore,
    useManualStore
};

// =============================================
// CREAR Y CONFIGURAR PINIA
// =============================================
const pinia = createPinia();

export default pinia;