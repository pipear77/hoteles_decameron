import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "./useAuth";

export default function LoginPage() {
    const { login } = useAuth();
    const nav = useNavigate();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [loading, setLoading] = useState(false);
    const [err, setErr] = useState<string | null>(null);

    const onSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErr(null);
        setLoading(true);
        try {
            await login(email, password);
            nav("/");
        } catch (e: any) {
            setErr(e?.response?.data?.message || "Credenciales inválidas");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen grid place-items-center bg-gray-100 p-4">
            <form onSubmit={onSubmit} className="w-full max-w-sm bg-white shadow rounded-2xl p-6 space-y-4">
                <h1 className="text-xl font-semibold text-gray-800">Iniciar sesión</h1>

                <div>
                    <label className="block text-sm mb-1">Email</label>
                    <input
                        type="email"
                        className="w-full border rounded-lg px-3 py-2 outline-none focus:ring"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                        autoFocus
                    />
                </div>

                <div>
                    <label className="block text-sm mb-1">Contraseña</label>
                    <input
                        type="password"
                        className="w-full border rounded-lg px-3 py-2 outline-none focus:ring"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                </div>

                {err && <p className="text-sm text-red-600">{err}</p>}

                <button
                    type="submit"
                    className="w-full rounded-lg py-2 bg-blue-600 text-white font-medium disabled:opacity-50"
                    disabled={loading}
                >
                    {loading ? "Ingresando..." : "Entrar"}
                </button>
            </form>
        </div>
    );
}
