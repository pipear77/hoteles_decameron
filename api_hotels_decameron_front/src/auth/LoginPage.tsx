import { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import {
    Box,
    Typography,
    TextField,
    Button,
    CircularProgress,
    Alert
} from '@mui/material';
import { useAuthContext } from './AuthProvider';

const LoginPage = () => {
    const { loading, error, login, isAuthenticated } = useAuthContext();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const navigate = useNavigate();

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (!email || !password) return;

        await login({ email, password });

        // si se autentica, redirige
        if (!error) {
            navigate('/dashboard');
        }
    };

    return (
        <Box
            sx={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                minHeight: '100vh',
                p: 2,
                bgcolor: 'grey.100',
            }}
        >
            <motion.div initial={{ opacity: 0, y: -20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}>
                <Box sx={{ width: '100%', maxWidth: 400, p: 4, bgcolor: 'background.paper', borderRadius: '24px', boxShadow: 3 }}>
                    <Typography component="h1" variant="h4" sx={{ mb: 1, fontWeight: 'bold' }}>
                        Gestión de Hoteles
                    </Typography>
                    <Typography variant="body1" sx={{ mb: 3 }}>
                        Inicia sesión para continuar
                    </Typography>

                    <Box component="form" onSubmit={handleSubmit} noValidate>
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            label="Correo Electrónico"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                        />
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            label="Contraseña"
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                        />
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            sx={{ mt: 3, mb: 2, borderRadius: '24px', py: 2 }}
                            disabled={loading}
                        >
                            {loading ? <CircularProgress size={24} sx={{ color: 'white' }} /> : 'Iniciar Sesión'}
                        </Button>
                        {error && <Alert severity="error" sx={{ mt: 2 }}>{error}</Alert>}
                    </Box>
                </Box>
            </motion.div>
        </Box>
    );
};

export default LoginPage;
