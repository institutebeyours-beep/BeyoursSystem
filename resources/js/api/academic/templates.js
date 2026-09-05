import axios from 'axios';

const API_URL = '/api/academic/templates';

export default {
    // Obtener todas las plantillas (paginado)
    getAll(params = {}) {
        return axios.get(API_URL, { params });
    },

    // Obtener todas las plantillas (sin paginación)
    getAllActive() {
        return axios.get(`${API_URL}/all`);
    },

    // Obtener una plantilla por ID
    getById(id) {
        return axios.get(`${API_URL}/${id}`);
    },

    // Obtener vista previa de una plantilla
    preview(id) {
        return axios.get(`${API_URL}/${id}/preview`);
    },

    // Crear plantilla
    create(data) {
        return axios.post(API_URL, data);
    },

    // Clonar plantilla
    clone(id, data) {
        return axios.post(`${API_URL}/${id}/clone`, data);
    },

    // Actualizar plantilla
    update(id, data) {
        return axios.put(`${API_URL}/${id}`, data);
    },

    // Eliminar plantilla
    delete(id) {
        return axios.delete(`${API_URL}/${id}`);
    }
};