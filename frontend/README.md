# Frontend — Gestión de Suministros

Capa **interfaz gráfica**: vistas Blade, CSS/JS y carpeta pública.

| Carpeta | Contenido |
|---------|-----------|
| `resources/views/` | Pantallas (login, dashboard, solicitudes, inventario, reportes…) |
| `resources/css/`, `resources/js/` | Estilos y scripts (Vite) |
| `public/` | `index.php` (arranca Laravel en `backend/`), favicon, `.htaccess` |

## Módulos de pantalla

- `auth/` — Login y segundo factor (TOTP)
- `solicitudes/` — Sucursal y gestión TI
- `suministros/`, `inventario/`, `usuarios/`
- `reportes/`, `bitacora/`, `trazabilidad/`, `configuracion/`
- `layouts/app.blade.php` — Menú lateral Bootstrap
- `emails/` — Plantillas de correo

Bootstrap 5 e iconos vía CDN en el layout principal.
