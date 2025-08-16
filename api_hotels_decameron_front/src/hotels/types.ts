export type RoomConfiguration = {
    room_type_id: number;
    accommodation_id: number;
    quantity: number;
};

export type HotelPayload = {
    name: string;
    address: string;
    nit: string;
    rooms_total: number;
    city_id: number;
    room_configurations: RoomConfiguration[];
};

export type Hotel = {
    id: number;
    name: string;
    address: string;
    nit: string;
    rooms_total: number;
    city?: { id: number; name: string } | null;
    city_id?: number;
    room_configurations?: RoomConfiguration[];
};
