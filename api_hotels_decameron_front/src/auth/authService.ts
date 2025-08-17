// src/auth/authService.ts
import axios from 'axios';
import api from '../api/axiosInstance'; // Importación corregida
import type { LoginCredentials, AuthResponse } from './types';

export const authService = {
    login: async (credentials: LoginCredentials): Promise<AuthResponse> => {
        try {
            const response = await api.post<AuthResponse>('/login', credentials);
            return response.data;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                throw new Error(error.response.data.message || 'Credenciales incorrectas');
            }
            throw new Error('Error desconocido durante la autenticación.');
        }
    },

    logout: async (): Promise<void> => {
        localStorage.removeItem('auth_token');
    },
};

export default authService;