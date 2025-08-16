import api from "./axios"; // tu cliente de Axios
import { type AuthResponse } from "../types/auth";

export const login = async (credentials: { email: string; password: string }): Promise<AuthResponse> => {
    const { data } = await api.post<AuthResponse>("/login", credentials);
    return data;
};

export const register = async (newUser: {
    first_name: string;
    last_name: string;
    email: string;
    password: string;
    role_id: number;
}): Promise<AuthResponse> => {
    const { data } = await api.post<AuthResponse>("/register", newUser);
    return data;
};

export const logout = async (): Promise<void> => {
    await api.post("/logout");
};
