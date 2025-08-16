import { useEffect, useMemo, useState } from "react";
import type { Hotel, HotelPayload, RoomConfiguration } from "./types";

/** 🔧 Ajusta estos catálogos a tus IDs reales en BD */
const CITIES = [
  { id: 1, name: "CARTAGENA" },
  { id: 2, name: "BOGOTA" },
  { id: 3, name: "MEDELLIN" },
  { id: 4, name: "ARMENIA" },
];

const ACCOMMODATIONS = [
  { id: 1, name: "SENCILLA" },
  { id: 2, name: "DOBLE" },
  { id: 3, name: "TRIPLE" },
  { id: 4, name: "CUADRUPLE" },
];

const ROOM_TYPES = [
  { id: 1, name: "ESTANDAR", allowed: [1, 2] },   // SENCILLA, DOBLE
  { id: 2, name: "JUNIOR", allowed: [3, 4] },     // TRIPLE, CUADRUPLE
  { id: 3, name: "SUITE", allowed: [1, 2, 3] },   // SENCILLA, DOBLE, TRIPLE
];

type Props = {
    initial?: Hotel | null;
    hotelsSnapshot: Hotel[];                  // para validar dupes de NIT/Nombre
    onCancel: () => void;
    onSave: (payload: HotelPayload | Partial<HotelPayload>, id?: number) => Promise<void>;
};

