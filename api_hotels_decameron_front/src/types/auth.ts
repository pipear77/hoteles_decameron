// src/types/auth.ts
export interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    role_id: number;
}

export interface AuthResponse {
    token: string;
    user: User;
}
