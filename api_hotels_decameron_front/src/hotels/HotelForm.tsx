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
import type { Hotel, HotelPayload, RoomConfiguration } from "./types";
import {motion} from 'framer-motion';

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
  hotelsSnapshot?: Hotel[];
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
  hotelsSnapshot = [],
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
  const [isSubmitting, setIsSubmitting] = useState(false);

  const sumQuantity = useMemo(
    () =>
      form.room_configurations.reduce(
        (acc, r) => acc + (Number(r.quantity) || 0),
        0
      ),
    [form.room_configurations]
  );
  
  // ⚠️ Validación en vivo
  useEffect(() => {
    const errs: string[] = [];
    
    if (!form.name.trim()) errs.push("El nombre es obligatorio.");
    if (!form.nit.trim()) errs.push("El NIT es obligatorio.");
    
    // 💡 Validación del NIT actualizada a 5 y 20 caracteres
    const nitLength = form.nit.trim().length;
    if (nitLength < 5 || nitLength > 20) {
      errs.push("El NIT debe tener entre 5 y 20 caracteres.");
    }
    
    if (!form.address.trim()) errs.push("La dirección es obligatoria.");
    if (form.rooms_total <= 0) errs.push("El total de habitaciones es obligatorio y debe ser mayor a 0.");
    
    if (sumQuantity !== Number(form.rooms_total)) {
      errs.push(
        `La suma de cantidades (${sumQuantity}) debe ser igual al total de habitaciones (${form.rooms_total}).`
      );
    }
    
    if (form.room_configurations.length === 0) {
        errs.push("Debe configurar al menos un tipo de habitación.");
    } else {
        const seen = new Set<string>();
        form.room_configurations.forEach((rc, index) => {
            const key = `${rc.room_type_id}-${rc.accommodation_id}`;
            if (seen.has(key)) {
                errs.push(`No se permiten tipos de habitación y acomodación duplicados en la fila ${index + 1}.`);
            }
            seen.add(key);
    
            // 💡 Validación reforzada para asegurar que los campos no estén vacíos
            if (!rc.room_type_id || !rc.accommodation_id) {
                errs.push(`El tipo de habitación y acomodación son obligatorios en la fila ${index + 1}.`);
            }
            if (rc.quantity <= 0) {
                errs.push(`La cantidad debe ser mayor a 0 en la fila ${index + 1}.`);
            }

            const rt = ROOM_TYPES.find((r) => r.id === rc.room_type_id);
            if (rt && !rt.allowed.includes(rc.accommodation_id)) {
                errs.push(`La combinación de habitación y acomodación en la fila ${index + 1} no es válida.`);
            }
        });
    }

    if (Array.isArray(hotelsSnapshot)) {
        const uniqueCheck = (hotel: Hotel) => 
            (hotel.nit.trim().toLowerCase() === form.nit.trim().toLowerCase() ||
             hotel.name.trim().toLowerCase() === form.name.trim().toLowerCase());

        if (editing) {
            const conflictingHotel = hotelsSnapshot.find(h => h.id !== initial!.id && uniqueCheck(h));
            if (conflictingHotel) errs.push(`Ya existe un hotel con este NIT o nombre.`);
        } else {
            const existingHotel = hotelsSnapshot.find(uniqueCheck);
            if (existingHotel) errs.push("Ya existe un hotel con este NIT o nombre.");
        }
    }

    setErrors(errs);
  }, [form, sumQuantity, hotelsSnapshot, editing, initial]);

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

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    
    if (errors.length > 0) {
      setIsSubmitting(false);
      return;
    }
    
    const payload: HotelPayload = {
      name: form.name.trim(),
      address: form.address.trim(),
      nit: form.nit.trim(),
      rooms_total: Number(form.rooms_total),
      city_id: Number(form.city_id),
      room_configurations: form.room_configurations.map((rc) => ({
            room_type_id: rc.room_type_id,
            accommodation_id: rc.accommodation_id,
            quantity: Number(rc.quantity),
        })),
    };

    try {
      await onSave(payload, initial?.id);
    } catch (saveError) {
      console.error("Error al guardar el hotel:", saveError);
      setErrors(["No se pudo guardar el hotel. Intenta de nuevo más tarde."]);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Box
      component="form"
      onSubmit={submit}
      noValidate
      sx={{
        p: 4,
        bgcolor: 'background.paper',
        borderRadius: 'rounded-2xl',
        boxShadow: 'shadow-lg',
      }}
    >
      {errors.length > 0 && (
        <Alert severity="error" sx={{ mb: 2 }}>
          <ul style={{ margin: 0, paddingLeft: 20 }}>
            {errors.map((err, i) => (
              <li key={i}>{err}</li>
            ))}
          </ul>
        </Alert>
      )}

      <Box sx={{ display: 'grid', gap: 2, gridTemplateColumns: '1fr 1fr', mb: 2 }}>
        <TextField
          label="Nombre *"
          value={form.name}
          onChange={(e) => setField('name', e.target.value)}
          fullWidth
          required
          variant="outlined"
          error={errors.some(e => e.includes("nombre"))}
        />
        <TextField
          label="NIT *"
          value={form.nit}
          onChange={(e) => setField('nit', e.target.value)}
          fullWidth
          required
          variant="outlined"
          error={errors.some(e => e.includes("NIT"))}
        />
        <TextField
          label="Dirección *"
          value={form.address}
          onChange={(e) => setField('address', e.target.value)}
          fullWidth
          required
          variant="outlined"
          error={errors.some(e => e.includes("dirección"))}
        />
        <TextField
          select
          label="Ciudad"
          value={form.city_id}
          onChange={(e) => setField('city_id', Number(e.target.value))}
          fullWidth
          variant="outlined"
        >
          {CITIES.map((city) => (
            <MenuItem key={city.id} value={city.id}>
              {city.name}
            </MenuItem>
          ))}
        </TextField>
      </Box>
      <TextField
        label="Rooms total *"
        type="number"
        value={form.rooms_total}
        onChange={(e) => setField('rooms_total', Number(e.target.value))}
        fullWidth
        required
        variant="outlined"
        sx={{ mb: 2 }}
        error={errors.some(e => e.includes("total de habitaciones"))}
      />

      <Typography variant="body1" className="text-lg">
        Suma configurada: {sumQuantity}
      </Typography>

      <Typography variant="h6" mt={4} mb={2} className="text-xl">
        Configuración de habitaciones
      </Typography>
      
      {form.room_configurations.map((rc, i) => (
        <motion.div
            key={i}
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 20 }}
            transition={{ duration: 0.3 }}
        >
          <Paper
            sx={{
              p: 2,
              mb: 2,
              display: "flex",
              gap: 2,
              alignItems: "center",
              boxShadow: 'shadow-md',
              borderRadius: 'rounded-xl',
            }}
          >
            <TextField
              select
              label="Tipo"
              value={rc.room_type_id || ''}
              onChange={(e) => updateRow(i, { room_type_id: Number(e.target.value) })}
              sx={{ flex: 1 }}
              required
              error={errors.some(e => e.includes(`habitación en la fila ${i + 1}`))}
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
              value={rc.accommodation_id || ''}
              onChange={(e) => updateRow(i, { accommodation_id: Number(e.target.value) })}
              sx={{ flex: 1 }}
              required
              error={errors.some(e => e.includes(`acomodación en la fila ${i + 1}`))}
            >
              {ROOM_TYPES.find((r) => r.id === rc.room_type_id)?.allowed.map((id) => {
                const accommodation = ACCOMMODATIONS.find(a => a.id === id);
                return (
                  <MenuItem key={id} value={id}>
                    {accommodation?.name}
                  </MenuItem>
                );
              })}
            </TextField>
            <TextField
              type="number"
              label="Cantidad"
              value={rc.quantity}
              onChange={(e) => updateRow(i, { quantity: Number(e.target.value) })}
              sx={{ width: 120 }}
              required
              error={errors.some(e => e.includes(`cantidad debe ser mayor a 0`))}
            />
            <IconButton onClick={() => removeRow(i)} color="error" aria-label="eliminar">
              <DeleteIcon />
            </IconButton>
          </Paper>
        </motion.div>
      ))}

      <Box sx={{ display: 'flex', justifyContent: 'flex-end', mt: 2, mb: 4 }}>
        <Button onClick={addRow} variant="outlined" startIcon={<i className="fas fa-plus" />}>
          + AGREGAR
        </Button>
      </Box>

      <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2 }}>
        <Button onClick={onCancel} variant="outlined" color="secondary" size="large">
          CANCELAR
        </Button>
        <Button
          type="submit"
          variant="contained"
          color="primary"
          size="large"
          disabled={isSubmitting || errors.length > 0}
        >
          {isSubmitting ? "GUARDANDO..." : "CREAR HOTEL"}
        </Button>
      </Box>
    </Box>
  );
}