<template>
    <div class="register-container">
        <div class="register-card">
            <h1>Crear cuenta</h1>
            <p class="subtitle">Regístrate en Beyours</p>

            <form @submit.prevent="handleRegister">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input 
                        type="text" 
                        id="name" 
                        v-model="form.name" 
                        placeholder="Tu nombre"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        v-model="form.email" 
                        placeholder="tu@email.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        v-model="form.password" 
                        placeholder="********"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        v-model="form.password_confirmation" 
                        placeholder="********"
                        required
                    >
                </div>

                <button type="submit" :disabled="loading" class="btn-register">
                    {{ loading ? 'Cargando...' : 'Registrarse' }}
                </button>

                <div v-if="errors" class="errors">
                    <p v-for="(error, key) in errors" :key="key" class="error">
                        {{ error[0] }}
                    </p>
                </div>
            </form>

            <p class="login-link">
                ¿Ya tienes cuenta? <router-link to="/login">Inicia sesión</router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const loading = ref(false);
const errors = ref(null);

const handleRegister = async () => {
    loading.value = true;
    errors.value = null;

    const result = await authStore.register(
        form.name,
        form.email,
        form.password,
        form.password_confirmation
    );

    if (result.success) {
        router.push('/dashboard');
    } else {
        errors.value = result.errors;
    }

    loading.value = false;
};
</script>

<style scoped>
.register-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f7fafc;
}

.register-card {
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
    border-color: #48bb78;
    box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.1);
}

.btn-register {
    width: 100%;
    padding: 0.75rem;
    background: #48bb78;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-register:hover:not(:disabled) {
    background: #38a169;
}

.btn-register:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.errors {
    margin-top: 1rem;
}

.error {
    color: #e53e3e;
    text-align: center;
    margin: 0.5rem 0;
}

.login-link {
    margin-top: 1.5rem;
    text-align: center;
    color: #718096;
}

.login-link a {
    color: #48bb78;
    text-decoration: none;
    font-weight: 500;
}

.login-link a:hover {
    text-decoration: underline;
}
</style>