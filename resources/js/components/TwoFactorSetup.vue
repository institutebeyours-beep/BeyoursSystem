<template>
    <div class="two-factor-container">
        <h2>🔐 Autenticación de Dos Factores (2FA)</h2>
        
        <!-- ✅ Si NO tiene 2FA activado: mostrar configuración -->
        <div v-if="!enabled">
            <p class="info">Escanea el código QR con tu app de autenticación</p>
            <p class="info">(Google Authenticator, Authy, etc.)</p>
            
            <div v-if="qrCode" class="qr-container" v-html="qrCode"></div>
            
            <div class="secret-box">
                <p>O ingresa manualmente este código:</p>
                <code class="secret">{{ secret }}</code>
                <button @click="copySecret" class="btn-copy">📋 Copiar</button>
            </div>
            
            <div class="recovery-box">
                <p><strong>📌 Guarda estos códigos de respaldo:</strong></p>
                <p class="warning">(Guárdalos en un lugar seguro. Cada código solo sirve una vez)</p>
                <div class="recovery-codes">
                    <span v-for="code in recoveryCodes" :key="code" class="code-badge">
                        {{ code }}
                    </span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="code">Ingresa el código de 6 dígitos de tu app</label>
                <div class="code-input">
                    <input 
                        type="text" 
                        id="code" 
                        v-model="verificationCode" 
                        maxlength="6" 
                        placeholder="000000"
                        @keyup.enter="enable2FA"
                    >
                    <button @click="enable2FA" :disabled="loading" class="btn-verify">
                        {{ loading ? 'Verificando...' : '✅ Activar 2FA' }}
                    </button>
                </div>
                <p class="error" v-if="error">{{ error }}</p>
            </div>
        </div>
        
        <!-- ✅ Si YA tiene 2FA activado: mostrar mensaje (sin botón desactivar) -->
        <div v-else class="enabled-box">
            <p class="success">✅ 2FA activado exitosamente</p>
            <p>Tu cuenta está más segura con autenticación de dos factores.</p>
            <p class="info-admin">🔒 Para desactivar 2FA, contacta a un administrador.</p>
            
            <!-- ✅ Botón para ir al Dashboard -->
            <button @click="goToDashboard" class="btn-dashboard">
                🚀 Ir al Dashboard
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();

const qrCode = ref(null);
const secret = ref(null);
const recoveryCodes = ref([]);
const verificationCode = ref('');
const loading = ref(false);
const enabled = ref(false);
const error = ref('');

onMounted(async () => {
    try {
        const response = await axios.get('/api/2fa/generate');
        qrCode.value = response.data.qr_code;
        secret.value = response.data.secret;
        recoveryCodes.value = response.data.recovery_codes;
    } catch (error) {
        if (error.response?.status === 400) {
            enabled.value = true;
        } else {
            console.error('Error al cargar 2FA:', error);
        }
    }
});

const enable2FA = async () => {
    if (!verificationCode.value || verificationCode.value.length !== 6) {
        error.value = 'Ingresa un código de 6 dígitos';
        return;
    }
    
    loading.value = true;
    error.value = '';
    
    try {
        await axios.post('/api/2fa/enable', { 
            code: verificationCode.value 
        });
        enabled.value = true;
        
        // ✅ Limpiar flag de forzado
        sessionStorage.removeItem('2fa_forced');
        
        Swal.fire({
            icon: 'success',
            title: '✅ 2FA activado exitosamente',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        
        // ✅ Redirigir al Dashboard después de 1.5 segundos
        setTimeout(() => {
            router.push('/dashboard');
        }, 1500);
        
    } catch (err) {
        error.value = err.response?.data?.message || 'Código inválido. Intenta nuevamente.';
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: error.value,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    loading.value = false;
};

const copySecret = () => {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(secret.value);
        Swal.fire({
            icon: 'success',
            title: '📋 Código copiado',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
};

// ✅ Función para ir al Dashboard
const goToDashboard = () => {
    router.push('/dashboard');
};
</script>

<style scoped>
.two-factor-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 2rem auto;
}

h2 {
    color: #2d3748;
    margin-bottom: 1.5rem;
}

.info {
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.qr-container {
    display: flex;
    justify-content: center;
    margin: 1.5rem 0;
    padding: 1rem;
    background: #f7fafc;
    border-radius: 8px;
}

.secret-box {
    background: #f7fafc;
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.secret {
    font-size: 1.2rem;
    font-weight: bold;
    background: #2d3748;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    letter-spacing: 2px;
}

.btn-copy {
    background: #718096;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
}

.btn-copy:hover {
    background: #4a5568;
}

.recovery-box {
    background: #fefcbf;
    border: 1px solid #ecc94b;
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.warning {
    color: #744210;
    font-size: 0.9rem;
}

.recovery-codes {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.code-badge {
    background: #2d3748;
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.8rem;
}

.form-group {
    margin-top: 1.5rem;
}

label {
    display: block;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.code-input {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.code-input input {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    font-size: 1.2rem;
    text-align: center;
    letter-spacing: 4px;
    min-width: 150px;
}

.code-input input:focus {
    outline: none;
    border-color: #4299e1;
}

.btn-verify {
    padding: 0.75rem 2rem;
    background: #48bb78;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-verify:hover:not(:disabled) {
    background: #38a169;
}

.btn-verify:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.error {
    color: #e53e3e;
    margin-top: 0.5rem;
}

.enabled-box {
    background: #f0fff4;
    border: 2px solid #48bb78;
    padding: 2rem;
    border-radius: 8px;
    text-align: center;
}

.success {
    color: #38a169;
    font-size: 1.2rem;
    font-weight: bold;
}

.info-admin {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #edf2f7;
    border-radius: 4px;
    color: #4a5568;
    font-size: 0.9rem;
}

/* ✅ Nuevo botón para ir al Dashboard */
.btn-dashboard {
    margin-top: 1.5rem;
    padding: 0.75rem 2.5rem;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-dashboard:hover {
    background: #3182ce;
}
</style>