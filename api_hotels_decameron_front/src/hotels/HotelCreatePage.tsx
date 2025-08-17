// src/hotels/HotelCreatePage.tsx
import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom"; // 👈 Importa useNavigate
import { Box, Typography, CircularProgress, Alert } from "@mui/material";
import HotelForm from "./HotelForm";
import api from "../api/axiosInstance";
import type { Hotel, HotelPayload } from "./types";

const HotelCreatePage: React.FC = () => {
    const navigate = useNavigate(); // 👈 Inicializa el hook de navegación

    const [hotels, setHotels] = useState<Hotel[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchHotels = async () => {
            setLoading(true);
            setError(null);
            try {
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    throw new Error('No se encontró el token de autenticación.');
                }
                const response = await api.get('/hotels', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    },
                });
                setHotels(response.data.data || []);
            } catch (err) {
                console.error(err);
                setError('No se pudo cargar la lista de hoteles para validación.');
            } finally {
                setLoading(false);
            }
        };
        fetchHotels();
    }, []);

    const handleSaveHotel = async (payload: HotelPayload | Partial<HotelPayload>, id?: number) => {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                throw new Error('No se encontró el token de autenticación.');
            }

            if (id) {
                // Lógica para actualizar un hotel
                await api.put(`/hotels/${id}`, payload, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
            } else {
                // Lógica para crear un nuevo hotel
                await api.post('/hotels', payload, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
            }

            // 🚀 Redirección exitosa
            navigate("/dashboard");

        } catch (saveError) {
            console.error("Error al guardar el hotel:", saveError);
            setError("No se pudo guardar el hotel. Por favor, revisa la información.");
            // Aquí puedes agregar un manejo de error más específico si la API devuelve mensajes detallados
        }
    };

    const handleCancel = () => {
        navigate("/dashboard");
    };

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4 }}>
                <CircularProgress />
            </Box>
        );
    }

    if (error) {
        return (
            <Box mt={2}>
                <Alert severity="error">{error}</Alert>
            </Box>
        );
    }

    return (
        <Box sx={{ p: 4, bgcolor: 'background.paper', borderRadius: 'rounded-2xl', boxShadow: 'shadow-lg' }}>
            <Typography variant="h4" component="h1" mb={3} className="text-3xl">
                Crear Hotel
            </Typography>
            <HotelForm
                hotelsSnapshot={hotels}
                onCancel={handleCancel}
                onSave={handleSaveHotel}
            />
        </Box>
    );
};

export default HotelCreatePage;