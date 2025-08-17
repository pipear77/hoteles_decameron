// src/auth/useAuth.ts
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import authService from './authService';
import type { LoginCredentials } from '../hotels/types';

interface AuthContextType {
    isAuthenticated: boolean;
    loading: boolean;
    error: string | null;
    login: (credentials: LoginCredentials) => Promise<void>;
    logout: () => void;
}

export const useAuth = (): AuthContextType => {
    const [isAuthenticated, setIsAuthenticated] = useState<boolean>(false);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);
    const navigate = useNavigate();

    useEffect(() => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            setIsAuthenticated(true);
        }
        setLoading(false);
    }, []);

    const login = async (credentials: LoginCredentials) => {
        setLoading(true);
        setError(null);
        try {
            await authService.login(credentials);
            setIsAuthenticated(true);
            navigate('/dashboard');
        } catch (err) {
            setError('Credenciales incorrectas o error en el servidor.');
            setIsAuthenticated(false);
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const logout = () => {
        authService.logout();
        setIsAuthenticated(false);
        navigate('/login');
    };

    return {
        isAuthenticated,
        loading,
        error,
        login,
        logout,
    };
};