import { useAuth } from "./useAuth";

export default function LogoutButton() {
    const { logout } = useAuth();
    return (
        <button
            onClick={logout}
            className="mt-6 w-full bg-red-600 hover:bg-red-700 text-white rounded-lg py-2"
        >
            Cerrar sesión
        </button>
    );
}
