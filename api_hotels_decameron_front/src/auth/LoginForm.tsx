import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from './useAuth'; // Usar el hook personalizado
import './LoginPage.css'; // Importar el CSS local

const LoginPage: React.FC = () => {
    const { loading, error, login } = useAuth(); // Usar el hook para manejar la lógica
    const [email, setEmail] = useState<string>('');
    const [password, setPassword] = useState<string>('');

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        // La validación simple se mantiene aquí, pero la lógica de login se delega al hook
        if (!email || !password) {
            // El hook no puede manejar este error, así que lo mostramos directamente
            // Si usáramos una librería como Zod, se manejaría dentro del hook
            alert('Por favor, ingresa tu correo y contraseña.');
            return;
        }
        await login({ email, password });
    };

    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">
            <motion.div
                className="w-full max-w-md bg-white rounded-2xl shadow-lg p-6"
                initial={{ opacity: 0, y: -20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
            >
                <div className="text-center mb-6">
                    <h1 className="text-3xl font-bold text-gray-900 mb-1">Gestión de Hoteles</h1>
                    <p className="text-lg text-gray-600">Inicia sesión para continuar</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Correo Electrónico
                        </label>
                        <motion.input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="Ingresa tu correo"
                            className="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            whileFocus={{ boxShadow: '0 0 0 3px rgba(99, 102, 241, 0.5)' }}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Contraseña
                        </label>
                        <motion.input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="Ingresa tu contraseña"
                            className="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            whileFocus={{ boxShadow: '0 0 0 3px rgba(99, 102, 241, 0.5)' }}
                        />
                    </div>
                    <motion.button
                        type="submit"
                        className="w-full py-3 px-4 font-semibold text-white bg-indigo-600 rounded-2xl shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                        disabled={loading}
                        // La lógica de animación se puede mover al hook para mayor limpieza
                        // pero la mantenemos aquí para simplicidad del ejemplo
                        initial={{ scale: 1, backgroundColor: '#4F46E5' }}
                        whileHover={{ scale: 1.05, backgroundColor: '#4338CA' }}
                        whileTap={{ scale: 0.95 }}
                    >
                        {loading ? 'Cargando...' : 'Iniciar Sesión'}
                    </motion.button>
                </form>

                {error && <p className="text-red-600 text-sm text-center mt-4">{error}</p>}
            </motion.div>
        </div>
    );
};

export default LoginPage;