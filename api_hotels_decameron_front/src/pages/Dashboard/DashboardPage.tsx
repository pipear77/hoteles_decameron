// src/pages/Dashboard/DashboardPage.tsx
import { useAuthContext } from "../../context/AuthContext";

const DashboardPage = () => {
    const { user, logout } = useAuthContext();

    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
            <div className="bg-white shadow-lg rounded-2xl p-8 text-center">
                <h1 className="text-3xl font-bold mb-4">
                    Bienvenido 🎉 {user?.first_name || "Usuario"}
                </h1>
                <p className="text-lg mb-6">
                    Ya estás autenticado en el sistema.
                </p>
                <button
                    onClick={logout}
                    className="bg-red-500 text-white px-6 py-2 rounded-2xl shadow-md hover:bg-red-600 transition"
                >
                    Cerrar sesión
                </button>
            </div>
        </div>
    );
};

export default DashboardPage;
