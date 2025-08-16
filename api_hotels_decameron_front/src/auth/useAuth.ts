import { useState } from "react";
import api from "../api/axiosInstance";

export function useAuth() {
    const [token, setToken] = useState<string | null>(localStorage.getItem("token"));

    const login = async (email: string, password: string) => {
        const { data } = await api.post("/login", { email, password });
        const t = data?.token ?? data?.access_token ?? "";
        if (!t) throw new Error("Token no recibido");
        localStorage.setItem("token", t);
        setToken(t);
    };

    const logout = async () => {
        try { await api.post("/logout"); } catch { }
        localStorage.removeItem("token");
        setToken(null);
    };

    return { token, login, logout };
}
