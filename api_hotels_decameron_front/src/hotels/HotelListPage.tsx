// src/hotels/HotelListPage.tsx
import React, { useEffect, useState } from 'react';
import { Box, Typography, CircularProgress, Alert, Button } from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import { useNavigate } from 'react-router-dom';
import api from '../api/axiosInstance';
import type { Hotel } from './types';
import { HotelService } from './HotelService'; // 👈 Importa el servicio
import HotelCard from './HotelCard'; // 👈 Importa el nuevo componente

const HotelListPage: React.FC = () => {
    const navigate = useNavigate();
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
            setError('No se pudo cargar la lista de hoteles. Intenta de nuevo más tarde.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchHotels();
    }, []);

    const handleEdit = (hotel: Hotel) => {
        // Implementar la lógica para navegar a la edición del hotel
        // Por ahora, solo muestra un mensaje
        alert(`Navegando a la edición de ${hotel.name}`);
        // TODO: Implementar la navegación a la página de edición
    };

    const handleDelete = async (hotel: Hotel) => {
        if (window.confirm(`¿Estás seguro de que quieres eliminar el hotel "${hotel.name}"?`)) {
            try {
                await HotelService.remove(hotel.id);
                fetchHotels(); // Refrescar la lista después de eliminar
            } catch (err) {
                console.error("Error al eliminar el hotel:", err);
                setError("No se pudo eliminar el hotel. Intenta de nuevo más tarde.");
            }
        }
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
        <Box>
            <Box
                sx={{
                    display: 'flex',
                    flexDirection: 'column',
                    gap: 3,
                }}
            >
                {hotels.length === 0 ? (
                    <Typography className="text-lg text-center mt-10">
                        Aún no hay hoteles para mostrar.
                    </Typography>
                ) : (
                    hotels.map((hotel) => (
                        <HotelCard
                            key={hotel.id}
                            hotel={hotel}
                            onEdit={handleEdit}
                            onDelete={handleDelete}
                        />
                    ))
                )}
            </Box>
        </Box>
    );
};

export default HotelListPage;