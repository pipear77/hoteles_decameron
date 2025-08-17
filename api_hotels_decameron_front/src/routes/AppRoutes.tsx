// src/routes/AppRoutes.tsx
import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import LoginPage from '../auth/LoginPage';
import DashboardPage from '../layout/DashboardPage';
import HotelListPage from '../hotels/HotelListPage';
import PrivateRoute from './PrivateRoute';
import HotelCreatePage from '../hotels/HotelCreatePage';
import HotelEditPage from '../hotels/HotelEditPage';

const AppRoutes: React.FC = () => {
    return (
        <Routes>
            <Route path="/" element={<Navigate to="/login" replace />} />
            <Route path="/login" element={<LoginPage />} />
            <Route
                path="/dashboard"
                element={
                    <PrivateRoute>
                        <DashboardPage />
                    </PrivateRoute>
                }
            >
                <Route index element={<HotelListPage />} />
                <Route path="create-hotel" element={<HotelCreatePage />} />
                <Route path="edit-hotel/:id" element={<HotelEditPage />} />

            </Route>
        </Routes>
    );
};

export default AppRoutes;