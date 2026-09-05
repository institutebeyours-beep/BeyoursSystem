import axios from 'axios';

const API_URL = '/api/admin/education-types';

export default {
    // Obtener todos los tipos (paginado)
    getAll(params = {}) {
        return axios.get(API_URL, { params });
    },

    // Obtener todos los tipos (sin paginación) - para selects
    getAllActive() {
        return axios.get(`${API_URL}/all`);
    },

    // Obtener un tipo por ID
    getById(id) {
        return axios.get(`${API_URL}/${id}`);
    },

    // Crear tipo
    create(data) {
        return axios.post(API_URL, data);
    },

    // Actualizar tipo
    update(id, data) {
        return axios.put(`${API_URL}/${id}`, data);
    },

    // Eliminar tipo
    delete(id) {
        return axios.delete(`${API_URL}/${id}`);
    }
};