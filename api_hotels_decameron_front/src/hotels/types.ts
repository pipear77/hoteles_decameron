import type { ReactNode } from "react";

// src/hotels/types.ts
export interface LoginCredentials {
    email: string;
    password: string;
}

export interface AuthResponse {
    token: string;
}

export interface City {
    id: number;
    name: string;
}

export interface Accommodation {
    id: number;
    name: string;
}

export interface RoomType {
    id: number;
    name: string;
    allowed: number[]; // IDs de acomodaciones permitidas
}

export interface RoomConfiguration {
    room_type_id: number;
    accommodation_id: number;
    quantity: number;
    price?: number; // Opcional, ya que puede no estar en la solicitud de creación
}

export interface HotelPayload {
    name: string;
    address: string;
    nit: string;
    rooms_total: number;
    city_id: number;
    room_configurations: RoomConfiguration[];
}

export interface Hotel {
    city_id: ReactNode;
    id: number;
    name: string;
    address: string;
    nit: string;
    rooms_total: number;
    city: City; // El backend devuelve el objeto completo de la ciudad
    room_configurations: RoomConfiguration[];
}