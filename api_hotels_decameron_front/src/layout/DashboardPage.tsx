// src/layout/DashboardPage.tsx
import { motion } from "framer-motion";
import { Box, Typography, Container, IconButton, Button } from "@mui/material";
import { useAuth } from "../auth/useAuth";
import { Outlet, useNavigate } from "react-router-dom";
import LogoutIcon from "@mui/icons-material/Logout";
import React from "react";

const DashboardPage: React.FC = () => {
    const { logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = () => {
        logout();
    };

    const navigateToCreate = () => {
        navigate("/dashboard/create-hotel");
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.5 }}>
            <Box sx={{ bgcolor: "grey.100", minHeight: "100vh", p: 4 }}>
                <Container maxWidth="lg" sx={{ py: 4 }}>
                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                            mb: 4,
                            p: 4,
                            bgcolor: 'background.paper',
                            borderRadius: 'rounded-2xl',
                            boxShadow: 'shadow-lg',
                        }}
                    >
                        <Typography variant="h4" component="h1" fontWeight="bold">
                            Gestión de Hoteles
                        </Typography>

                        <Box sx={{ display: "flex", gap: 2, alignItems: "center" }}>
                            {/* El botón de crear hotel ahora se muestra aquí */}
                            <Button
                                variant="contained"
                                onClick={navigateToCreate}
                                className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-2xl transition-colors duration-300 shadow-md"
                            >
                                + Crear Hotel
                            </Button>
                            <IconButton onClick={handleLogout} aria-label="cerrar sesión" className="rounded-full shadow-lg p-2">
                                <LogoutIcon className="text-red-500" />
                                <Typography component="span" sx={{ ml: 1, color: 'text.secondary' }}>Cerrar sesión</Typography>
                            </IconButton>
                        </Box>
                    </Box>
                    {/* Aquí se renderizan las rutas hijas */}
                    <Outlet />
                </Container>
            </Box>
        </motion.div>
    );
};

export default DashboardPage;