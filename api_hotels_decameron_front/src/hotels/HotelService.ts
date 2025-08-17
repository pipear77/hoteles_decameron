// src/hotels/HotelService.ts
import api from "../api/axiosInstance";
import type { Hotel, HotelPayload } from "./types";

const getAuthHeaders = () => {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        throw new Error('No se encontró el token de autenticación.');
    }
    return { 'Authorization': `Bearer ${token}` };
};

export const HotelService = {
    getAll: async (): Promise<Hotel[]> => {
        const response = await api.get('/hotels', { headers: getAuthHeaders() });
        return response.data.data;
    },

    create: async (payload: HotelPayload): Promise<Hotel> => {
        const response = await api.post('/hotels', payload, { headers: getAuthHeaders() });
        return response.data.data;
    },

    update: async (id: number, payload: Partial<HotelPayload>): Promise<Hotel> => {
        const response = await api.put(`/hotels/${id}`, payload, { headers: getAuthHeaders() });
        return response.data.data;
    },

    remove: async (id: number): Promise<void> => {
        await api.delete(`/hotels/${id}`, { headers: getAuthHeaders() });
    },
};