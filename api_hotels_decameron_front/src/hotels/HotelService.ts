import api from "../api/axiosInstance";
import type { HotelPayload } from "./types";

export const HotelService = {
    async getAll() {
        const { data } = await api.get("/hotels");
        return Array.isArray(data) ? data : data?.data; // soporta API Resource
    },
    async create(payload: HotelPayload) {
        const { data } = await api.post("/hotels", payload);
        return data;
    },
    async update(id: number, payload: Partial<HotelPayload>) {
        const { data } = await api.put(`/hotels/${id}`, payload);
        return data;
    },
    async remove(id: number) {
        await api.delete(`/hotels/${id}`);
    },
};
