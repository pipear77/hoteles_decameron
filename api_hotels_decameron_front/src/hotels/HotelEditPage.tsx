// src/hotels/HotelEditPage.tsx
import React, { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { Box, Typography, CircularProgress, Alert } from "@mui/material";
import HotelForm from "./HotelForm";
import { HotelService } from "./HotelService";
import type { Hotel, HotelPayload } from "./types";

const HotelEditPage: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();

    const [hotel, setHotel] = useState<Hotel | null>(null);
    const [hotelsSnapshot, setHotelsSnapshot] = useState<Hotel[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                // Obtener el hotel específico
                const hotelData = await HotelService.getById(Number(id));
                setHotel(hotelData);
                
                // Obtener todos los hoteles para la validación de duplicados
                const allHotels = await HotelService.getAll();
                setHotelsSnapshot(allHotels);
            } catch (err) {
                console.error("Error al cargar datos:", err);
                setError("No se pudo cargar la información del hotel. Intenta de nuevo.");
            } finally {
                setLoading(false);
            }
        };

        if (id) {
            fetchData();
        } else {
            setError("No se encontró el ID del hotel para editar.");
            setLoading(false);
        }
    }, [id]);

    const handleSaveHotel = async (payload: Partial<HotelPayload>) => {
        try {
            await HotelService.update(Number(id), payload);
            navigate("/dashboard");
        } catch (saveError) {
            console.error("Error al guardar el hotel:", saveError);
            setError("No se pudo guardar los cambios. Por favor, revisa la información.");
        }
    };

    const handleCancel = () => {
        navigate("/dashboard");
    };

    if (loading) {
        return <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4 }}><CircularProgress /></Box>;
    }

    if (error || !hotel) {
        return <Box mt={2}><Alert severity="error">{error}</Alert></Box>;
    }

    return (
        <Box sx={{ p: 4, bgcolor: 'background.paper', borderRadius: 'rounded-2xl', boxShadow: 'shadow-lg' }}>
            <Typography variant="h4" component="h1" mb={3} className="text-3xl">
                Editar Hotel
            </Typography>
            <HotelForm
                initial={hotel}
                hotelsSnapshot={hotelsSnapshot}
                onCancel={handleCancel}
                onSave={handleSaveHotel}
            />
        </Box>
    );
};

export default HotelEditPage;