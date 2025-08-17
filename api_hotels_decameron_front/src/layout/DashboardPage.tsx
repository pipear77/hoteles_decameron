import { motion } from "framer-motion";
import { Box, Typography, Container, IconButton, Button } from "@mui/material";
import { useAuth } from "../auth/useAuth";
import { Outlet, useNavigate } from "react-router-dom";
import LogoutIcon from "@mui/icons-material/Logout";
import React from "react";

interface DashboardPageProps {
    children?: React.ReactNode;
}

const DashboardPage: React.FC<DashboardPageProps> = () => {
    const { logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = () => {
        logout();
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.5 }}>
            <Box sx={{ bgcolor: "grey.100", minHeight: "100vh", p: 4 }}>
                <Container maxWidth="lg" sx={{ py: 4 }}>
                    <Box sx={{ display: "flex", justifyContent: "space-between", alignItems: "center", mb: 4 }}>
                        <Typography variant="h4" component="h1" fontWeight="bold">
                            Bienvenido a la Gestión de Hoteles
                        </Typography>

                        <Box sx={{ display: "flex", gap: 2 }}>
                            <Button
                                variant="outlined"
                                sx={{ borderRadius: "24px" }}
                                onClick={() => navigate("/dashboard/create-hotel")}
                            >
                                + Crear Hotel
                            </Button>

                            <IconButton onClick={handleLogout} aria-label="cerrar sesión" className="rounded-full shadow-lg p-2">
                                <LogoutIcon className="text-red-500" />
                                &nbsp;Cerrar sesión
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
