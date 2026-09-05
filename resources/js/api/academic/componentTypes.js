import axios from 'axios';

const API_URL = '/api/academic/component-types';

export default {
    // Obtener todos los tipos (para admin)
    getAll() {
        return axios.get(`${API_URL}/all`);
    },

    // Obtener tipos activos (para selectores)
    getActive() {
        return axios.get(API_URL);
    },

    // ✅ Obtener un tipo por ID - CORREGIDO
    getById(id) {
        return axios.get(`${API_URL}/${id}`); // ← Quitar el /all
    },

    // Crear nuevo tipo
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