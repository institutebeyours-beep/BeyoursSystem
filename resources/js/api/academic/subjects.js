import axios from 'axios';

const API_URL = '/api/academic/subjects';

export default {
    // Obtener todas las asignaturas (paginado)
    getAll(params = {}) {
        return axios.get(API_URL, { params });
    },

    // Obtener todas las asignaturas (sin paginación)
    getAllActive() {
        return axios.get(`${API_URL}/all`);
    },

    // Obtener una asignatura por ID
    getById(id) {
        return axios.get(`${API_URL}/${id}`);
    },

    // Crear asignatura
    create(data) {
        return axios.post(API_URL, data);
    },

    // Actualizar asignatura
    update(id, data) {
        return axios.put(`${API_URL}/${id}`, data);
    },

    // Eliminar asignatura
    delete(id) {
        return axios.delete(`${API_URL}/${id}`);
    }
};