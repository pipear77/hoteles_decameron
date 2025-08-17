import { useState, useEffect } from "react";
import HotelForm from "./HotelForm";
import type { Hotel, HotelPayload } from "./types";
import { useNavigate } from "react-router-dom";

export default function HotelCreatePage() {
    const [hotelsSnapshot, setHotelsSnapshot] = useState<Hotel[]>([]);
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    // Carga de hoteles para validar duplicados (nombre/NIT) en el formulario
    useEffect(() => {
        const fetchHotels = async () => {
            try {
                const token = localStorage.getItem("auth_token");
                const res = await fetch("http://localhost:8000/api/hotels", {
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        "Accept": "application/json",
                    },
                });
                if (res.ok) {
                    const data = await res.json();
                    setHotelsSnapshot(data);
                }
            } catch (err) {
                console.error("Error cargando hoteles", err);
            }
        };
        fetchHotels();
    }, []);

    // Firma compatible con HotelForm: (payload | partial, id?)
    const handleSave = async (
        payload: HotelPayload | Partial<HotelPayload>,
        _id?: number
    ): Promise<void> => {
        setLoading(true);
        try {
            const token = localStorage.getItem("auth_token");

            // Para crear esperamos un HotelPayload completo; el form ya valida.
            const body = payload as HotelPayload;

            const res = await fetch("http://localhost:8000/api/hotels", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json",
                },
                body: JSON.stringify(body),
            });

            if (!res.ok) {
                const data = await res.json().catch(() => ({}));
                throw new Error(data.message || "Error al guardar hotel");
            }

            // Éxito → volver al listado
            navigate("/dashboard");
        } catch (err: any) {
            alert(err?.message ?? "Error inesperado al crear hotel");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-6">
            <h1 className="text-2xl font-bold mb-4">Crear Hotel</h1>
            {loading && <p>Guardando...</p>}
            <HotelForm
                initial={null}
                hotelsSnapshot={hotelsSnapshot}
                onCancel={() => navigate("/dashboard")}
                onSave={handleSave}
            />
        </div>
    );
}
