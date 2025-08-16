import { useEffect, useState } from "react";
import type { Hotel, HotelPayload } from "./types";
import { HotelService } from "./HotelService";
import HotelForm from "./HotelForm";

function Modal({ open, onClose, children }: { open: boolean; onClose: () => void; children: React.ReactNode }) {
    if (!open) return null;
    return (
        <div className="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
            <div className="w-full max-w-3xl bg-white rounded-2xl shadow p-6 relative">
                <button onClick={onClose} className="absolute right-3 top-3 text-gray-500">✕</button>
                {children}
            </div>
        </div>
    );
}

export default function HotelListPage() {
    const [hotels, setHotels] = useState<Hotel[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const [open, setOpen] = useState(false);
    const [editing, setEditing] = useState<Hotel | null>(null);

    const load = async () => {
        setLoading(true);
        setError(null);
        try {
            const list = await HotelService.getAll();
            setHotels(list ?? []);
        } catch (e: any) {
            setError(e?.response?.data?.message || "Error cargando hoteles");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { load(); }, []);

    const onCreate = () => { setEditing(null); setOpen(true); };
    const onEdit = (h: Hotel) => { setEditing(h); setOpen(true); };

    const onSave = async (payload: HotelPayload | Partial<HotelPayload>, id?: number) => {
        try {
            if (id) await HotelService.update(id, payload);
            else await HotelService.create(payload as HotelPayload);
            setOpen(false);
            await load();
        } catch (e: any) {
            alert(e?.response?.data?.message || "No se pudo guardar");
        }
    };

    const onDelete = async (id: number) => {
        if (!confirm("¿Eliminar este hotel?")) return;
        try {
            await HotelService.remove(id);
            await load();
        } catch (e: any) {
            alert(e?.response?.data?.message || "No se pudo eliminar");
        }
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h1 className="text-xl font-semibold">Hoteles</h1>
                <button onClick={onCreate} className="px-4 py-2 rounded-lg bg-blue-600 text-white">+ Nuevo hotel</button>
            </div>

            {loading && <p>Cargando...</p>}
            {error && <p className="text-red-600">{error}</p>}

            {!loading && hotels.length === 0 && <p>No hay hoteles registrados.</p>}

            {!loading && hotels.length > 0 && (
                <div className="overflow-auto rounded-xl border bg-white">
                    <table className="min-w-[720px] w-full text-sm">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="text-left p-3">Nombre</th>
                                <th className="text-left p-3">NIT</th>
                                <th className="text-left p-3">Ciudad</th>
                                <th className="text-left p-3">Rooms</th>
                                <th className="text-right p-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {hotels.map((h) => (
                                <tr key={h.id} className="border-t">
                                    <td className="p-3">{h.name}</td>
                                    <td className="p-3">{h.nit}</td>
                                    <td className="p-3">{h.city?.name || "-"}</td>
                                    <td className="p-3">{h.rooms_total}</td>
                                    <td className="p-3 text-right space-x-2">
                                        <button onClick={() => onEdit(h)} className="px-3 py-1 rounded-lg border">Editar</button>
                                        <button onClick={() => onDelete(h.id)} className="px-3 py-1 rounded-lg border text-red-600">Eliminar</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <Modal open={open} onClose={() => setOpen(false)}>
                <h2 className="text-lg font-semibold mb-4">{editing ? "Editar hotel" : "Nuevo hotel"}</h2>
                <HotelForm
                    initial={editing}
                    onCancel={() => setOpen(false)}
                    onSave={onSave}
                    hotelsSnapshot={hotels}
                />
            </Modal>
        </div>
    );
}
