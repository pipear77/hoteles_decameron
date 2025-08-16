// src/context/AuthContext.tsx
import { createContext, useContext, useState, type ReactNode } from "react";
import * as authApi from "../services/auth"; // 👈 ajusta ruta según tu estructura
import { type User } from "../types/auth";

interface AuthContextType {
    user: User | null;
    login: (email: string, password: string) => Promise<void>;
    register: (firstName: string, lastName: string, email: string, password: string) => Promise<void>;
    logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [user, setUser] = useState<User | null>(
        JSON.parse(localStorage.getItem("user") || "null")
    );

    const login = async (email: string, password: string) => {
        const res = await authApi.login({ email, password });
        localStorage.setItem("token", res.token);
        localStorage.setItem("user", JSON.stringify(res.user));
        setUser(res.user);
    };

    const register = async (firstName: string, lastName: string, email: string, password: string) => {
        const res = await authApi.register({ first_name: firstName, last_name: lastName, email, password, role_id: 1 });
        localStorage.setItem("token", res.token);
        localStorage.setItem("user", JSON.stringify(res.user));
        setUser(res.user);
    };

    const logout = async () => {
        await authApi.logout();
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        setUser(null);
    };

    return (
        <AuthContext.Provider value={{ user, login, register, logout }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuthContext = () => {
    const ctx = useContext(AuthContext);
    if (!ctx) throw new Error("useAuthContext debe usarse dentro de AuthProvider");
    return ctx;
};

export default AuthContext;
