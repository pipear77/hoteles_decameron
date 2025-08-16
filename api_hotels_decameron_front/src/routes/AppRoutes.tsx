import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import LoginPage from "../auth/LoginPage";
import DashboardLayout from "../layout/DashboardLayout";
import HotelListPage from "../hotels/HotelListPage";

function PrivateRoute({ children }: { children: React.ReactElement }) {
    const token = localStorage.getItem("token");
    return token ? children : <Navigate to="/login" />;
}

export default function AppRoutes() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/login" element={<LoginPage />} />
                <Route
                    path="/*"
                    element={
                        <PrivateRoute>
                            <DashboardLayout>
                                <Routes>
                                    <Route path="/" element={<HotelListPage />} />
                                </Routes>
                            </DashboardLayout>
                        </PrivateRoute>
                    }
                />
            </Routes>
        </BrowserRouter>
    );
}
