import { useState } from "react";
import { useAuth } from "../../hooks/useAuth";
import { motion } from "framer-motion";

const RegisterPage = () => {
    const { register } = useAuth();
    const [firstName, setFirstName] = useState("");
    const [lastName, setLastName] = useState("");
    const [email, setEmail] = useState("");
    const [roleId, setRoleId] = useState<number>(1); // ⚠️ Podrías cargar roles desde backend
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        setSuccess(null);
        if (password !== confirmPassword) {
            setError("Las contraseñas no coinciden");
            return;
        }
        setLoading(true);
        // try {
        //     const res = await register({
        //         first_name: firstName,
        //         last_name: lastName,
        //         email,
        //         role_id: roleId,
        //         password,
        //         password_confirmation: confirmPassword,
        //     });
        //     if (res.status) {
        //         setSuccess("Usuario registrado exitosamente");
        //     } else {
        //         setError(res.message || "Error al registrarse");
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
                className="bg-white shadow-lg rounded-2xl p-6 w-full max-w-md"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
            >
                <h1 className="text-2xl font-bold mb-4 text-center">Registro</h1>

                {error && <p className="text-red-600 text-sm mb-3">{error}</p>}
                {success && <p className="text-green-600 text-sm mb-3">{success}</p>}

                <div className="flex gap-2">
                    <input
                        type="text"
                        placeholder="Nombre"
                        value={firstName}
                        onChange={(e) => setFirstName(e.target.value)}
                        className="border p-2 rounded w-1/2 focus:ring focus:ring-blue-200"
                        required
                    />
                    <input
                        type="text"
                        placeholder="Apellido"
                        value={lastName}
                        onChange={(e) => setLastName(e.target.value)}
                        className="border p-2 rounded w-1/2 focus:ring focus:ring-blue-200"
                        required
                    />
                </div>

                <input
                    type="email"
                    placeholder="Correo electrónico"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="border p-2 rounded w-full my-3 focus:ring focus:ring-blue-200"
                    required
                />

                {/* 🔑 En el futuro podrías mapear roles desde backend */}
                <select
                    value={roleId}
                    onChange={(e) => setRoleId(Number(e.target.value))}
                    className="border p-2 rounded w-full mb-3"
                >
                    <option value={1}>Usuario</option>
                    <option value={2}>Administrador</option>
                </select>

                <input
                    type="password"
                    placeholder="Contraseña"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="border p-2 rounded w-full mb-3 focus:ring focus:ring-blue-200"
                    required
                />
                <input
                    type="password"
                    placeholder="Confirmar contraseña"
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    className="border p-2 rounded w-full mb-4 focus:ring focus:ring-blue-200"
                    required
                />

                <button
                    type="submit"
                    disabled={loading}
                    className="bg-green-600 text-white rounded-2xl p-2 w-full hover:bg-green-700 disabled:opacity-50"
                >
                    {loading ? "Registrando..." : "Registrarse"}
                </button>
            </motion.form>
        </div>
    );
};

export default RegisterPage;
