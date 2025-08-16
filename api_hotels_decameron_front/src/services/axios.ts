import axios from "axios";
import type {
    AxiosInstance,
    AxiosResponse,
    InternalAxiosRequestConfig,
} from "axios";

// ⚙️ URL base de la API (usa variables de entorno en producción)
const API_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

// 🛠️ Instancia única de Axios (principio DRY)
const api: AxiosInstance = axios.create({
    baseURL: API_URL,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

// 🔑 Interceptor para añadir token de autenticación automáticamente
api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// 📦 Interceptor de respuesta (manejo global de errores)
api.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Si el token expira → logout automático
            localStorage.removeItem("token");
            window.location.href = "/login";
        }
        return Promise.reject(error);
    }
);

export default api;
