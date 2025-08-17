// src/hotels/HotelForm.tsx
import { useEffect, useMemo, useState } from "react";
import {
  TextField,
  Button,
  Typography,
  Box,
  IconButton,
  MenuItem,
  Paper,
  Alert,
} from "@mui/material";
import DeleteIcon from "@mui/icons-material/Delete";
import { Grid } from '@mui/material';
import type { Hotel, HotelPayload, RoomConfiguration } from "./types";

// 🔧 Ajusta según tu backend
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
  { id: 1, name: "ESTANDAR", allowed: [1, 2] },
  { id: 2, name: "JUNIOR", allowed: [3, 4] },
  { id: 3, name: "SUITE", allowed: [1, 2, 3] },
];

type Props = {
  initial?: Hotel | null;
  hotelsSnapshot: Hotel[];
  onCancel: () => void;
  onSave: (
    payload: HotelPayload | Partial<HotelPayload>,
    id?: number
  ) => Promise<void>;
};

export default function HotelForm({
  initial,
  onCancel,
  onSave,
  hotelsSnapshot,
}: Props) {
  const editing = Boolean(initial?.id);

  const [form, setForm] = useState<HotelPayload>({
    name: initial?.name ?? "",
    address: initial?.address ?? "",
    nit: initial?.nit ?? "",
    rooms_total: initial?.rooms_total ?? 0,
    city_id:
      initial?.city_id !== undefined
        ? Number(initial.city_id)
        : CITIES[0].id,
    room_configurations: initial?.room_configurations ?? [],
  });

  const [errors, setErrors] = useState<string[]>([]);

  const sumQuantity = useMemo(
    () =>
      form.room_configurations.reduce(
        (acc, r) => acc + (Number(r.quantity) || 0),
        0
      ),
    [form.room_configurations]
  );

  useEffect(() => {
    validate();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [form]);

  const setField = (k: keyof HotelPayload, v: any) =>
    setForm((f) => ({ ...f, [k]: v }));

  const addRow = () => {
    setField("room_configurations", [
      ...form.room_configurations,
      {
        room_type_id: ROOM_TYPES[0].id,
        accommodation_id: ROOM_TYPES[0].allowed[0],
        quantity: 1,
      },
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
    if (patch.room_type_id) {
      const rt = ROOM_TYPES.find((r) => r.id === patch.room_type_id)!;
      if (!rt.allowed.includes(next[i].accommodation_id)) {
        next[i].accommodation_id = rt.allowed[0];
      }
    }
    setField("room_configurations", next);
  };

  const validate = () => {
    const errs: string[] = [];

    if (sumQuantity !== Number(form.rooms_total)) {
      errs.push(
        `La suma de cantidades (${sumQuantity}) debe ser igual a rooms_total (${form.rooms_total}).`
      );
    }

    const seen = new Set<string>();
    for (const rc of form.room_configurations) {
      const key = `${rc.room_type_id}-${rc.accommodation_id}`;
      if (seen.has(key))
        errs.push(
          "No se permiten tipos de habitación y acomodación repetidos."
        );
      seen.add(key);
    }

    for (const rc of form.room_configurations) {
      const rt = ROOM_TYPES.find((r) => r.id === rc.room_type_id);
      if (!rt?.allowed.includes(rc.accommodation_id)) {
        errs.push("Hay combinaciones no permitidas según reglas del negocio.");
        break;
      }
    }

    if (!editing) {
      const nitExists = hotelsSnapshot.some(
        (h) =>
          h.nit.trim().toLowerCase() === form.nit.trim().toLowerCase()
      );
      if (nitExists) errs.push("Ya existe un hotel con ese NIT.");
      const nameExists = hotelsSnapshot.some(
        (h) =>
          h.name.trim().toLowerCase() === form.name.trim().toLowerCase()
      );
      if (nameExists) errs.push("Ya existe un hotel con ese nombre.");
    } else {
      const nitExists = hotelsSnapshot.some(
        (h) =>
          h.id !== initial!.id &&
          h.nit.trim().toLowerCase() === form.nit.trim().toLowerCase()
      );
      if (nitExists) errs.push("Ya existe otro hotel con ese NIT.");
      const nameExists = hotelsSnapshot.some(
        (h) =>
          h.id !== initial!.id &&
          h.name.trim().toLowerCase() === form.name.trim().toLowerCase()
      );
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
      room_configurations: form.room_configurations.map((rc) => ({
        room_type_id: Number(rc.room_type_id),
        accommodation_id: Number(rc.accommodation_id),
        quantity: Number(rc.quantity),
      })),
    };

    await onSave(payload, initial?.id);
  };

  return (
    <Box component="form" onSubmit={submit} noValidate sx={{ mt: 2 }}>
      <Grid container spacing={2}>
        <Grid item xs={12} md={6}>
          <TextField
            fullWidth
            label="Nombre"
            value={form.name}
            onChange={(e) => setField("name", e.target.value)}
            required
          />
        </Grid>
        <Grid item xs={12} md={6}>
          <TextField
            fullWidth
            label="NIT"
            value={form.nit}
            onChange={(e) => setField("nit", e.target.value)}
            required
          />
        </Grid>
        <Grid item xs={12} md={6}>
          <TextField
            fullWidth
            label="Dirección"
            value={form.address}
            onChange={(e) => setField("address", e.target.value)}
            required
          />
        </Grid>
        <Grid item xs={12} md={6}>
          <TextField
            select
            fullWidth
            label="Ciudad"
            value={form.city_id}
            onChange={(e) => setField("city_id", Number(e.target.value))}
          >
            {CITIES.map((c) => (
              <MenuItem key={c.id} value={c.id}>
                {c.name}
              </MenuItem>
            ))}
          </TextField>
        </Grid>
        <Grid item xs={12} md={6}>
          <TextField
            type="number"
            fullWidth
            label="Rooms total"
            value={form.rooms_total}
            onChange={(e) => setField("rooms_total", Number(e.target.value))}
            required
          />
          <Typography variant="caption" color="text.secondary">
            Suma configurada: {sumQuantity}
          </Typography>
        </Grid>
      </Grid>

      <Box mt={3}>
        <Box
          sx={{ display: "flex", justifyContent: "space-between", mb: 2 }}
        >
          <Typography variant="h6">Configuración de habitaciones</Typography>
          <Button variant="contained" onClick={addRow}>
            + Agregar
          </Button>
        </Box>

        {form.room_configurations.map((rc, i) => {
          const allowed = ROOM_TYPES.find((r) => r.id === rc.room_type_id)!
            .allowed;
          return (
            <Paper
              key={i}
              sx={{
                p: 2,
                mb: 2,
                display: "flex",
                alignItems: "center",
                gap: 2,
              }}
            >
              <TextField
                select
                label="Tipo"
                value={rc.room_type_id}
                onChange={(e) =>
                  updateRow(i, { room_type_id: Number(e.target.value) })
                }
                sx={{ flex: 1 }}
              >
                {ROOM_TYPES.map((rt) => (
                  <MenuItem key={rt.id} value={rt.id}>
                    {rt.name}
                  </MenuItem>
                ))}
              </TextField>

              <TextField
                select
                label="Acomodación"
                value={rc.accommodation_id}
                onChange={(e) =>
                  updateRow(i, {
                    accommodation_id: Number(e.target.value),
                  })
                }
                sx={{ flex: 1 }}
              >
                {ACCOMMODATIONS.filter((a) => allowed.includes(a.id)).map(
                  (a) => (
                    <MenuItem key={a.id} value={a.id}>
                      {a.name}
                    </MenuItem>
                  )
                )}
              </TextField>

              <TextField
                type="number"
                label="Cantidad"
                value={rc.quantity}
                onChange={(e) =>
                  updateRow(i, { quantity: Number(e.target.value) })
                }
                sx={{ width: 120 }}
              />

              <IconButton onClick={() => removeRow(i)} color="error">
                <DeleteIcon />
              </IconButton>
            </Paper>
          );
        })}
      </Box>

      {errors.length > 0 && (
        <Alert severity="error" sx={{ mt: 2 }}>
          {errors.map((e, i) => (
            <div key={i}>• {e}</div>
          ))}
        </Alert>
      )}

      <Box mt={3} sx={{ display: "flex", justifyContent: "flex-end", gap: 2 }}>
        <Button variant="outlined" onClick={onCancel}>
          Cancelar
        </Button>
        <Button type="submit" variant="contained" color="success">
          {editing ? "Guardar cambios" : "Crear hotel"}
        </Button>
      </Box>
    </Box>
  );
}
