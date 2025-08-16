import { useState } from "react";
import { useAuth } from "../../hooks/useAuth";
import { motion } from "framer-motion";

const LoginPage = () => {
    const { login } = useAuth();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        setLoading(true);
        // try {
        //     const res = await login({ email, password });
        //     if (!res.status) {
        //         setError(res.message || "Error al iniciar sesión");
        //     }
        // } catch {
        //     setError("Error inesperado en el servidor");
        // } finally {
        //     setLoading(false);
        // }
    };

    return (
        <div className="flex items-center justify-center min-h-screen bg-gray-100 px-4">
            <motion.form
                onSubmit={handleSubmit}
                className="bg-white shadow-lg rounded-2xl p-6 w-full max-w-sm"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
            >
                <h1 className="text-2xl font-bold mb-4 text-center">Iniciar sesión</h1>

                {error && (
                    <p className="text-red-600 text-sm mb-3 text-center">{error}</p>
                )}

                <input
                    type="email"
                    placeholder="Correo electrónico"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="border p-2 rounded w-full mb-3 focus:ring focus:ring-blue-200"
                    required
                />
                <input
                    type="password"
                    placeholder="Contraseña"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="border p-2 rounded w-full mb-4 focus:ring focus:ring-blue-200"
                    required
                />

                <button
                    type="submit"
                    disabled={loading}
                    className="bg-blue-600 text-white rounded-2xl p-2 w-full hover:bg-blue-700 disabled:opacity-50"
                >
                    {loading ? "Ingresando..." : "Entrar"}
                </button>
            </motion.form>
        </div>
    );
};

export default LoginPage;
