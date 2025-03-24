import axios from "axios";

const api = axios.create({
    baseURL: '/api/', // Laravel usa /api para rutas API
    headers: {
      'Content-Type': 'application/json',
    }
});

// Interceptor para agregar el token (si usas autenticación)
api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => Promise.reject(error));

export default api;
