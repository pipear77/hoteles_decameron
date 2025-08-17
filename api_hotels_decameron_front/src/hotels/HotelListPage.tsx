// src/hotels/HotelListPage.tsx
import React, { useEffect, useState } from 'react';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';
import Alert from '@mui/material/Alert';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import { useAuthContext } from '../auth/AuthProvider'; // ⚠️ Asegúrate de tener este hook
import api from '../api/axiosInstance';
import type { Hotel } from './types';
import { Link } from 'react-router-dom'; // 👈 Importa Link para la navegación

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
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                    mb: 4,
                }}
            >
                <Typography variant="h4" component="h1">
                    Listado de Hoteles
                </Typography>
                <Button
                    component={Link} // 👈 Usa Link para la navegación
                    to="/dashboard/create-hotel" // 👈 Ruta para el formulario
                    variant="contained"
                    startIcon={<AddIcon />}
                >
                    Crear Hotel
                </Button>
            </Box>

            {hotels.length === 0 ? (
                <Typography>Aún no hay hoteles para mostrar.</Typography>
            ) : (
                <ul>
                    {hotels.map((hotel) => (
                        <li key={hotel.id}>{hotel.name}</li>
                    ))}
                </ul>
            )}
        </Box>
    );
};

export default HotelListPage;