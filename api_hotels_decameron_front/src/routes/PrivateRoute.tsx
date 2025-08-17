import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuthContext } from '../auth/AuthProvider';

const PrivateRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const { isAuthenticated, loading } = useAuthContext();
    const location = useLocation();

    if (loading) return <div>Cargando...</div>;
    if (!isAuthenticated) return <Navigate to="/login" state={{ from: location }} replace />;
    return <>{children}</>;
};

export default PrivateRoute;
