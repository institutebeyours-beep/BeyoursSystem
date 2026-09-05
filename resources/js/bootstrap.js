import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Interceptor para manejar errores 401
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Redirigir al login si no está autenticado
            if (!window.location.pathname.includes('/login')) {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

console.log('✅ Axios configurado correctamente');