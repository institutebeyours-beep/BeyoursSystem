<template>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Recuperar Contraseña</h1>
            <p class="subtitle">Te enviaremos un enlace para restablecer tu contraseña</p>

            <form @submit.prevent="handleForgotPassword">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        v-model="email" 
                        placeholder="tu@email.com"
                        required
                    >
                </div>

                <button type="submit" :disabled="loading" class="btn-submit">
                    {{ loading ? 'Enviando...' : 'Enviar enlace' }}
                </button>

                <p class="success" v-if="success">{{ success }}</p>
                <p class="error" v-if="error">{{ error }}</p>
            </form>

            <p class="back-link">
                <router-link to="/login">Volver al inicio de sesión</router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const email = ref('');
const loading = ref(false);
const success = ref('');
const error = ref('');

const handleForgotPassword = async () => {
    loading.value = true;
    success.value = '';
    error.value = '';

    try {
        const response = await axios.post('/api/password/forgot', {
            email: email.value
        });
        success.value = response.data.message;
    } catch (err) {
        error.value = err.response?.data?.message || 'Error al enviar el enlace';
    }

    loading.value = false;
};
</script>

<style scoped>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f7fafc;
}

.auth-card {
    background: white;
    padding: 3rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

h1 {
    color: #2d3748;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #718096;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

input:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.btn-submit {
    width: 100%;
    padding: 0.75rem;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-submit:hover:not(:disabled) {
    background: #3182ce;
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.success {
    color: #38a169;
    margin-top: 1rem;
    text-align: center;
}

.error {
    color: #e53e3e;
    margin-top: 1rem;
    text-align: center;
}

.back-link {
    margin-top: 1.5rem;
    text-align: center;
    color: #718096;
}

.back-link a {
    color: #4299e1;
    text-decoration: none;
    font-weight: 500;
}

.back-link a:hover {
    text-decoration: underline;
}
</style>