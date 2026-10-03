# Backend — Gestión de Suministros

Capa **servidor**: API, reglas de negocio, autenticación, correos y **base de datos**.

| Carpeta | Contenido |
|---------|-----------|
| `app/` | Controladores, modelos, servicios, policies, mail |
| `database/` | Migraciones, seeders, factories (MySQL) |
| `routes/` | Rutas web y API |
| `config/` | JWT, correo, notificaciones |
| `bootstrap/` | Arranque (incluye ruta pública hacia `frontend/public`) |
| `resources/` | Enlace a `../frontend/resources` (vistas y assets para Laravel) |
| `.env` | Base de datos y SMTP |

## Comandos

Desde la **raíz** del repo:

```bash
php artisan migrate:fresh --seed
php artisan sistema:verificar
php artisan serve
```

Instalación PHP (solo una vez):

```bash
cd backend
composer install
cp .env.example .env
```

Assets (Vite), desde `backend/`:

```bash
npm install
npm run dev
```
