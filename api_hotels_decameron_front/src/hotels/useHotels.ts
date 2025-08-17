// src/hotels/useHotels.ts
import { useState, useEffect } from "react";
import { HotelService } from "./HotelService";
import type { Hotel, HotelPayload } from "./types";

export const useHotels = () => {
    const [hotels, setHotels] = useState<Hotel[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchHotels = async () => {
        setLoading(true);
        setError(null);
        try {
            const data = await HotelService.getAll();
            setHotels(data);
        } catch (err) {
            console.error(err);
            setError('No se pudo cargar la lista de hoteles.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchHotels();
    }, []);

    const createHotel = async (payload: HotelPayload) => {
        await HotelService.create(payload);
        await fetchHotels(); // Refrescar la lista
    };

    const updateHotel = async (id: number, payload: Partial<HotelPayload>) => {
        await HotelService.update(id, payload);
        await fetchHotels(); // Refrescar la lista
    };

    const deleteHotel = async (id: number) => {
        await HotelService.remove(id);
        await fetchHotels(); // Refrescar la lista
    };

    return {
        hotels,
        loading,
        error,
        createHotel,
        updateHotel,
        deleteHotel,
    };
};