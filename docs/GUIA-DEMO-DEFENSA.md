# Guion de demo (defensa / Fabrigas)

Duración sugerida: **15–20 minutos**. Tener MySQL y `php artisan serve` en marcha; SMTP configurado o explicar modo `log`.

## Antes de empezar

```bash
php artisan config:clear
php artisan sistema:verificar
```

Usuarios seed: ver [GUIA-PROYECTO-FUNCIONAL.md](GUIA-PROYECTO-FUNCIONAL.md).

## 1. Seguridad de acceso (2–3 min)

1. Mostrar **login** (reCAPTCHA si `RECAPTCHA_ENABLED=true`).
2. Entrar como **sucursal** → menú reducido (consulta + nueva solicitud).
3. Cerrar sesión; entrar como **soporte/admin** → panel completo, **2FA** si aplica.

## 2. Flujo sucursal (4 min)

1. **Nueva solicitud**: elegir suministro, cantidad, observación → registrar.
2. Mencionar correo a TI + confirmación (o bitácora / log si SMTP off).
3. **Consulta de solicitudes**: buscar por número, ver detalle.

## 3. Gestión TI (5 min)

1. **Gestión de solicitudes**: filtros por estado y sucursal.
2. **Pendiente** → poner en proceso → **Atender** (despacho, nota, correos).
3. Abrir **detalle** de solicitud atendida: auditoría de despacho.

## 4. Inventario y catálogo (3 min)

1. **Suministros**: catálogo, filtros, alta/edición (formulario).
2. **Inventario**: filtros, stock bajo, entrada/ajuste rápido.
3. **Trazabilidad**: movimiento ligado a solicitud si aplica.

## 5. Gobierno y reportes (3 min)

1. **Bitácora**: filtros (módulo, acción, fechas).
2. **Reportes**: generar consumo o solicitudes → gráfico + PDF.
3. **Configuración**: interruptores de notificación (admin).

## 6. Cierre (1 min)

- Monorepo `backend/` + `frontend/`, MySQL según MER Cap. V.
- Bitácora de negocio vs `laravel.log` técnico.

## Capturas útiles para la tesis

- Login (+ reCAPTCHA si lo usan en producción).
- Listados con filtros (solicitudes, inventario, bitácora).
- Modal atender + detalle atendida.
- PDF de reporte y panel con KPIs.
