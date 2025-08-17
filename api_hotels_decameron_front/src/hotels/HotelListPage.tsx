// src/hotels/HotelListPage.tsx
import React, { useEffect, useState } from 'react';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';
import Alert from '@mui/material/Alert';
import api from '../api/axiosInstance';
import type { Hotel } from './types'; // Verifica la ruta de importación

const HotelListPage: React.FC = () => {
    const [hotels, setHotels] = useState<Hotel[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchHotels = async () => {
            setLoading(true);
            setError(null);
            try {
                const token = localStorage.getItem('auth_token');
                
                // Si no hay token, no hacemos la petición (el PrivateRoute lo maneja)
                if (!token) {
                    setLoading(false);
                    return;
                }

                const response = await api.get('/hotels', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    },
                });

                setHotels(response.data.data);
            } catch (err) {
                console.error(err);
                setError('No se pudo cargar la lista de hoteles. Intenta de nuevo más tarde.');
            } finally {
                setLoading(false);
            }
        };

        fetchHotels();
    }, []);

    // Lógica para mostrar los diferentes estados de la UI
    if (loading) {
        return <CircularProgress />;
    }

    if (error) {
        return <Alert severity="error">{error}</Alert>;
    }
    
    if (hotels.length === 0) {
        return <Typography>Aún no hay hoteles para mostrar.</Typography>;
    }

    return (
        <Box>
            <Typography variant="h4" component="h1">
                Listado de Hoteles
            </Typography>
            {/* Aquí deberías mapear la lista de hoteles para mostrarlos */}
            <ul>
                {hotels.map((hotel) => (
                    <li key={hotel.id}>{hotel.name}</li>
                ))}
            </ul>
        </Box>
    );
};

export default HotelListPage;