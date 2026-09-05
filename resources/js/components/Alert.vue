<template>
    <div class="alert-modern" :class="type">
        <div class="alert-icon">{{ icon }}</div>
        <div class="alert-body">
            <h4 v-if="title" class="alert-title">{{ title }}</h4>
            <p class="alert-message">{{ message }}</p>
        </div>
        <button v-if="dismissible" @click="$emit('dismiss')" class="alert-close">×</button>
    </div>
</template>

<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'warning', 'error', 'info'].includes(value)
    },
    title: String,
    message: String,
    dismissible: Boolean,
    icon: String
});

defineEmits(['dismiss']);
</script>

<style scoped>
.alert-modern {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    animation: slideIn 0.4s ease;
    position: relative;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.alert-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.alert-body {
    flex: 1;
}

.alert-title {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
}

.alert-message {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

.alert-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.3s;
    line-height: 1;
}

.alert-close:hover {
    opacity: 1;
}

/* Tipos de alerta */
.alert-modern.success {
    background: linear-gradient(135deg, #f0fff4, #e6fffa);
    border-left: 4px solid #48bb78;
    color: #276749;
}

.alert-modern.warning {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border-left: 4px solid #ed8936;
    color: #975a16;
}

.alert-modern.error {
    background: linear-gradient(135deg, #fff5f5, #fed7d7);
    border-left: 4px solid #fc8181;
    color: #9b2c2c;
}

.alert-modern.info {
    background: linear-gradient(135deg, #ebf8ff, #bee3f8);
    border-left: 4px solid #4299e1;
    color: #2a4365;
}
</style>