// ========================================== //
// 1. ESTILOS                                //
// ========================================== //
import '../css/app.css';

// ========================================== //
// 2. BOOTSTRAP (Axios, CSRF, etc.)          //
// ========================================== //
import './bootstrap';

// ========================================== //
// 3. DEPENDENCIAS DE VUE                    //
// ========================================== //
import { createApp } from 'vue';
import { createPinia } from 'pinia';

// ========================================== //
// 4. ROUTER Y LAYOUT                        //
// ========================================== //
import router from './router';
import App from './components/layouts/AppLayout.vue';

// ========================================== //
// 5. STORES                                 //
// ========================================== //
import { useAuthStore } from './stores/auth';

// ========================================== //
// 6. SWEETALERT2 (Alertas modernas)         //
// ========================================== //
import Swal from 'sweetalert2';
window.Swal = Swal;

// ========================================== //
// 7. CREAR APLICACIÓN VUE                   //
// ========================================== //
const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// ========================================== //
// 8. INICIALIZAR AUTENTICACIÓN              //
// ========================================== //
const authStore = useAuthStore();
authStore.initialize();

// ========================================== //
// 9. MONTAR APLICACIÓN                      //
// ========================================== //
app.mount('#app');

// ========================================== //
// 10. LOG DE INICIO                         //
// ========================================== //
console.log('✅ Beyours App iniciada correctamente');
console.log('🔍 Tailwind CSS:', document.querySelector('link[href*="app.css"]') ? '✅ Cargado' : '❌ No cargado');