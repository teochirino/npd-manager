<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
            <!-- Logo -->
            <div class="mb-6 text-center">
                <svg viewBox="0 0 200 58" width="180" height="52" class="mx-auto" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1" y="6" width="42" height="42" rx="4" fill="none" stroke="#1a1f3c" stroke-width="2.5"/>
                    <text x="22" y="30" text-anchor="middle" dominant-baseline="middle" font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#1a1f3c" letter-spacing="2">LI</text>
                    <text x="54" y="26" font-family="Georgia,serif" font-size="19" font-weight="400" fill="#1a1f3c">línea italia</text>
                    <text x="186" y="14" font-family="Georgia,serif" font-size="9" fill="#9ca3af">®</text>
                    <text x="54" y="41" font-family="Arial,sans-serif" font-size="7.5" font-weight="400" fill="#9ca3af" letter-spacing="2.8">ECCELLENZA IN MOBILE</text>
                </svg>
                <div class="text-xs tracking-wider uppercase text-gray-400 mt-2">NPD Manager · Gestión de Innovación</div>
            </div>

            <!-- Formulario -->
            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Usuario</label>
                    <input 
                        v-model="form.email"
                        type="email" 
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none"
                        placeholder="correo@lineaitalia.com"
                        required
                    />
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Contraseña</label>
                    <input 
                        v-model="form.password"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none"
                        placeholder="••••••••"
                        required
                    />
                </div>
                <div v-if="error" class="mb-4 text-sm text-red-600 bg-red-50 p-2 rounded">
                    {{ error }}
                </div>
                <button 
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-navy text-white py-2 rounded-lg font-semibold hover:bg-navy-2 transition disabled:opacity-50"
                >
                    {{ loading ? 'Ingresando...' : 'Ingresar →' }}
                </button>
            </form>

            <!-- Credenciales de prueba -->
            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-400">
                <p class="mb-1">🔐 Credenciales de prueba:</p>
                <p>gerente@lineaitalia.com / gerente2026</p>
                <p>rbarraza@lineaitalia.com / rb2026</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const form = ref({
    email: '',
    password: ''
})

const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
    loading.value = true
    error.value = ''
    
    try {
        console.log('Enviando petición a:', '/api/login')
        console.log('Datos:', form.value)
        
        const response = await axios.post('/api/login', form.value)
        
        console.log('Respuesta:', response.data)
        
        const { user, token } = response.data
        
        // Guardar token en localStorage
        localStorage.setItem('token', token)
        localStorage.setItem('user', JSON.stringify(user))
        
        // Configurar axios para próximas peticiones
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        // Redirigir al dashboard
        window.location.href = '/dashboard'
        
    } catch (err) {
        console.error('Error de login:', err)
        const errorMsg = err.response?.data?.message || err.response?.data?.errors?.email?.[0] || 'Credenciales incorrectas'
        error.value = errorMsg
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.bg-navy {
    background-color: #1a1f3c;
}
.bg-navy-2 {
    background-color: #2d3561;
}
</style>