// src/hotels/HotelCard.tsx
import React from 'react';
import { Box, Typography, Button, IconButton } from '@mui/material';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import { motion } from 'framer-motion';
import type { Hotel } from './types';

interface HotelCardProps {
    hotel: Hotel;
    onEdit: (hotel: Hotel) => void;
    onDelete: (hotel: Hotel) => void;
}

const HotelCard: React.FC<HotelCardProps> = ({ hotel, onEdit, onDelete }) => {
    return (
        <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.3 }}
        >
            <Box
                className="p-4 rounded-2xl shadow-lg transition-transform duration-300 hover:scale-105"
                sx={{
                    bgcolor: 'background.paper',
                    display: 'flex',
                    flexDirection: 'column',
                    gap: 2,
                }}
            >
                <Typography variant="h6" className="text-xl font-semibold">
                    {hotel.name}
                </Typography>
                <Typography variant="body2" color="text.secondary" className="text-sm">
                    **NIT:** {hotel.nit}
                </Typography>
                <Typography variant="body2" color="text.secondary" className="text-sm">
                    **Habitaciones:** {hotel.rooms_total}
                </Typography>
                <Box sx={{ mt: 'auto', display: 'flex', gap: 1, justifyContent: 'flex-end' }}>
                    <IconButton onClick={() => onEdit(hotel)} aria-label="editar hotel" size="small">
                        <EditIcon color="primary" />
                    </IconButton>
                    <IconButton onClick={() => onDelete(hotel)} aria-label="eliminar hotel" size="small">
                        <DeleteIcon color="error" />
                    </IconButton>
                </Box>
            </Box>
        </motion.div>
    );
};

export default HotelCard;