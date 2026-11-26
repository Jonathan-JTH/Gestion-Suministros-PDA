# Evidencias Cap. VI (6.2) — Figuras 36, 37 y 38

Guía para capturas en **Cursor/VS Code** y **MySQL Workbench**. Proyecto monorepo: el “Laravel clásico” vive en **`backend/`**; la web pública en **`frontend/public/`**.

---

## Antes de capturar (seguridad)

- **Cerrar** la pestaña `.env` (nunca visible en captura).
- Colapsar **`vendor/`** (ocupa pantalla y no aporta a la tesis).
- Si aparece correo real en Workbench, no es crítico; evite mostrar contraseñas de conexión en el panel de conexión.

---

## Figura 36 — Entorno de desarrollo

### Qué abrir en Cursor

1. Abrir carpeta del repo: `gestion-suministros` (raíz).
2. En el explorador, expandir **`backend/`** (no solo la raíz suelta).
3. Dejar visibles estas entradas (como pide la ingeniera, equivalente Laravel):

| Carpeta / archivo | Ruta real |
|-------------------|-----------|
| `app/` | `backend/app/` |
| `database/` | `backend/database/` |
| `resources/` | `backend/resources/` (enlace a vistas del frontend) |
| `routes/` | `backend/routes/` |
| `public/` | **`frontend/public/`** (entrada HTTP; mencionar en leyenda) |
| `composer.json` | `backend/composer.json` |

4. Opcional en la misma captura (colapsado): **`frontend/`**, **`docs/`**, **`artisan`** en raíz — refuerza monorepo sin llenar la figura.

### Composición sugerida

- Panel izquierdo: árbol de carpetas.
- Panel derecho (opcional): `composer.json` abierto mostrando `"name"` / `"require"` con **laravel/framework** (sin secrets).

### Leyenda APA (copiar y ajustar número)

**Figura 36**  
*Estructura del entorno de desarrollo del sistema Gestión de Suministros (monorepo backend/frontend).*  

*Nota.* Elaboración propia, 2026. La aplicación Laravel 12 reside en `backend/`; los recursos públicos se sirven desde `frontend/public/`.

---

## Figura 37 — Estructura de módulos (`app/`)

### Lo que **sí existe** en tu proyecto (capturar solo esto)

Expandir **`backend/app/`** y **`backend/app/Http/`**:

```
app/
├── Console/          ← comandos artisan (VerificarSistema, ProbarCorreo…)
├── Http/
│   ├── Controllers/  ← Auth, Solicitud, Inventario, Reporte, Api/Auth…
│   └── Middleware/   ← Role, 2FA, permisos, JWT
├── Mail/             ← plantillas de correo
├── Models/           ← Eloquent (Usuario, Solicitud, Bitácora…)
├── Policies/         ← SolicitudPolicy
├── Providers/
├── Services/         ← Inventario, Notificaciones, 2FA, reCAPTCHA, JWT
└── Support/          ← Bitácora, PDF/gráficos reporte (capa de apoyo)
```

### Lo que **no existe** (no inventar en la tesis)

| Carpeta típica | En este repo |
|----------------|--------------|
| `Http/Requests/` | **No** — validación en controladores con `$request->validate()` |
| Varios Policies | Solo **`Policies/SolicitudPolicy.php`** |

### Composición sugerida

- Una captura del árbol **`app/`** completo.
- Segunda mini-captura o zoom: **`Http/Controllers/`** listando los 12 controladores (incluye `Api/AuthController.php`).

### Leyenda APA

**Figura 37**  
*Organización modular del directorio `app/` (capa de aplicación Laravel).*  

*Nota.* Elaboración propia, 2026. Los controladores concentran la lógica de presentación; servicios y modelos separan reglas de negocio y persistencia. No se utilizaron Form Requests en esta versión.

---

## Figura 38 — Base de datos implementada (MySQL)

### Dónde capturar

**MySQL Workbench** → conexión local → esquema **`gestion_suministros`** → pestaña **Tables** (lista completa, orden alfabético).

Alternativa: phpMyAdmin → base `gestion_suministros` → lista de tablas.

### Tablas para el pie de figura / párrafo 6.2

**11 entidades de negocio (MER Capítulo V — materializadas):**

1. `roles`  
2. `permisos`  
3. `rol_permiso`  
4. `usuarios`  
5. `sucursales`  
6. `suministros`  
7. `inventario`  
8. `solicitudes`  
9. `detalle_solicitudes`  
10. `movimiento_inventario`  
11. `bitácora`  

**Tablas de diseño de seguridad / ampliación (no cuentan como las 11 del MER, pero existen):**

- `desafios_2fa`  
- `codigos_2fa`  
- *(TOTP activo usa también columna `totp_secreto` en `usuarios`)*  

**Configuración operativa (fuera del MER académico):**

- `parametros_sistema`  

**Infraestructura Laravel (no forman parte del MER):**

- `migrations`  
- `cache`, `cache_locks`  
- `jobs`, `job_batches`, `failed_jobs`  
- `sessions`  
- `password_reset_tokens`  

### Texto listo para pegar bajo la figura (párrafo corto)

> La Figura 38 muestra el esquema físico `gestion_suministros` en MySQL 8. Las once tablas de negocio definidas en el Capítulo V se implementaron con los nombres indicados; las tablas `migrations`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions` y `password_reset_tokens` son componentes estándar de Laravel 12 y no integran el modelo entidad-relación académico. La tabla `parametros_sistema` almacena parámetros operativos (notificaciones). Las tablas `desafios_2fa` y `codigos_2fa` complementan el diseño de autenticación en dos factores.

### Leyenda APA

**Figura 38**  
*Tablas de la base de datos `gestion_suministros` en MySQL Workbench.*  

*Nota.* Elaboración propia, 2026.

---

## Checklist Semana 1 (ingeniera: solo 3 evidencias)

| # | Hecho | Archivo / ubicación |
|---|--------|---------------------|
| Fig. 36 | ☐ | Captura `backend/` + mención `frontend/public` |
| Fig. 37 | ☐ | Captura `backend/app/` + Controllers |
| Fig. 38 | ☐ | Workbench lista de tablas |

Insertar en Word **después** del párrafo de 6.2 que describe entorno y capas, **antes** de 6.3.

---

## Si Workbench no muestra tablas

Desde la raíz del repo:

```bash
php artisan migrate:fresh --seed
```

Vuelva a refrescar el esquema en Workbench (clic derecho → Refresh All).

---

## Árbol de referencia (Fig. 36 — texto)

```
gestion-suministros/
├── artisan
├── backend/
│   ├── app/
│   ├── bootstrap/
│   ├── composer.json
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   └── …
├── docs/
└── frontend/
    └── public/          ← index.php (servidor web)
```
