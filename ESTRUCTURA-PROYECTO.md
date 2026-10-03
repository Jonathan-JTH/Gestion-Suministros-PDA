# Estructura del proyecto

## Qué ver en la raíz (entrega / defensa)

```
gestion-suministros/
├── backend/     ← Laravel: app, config, database, routes, .env, vendor
├── frontend/    ← Interfaz: resources (vistas, CSS, JS), public (index.php)
├── docs/        ← Tesis y guías
├── artisan      ← Atajo: ejecuta backend/artisan
└── README.md
```

No hace falta abrir carpetas sueltas en la raíz: **todo el código vive en `backend/` o `frontend/`**.

## Backend (`backend/`)

| Contenido | Para qué |
|-----------|----------|
| `app/` | Controladores, modelos, servicios, correos |
| `database/` | Migraciones y seeders (MySQL del Cap. V) |
| `config/`, `routes/` | Configuración y rutas web/API |
| `.env` | Base de datos, JWT, correo |
| `resources/` | Enlace a `frontend/resources` (Laravel encuentra las vistas ahí) |

## Frontend (`frontend/`)

| Contenido | Para qué |
|-----------|----------|
| `resources/views/` | Pantallas Blade (Bootstrap) |
| `resources/css`, `resources/js` | Estilos y scripts (Vite) |
| `public/` | Punto de entrada web (`index.php` → arranca `backend/`) |

## Comandos (desde la raíz del repo)

```bash
php artisan serve
php artisan sistema:verificar
php artisan migrate:fresh --seed
```

`php artisan serve` usa la carpeta pública en `frontend/public`.

Para assets en desarrollo, desde `backend/`:

```bash
cd backend
npm install
npm run dev
```

(O deja `package.json` en la raíz si aún no lo moviste; lo importante es ejecutar Vite con la config en `backend/vite.config.js`.)

## Resumen para el informe

| Capa | Carpeta | Contenido |
|------|---------|-----------|
| Backend + BD | `backend/` | Lógica, API, MySQL |
| Frontend | `frontend/` | Interfaz gráfica |
| Documentación | `docs/` | Word actualizado, guías |