export default function HotelForm({ initial, onCancel, onSave, hotelsSnapshot }: Props) {
    const editing = Boolean(initial?.id);

    const [form, setForm] = useState<HotelPayload>({
        name: initial?.name ?? "",
        address: initial?.address ?? "",
        nit: initial?.nit ?? "",
        rooms_total: initial?.rooms_total ?? 0,
        city_id: initial?.city?.id ?? initial?.city_id ?? CITIES[0].id,
        room_configurations: initial?.room_configurations ?? [],
    });

    const [errors, setErrors] = useState<string[]>([]);
    const sumQuantity = useMemo(
        () => form.room_configurations.reduce((acc, r) => acc + (Number(r.quantity) || 0), 0),
        [form.room_configurations]
    );

    useEffect(() => {
        validate();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [form]);

    const setField = (k: keyof HotelPayload, v: any) => setForm((f) => ({ ...f, [k]: v }));

    const addRow = () => {
        setField("room_configurations", [
            ...form.room_configurations,
            { room_type_id: ROOM_TYPES[0].id, accommodation_id: ROOM_TYPES[0].allowed[0], quantity: 1 },
        ]);
    };
    const removeRow = (i: number) => {
        const next = [...form.room_configurations];
        next.splice(i, 1);
        setField("room_configurations", next);
    };
    const updateRow = (i: number, patch: Partial<RoomConfiguration>) => {
        const next = [...form.room_configurations];
        next[i] = { ...next[i], ...patch };
        // corrige acomodaciones inválidas si cambian tipo
        if (patch.room_type_id) {
            const rt = ROOM_TYPES.find(r => r.id === patch.room_type_id)!;
            if (!rt.allowed.includes(next[i].accommodation_id)) {
                next[i].accommodation_id = rt.allowed[0];
            }
        }
        setField("room_configurations", next);
    };

    const validate = () => {
        const errs: string[] = [];

        // 1) Suma == rooms_total (el backend lo exige)
        if (sumQuantity !== Number(form.rooms_total)) {
            errs.push(`La suma de cantidades (${sumQuantity}) debe ser igual a rooms_total (${form.rooms_total}).`);
        }

        // 2) No duplicar par (room_type_id, accommodation_id)
        const seen = new Set<string>();
        for (const rc of form.room_configurations) {
            const key = `${rc.room_type_id}-${rc.accommodation_id}`;
            if (seen.has(key)) errs.push("No se permiten tipos de habitación y acomodación repetidos.");
            seen.add(key);
        }

        // 3) Combos permitidos
        for (const rc of form.room_configurations) {
            const rt = ROOM_TYPES.find(r => r.id === rc.room_type_id);
            if (!rt?.allowed.includes(rc.accommodation_id)) {
                errs.push("Hay combinaciones no permitidas según reglas del negocio.");
                break;
            }
        }

        // 4) No hoteles repetidos (por NIT o Nombre) en creación
        if (!editing) {
            const nitExists = hotelsSnapshot.some(h => h.nit.trim().toLowerCase() === form.nit.trim().toLowerCase());
            if (nitExists) errs.push("Ya existe un hotel con ese NIT.");
            const nameExists = hotelsSnapshot.some(h => h.name.trim().toLowerCase() === form.name.trim().toLowerCase());
            if (nameExists) errs.push("Ya existe un hotel con ese nombre.");
        } else {
            const nitExists = hotelsSnapshot.some(h => h.id !== initial!.id && h.nit.trim().toLowerCase() === form.nit.trim().toLowerCase());
            if (nitExists) errs.push("Ya existe otro hotel con ese NIT.");
            const nameExists = hotelsSnapshot.some(h => h.id !== initial!.id && h.name.trim().toLowerCase() === form.name.trim().toLowerCase());
            if (nameExists) errs.push("Ya existe otro hotel con ese nombre.");
        }

        setErrors(errs);
        return errs.length === 0;
    };

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!validate()) return;

        const payload: HotelPayload = {
            name: form.name.trim(),
            address: form.address.trim(),
            nit: form.nit.trim(),
            rooms_total: Number(form.rooms_total),
            city_id: Number(form.city_id),
            room_configurations: form.room_configurations.map(rc => ({
                room_type_id: Number(rc.room_type_id),
                accommodation_id: Number(rc.accommodation_id),
                quantity: Number(rc.quantity),
            })),
        };

        await onSave(payload, initial?.id);
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm mb-1">Nombre</label>
                    <input className="w-full border rounded-lg px-3 py-2" value={form.name} onChange={e => setField("name", e.target.value)} required />
                </div>
                <div>
                    <label className="block text-sm mb-1">NIT</label>
                    <input className="w-full border rounded-lg px-3 py-2" value={form.nit} onChange={e => setField("nit", e.target.value)} required />
                </div>
                <div>
                    <label className="block text-sm mb-1">Dirección</label>
                    <input className="w-full border rounded-lg px-3 py-2" value={form.address} onChange={e => setField("address", e.target.value)} required />
                </div>
                <div>
                    <label className="block text-sm mb-1">Ciudad</label>
                    <select
                        className="w-full border rounded-lg px-3 py-2"
                        value={form.city_id}
                        onChange={(e) => setField("city_id", Number(e.target.value))}
                    >
                        {CITIES.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                </div>
                <div>
                    <label className="block text-sm mb-1">Rooms total</label>
                    <input
                        type="number"
                        min={0}
                        className="w-full border rounded-lg px-3 py-2"
                        value={form.rooms_total}
                        onChange={(e) => setField("rooms_total", Number(e.target.value))}
                        required
                    />
                    <p className="text-xs text-gray-500 mt-1">Suma configurada: {sumQuantity}</p>
                </div>
            </div>

            <div>
                <div className="flex items-center justify-between mb-2">
                    <h3 className="font-semibold">Configuración de habitaciones</h3>
                    <button type="button" onClick={addRow} className="px-3 py-2 text-sm rounded-lg bg-blue-600 text-white">
                        + Agregar
                    </button>
                </div>

                <div className="space-y-2">
                    {form.room_configurations.map((rc, i) => {
                        const allowed = ROOM_TYPES.find(r => r.id === rc.room_type_id)!.allowed;
                        return (
                            <div key={i} className="grid grid-cols-12 gap-2 items-center">
                                <div className="col-span-4">
                                    <label className="block text-xs mb-1">Tipo</label>
                                    <select
                                        className="w-full border rounded-lg px-3 py-2"
                                        value={rc.room_type_id}
                                        onChange={(e) => updateRow(i, { room_type_id: Number(e.target.value) })}
                                    >
                                        {ROOM_TYPES.map(rt => <option key={rt.id} value={rt.id}>{rt.name}</option>)}
                                    </select>
                                </div>
                                <div className="col-span-4">
                                    <label className="block text-xs mb-1">Acomodación</label>
                                    <select
                                        className="w-full border rounded-lg px-3 py-2"
                                        value={rc.accommodation_id}
                                        onChange={(e) => updateRow(i, { accommodation_id: Number(e.target.value) })}
                                    >
                                        {ACCOMMODATIONS.filter(a => allowed.includes(a.id)).map(a =>
                                            <option key={a.id} value={a.id}>{a.name}</option>
                                        )}
                                    </select>
                                </div>
                                <div className="col-span-3">
                                    <label className="block text-xs mb-1">Cantidad</label>
                                    <input
                                        type="number"
                                        min={1}
                                        className="w-full border rounded-lg px-3 py-2"
                                        value={rc.quantity}
                                        onChange={(e) => updateRow(i, { quantity: Number(e.target.value) })}
                                    />
                                </div>
                                <div className="col-span-1 pt-6">
                                    <button type="button" onClick={() => removeRow(i)} className="w-full border rounded-lg px-2 py-2">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            {errors.length > 0 && (
                <div className="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 text-sm space-y-1">
                    {errors.map((e, i) => <p key={i}>• {e}</p>)}
                </div>
            )}

            <div className="flex gap-2 justify-end">
                <button type="button" onClick={onCancel} className="px-4 py-2 rounded-lg border">Cancelar</button>
                <button type="submit" className="px-4 py-2 rounded-lg bg-green-600 text-white">
                    {editing ? "Guardar cambios" : "Crear hotel"}
                </button>
            </div>
        </form>
    );
}
