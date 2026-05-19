import axios from 'axios';

// Configuración base de axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';
window.axios.defaults.baseURL = 'http://127.0.0.1:8000';

// Configurar token si existe
const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}