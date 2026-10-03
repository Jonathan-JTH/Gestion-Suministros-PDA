# Gestión de Suministros — guía funcional

## Requisitos

- PHP 8.2+, Composer, MySQL 8+
- Extensiones PHP: `pdo_mysql`, `openssl`, `mbstring`

## Estructura backend / frontend

- **`backend/`** — acceso directo a `app`, `database`, `routes`, `config` (misma base de datos MySQL del Cap. V).
- **`frontend/`** — acceso directo a `views`, `css`, `js`, `public` (interfaz Bootstrap).

Los comandos `artisan` se ejecutan desde la **raíz** del repositorio (convención Laravel).

## Instalación rápida

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configurar DB_* en .env
php artisan migrate:fresh --seed
php artisan serve
```

## Usuarios de prueba (seeder)

| Rol | Correo | Contraseña | 2FA |
|-----|--------|------------|-----|
| Admin | admin@suministros.local | admin123 | Sí (configurar en primer login) |
| Soporte | soporte@suministros.local | soporte123 | Sí |
| Sucursal | sucursal@suministros.local | sucursal123 | No |

## Correos (notificaciones)

En `.env`:

```env
NOTIFICACION_SOPORTE_EMAIL=soporteti@grupofabrigas.com
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu@gmail.com
MAIL_PASSWORD=contraseña_de_aplicacion
MAIL_FROM_ADDRESS=tu@gmail.com
```

Si **no** hay `MAIL_USERNAME`, el sistema usa `log` (ver `storage/logs/laravel.log`).

```bash
php artisan config:clear
php artisan notificacion:probar-correo
php artisan sistema:verificar
```

**Configuración → Notificaciones** (admin): activar/desactivar envíos y aviso al destinatario al despachar. Copia a TI obligatoria cuando correos están activos.

## Flujo demo (defensa)

1. **Sucursal** → Nueva solicitud → correo TI + confirmación al solicitante.
2. **Soporte** → Solicitudes → En proceso → **Atender** → correo manual opcional (“su solicitud ha sido atendida”) → inventario actualizado.
3. **Dashboard**, **Reportes** (5 tipos + PDF), **Bitácora**, **Trazabilidad**.

## API JWT (opcional)

`POST /api/auth/login` → `POST /api/auth/2fa` → `GET /api/auth/me` con header `Authorization: Bearer ...`

Definir `JWT_SECRET` en `.env`.

## Documento de tesis

Ver `docs/Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx` y `COMPARATIVO-CAMBIOS-DOCUMENTO.md`.
