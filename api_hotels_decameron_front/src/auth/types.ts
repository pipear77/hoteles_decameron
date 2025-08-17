// src/auth/types.ts
export interface LoginCredentials {
    email: string;
    password: string;
}

export interface AuthResponse {
    token: string;
    // Otros datos de usuario que el backend pueda devolver
}

export interface AuthContextType {
    isAuthenticated: boolean;
    loading: boolean;
    error: string | null;
    login: (credentials: LoginCredentials) => Promise<void>;
    logout: () => void;
}

// src/hotels/types.ts
export interface RoomConfiguration {
    room_type_id: number;
    accommodation_id: number;
    quantity: number;
}

export interface Hotel {
    id: number;
    name: string;
    nit: string;
    address: string;
    rooms_total: number;
    city_id: number;
    room_configurations: RoomConfiguration[];
}

export interface HotelPayload {
    name: string;
    address: string;
    nit: string;
    rooms_total: number;
    city_id: number; // <-- debe ser number, no string
    room_configurations: RoomConfiguration[];
}

