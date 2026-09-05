<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- ========================================== -->
        <!-- SIDEBAR - SOLO SI ESTÁ AUTENTICADO        -->
        <!-- ========================================== -->
        <Sidebar v-if="authStore.isAuthenticated" />

        <!-- ========================================== -->
        <!-- CONTENIDO PRINCIPAL                        -->
        <!-- ========================================== -->
        <main 
            class="min-h-screen transition-all duration-300"
            :class="authStore.isAuthenticated ? 'sm:ml-64' : ''"
        >
            <div class="container mx-auto px-3 sm:px-6 py-4 sm:py-6">
                <router-view />
            </div>
        </main>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import Sidebar from './Sidebar.vue';

const authStore = useAuthStore();
</script>