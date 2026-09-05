import axios from 'axios';

const API_URL = '/api/academic/careers';

export default {
    // Obtener todas las carreras (paginado)
    getAll(params = {}) {
        return axios.get(API_URL, { params });
    },

    // Obtener todas las carreras (sin paginación)
    getAllActive() {
        return axios.get(`${API_URL}/all`);
    },

    // Obtener una carrera por ID
    getById(id) {
        return axios.get(`${API_URL}/${id}`);
    },

    // Crear carrera
    create(data) {
        return axios.post(API_URL, data);
    },

    // Crear carrera desde plantilla
    createFromTemplate(data) {
        return axios.post(`${API_URL}/create-from-template`, data);
    },

    // Actualizar carrera
    update(id, data) {
        return axios.put(`${API_URL}/${id}`, data);
    },

    // Eliminar carrera
    delete(id) {
        return axios.delete(`${API_URL}/${id}`);
    }
};