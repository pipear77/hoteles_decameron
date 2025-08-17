// src/api/axiosInstance.ts
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // <-- Asegúrate de que esto sea correcto
  headers: {
    'Content-Type': 'application/json',
  },
});

export default api;