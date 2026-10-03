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

## reCAPTCHA (login)

Protección opcional en la pantalla de inicio de sesión (Google reCAPTCHA **v2 checkbox**).

1. Crear sitio en [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin) (dominios: `localhost`, su URL de demo).
2. En `backend/.env`:

```env
RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=su_clave_sitio
RECAPTCHA_SECRET_KEY=su_clave_secreta
```

3. `php artisan config:clear`

Con `RECAPTCHA_ENABLED=false` o sin claves, el login funciona **sin** widget.

Claves creadas en **Google Cloud** (pantalla con “CreateAssessment”): `RECAPTCHA_ENTERPRISE=true`.

**Tipo de integración** (`RECAPTCHA_INTEGRATION`):

| Valor | Cuándo usarlo |
|--------|----------------|
| `enterprise_v3` | Clave **basada en puntuación (v3)** — sin casilla; verificación al pulsar Ingresar |
| `enterprise_v2` | Casilla «No soy un robot» con claves Cloud (checkbox) |
| `classic_v2` | Casilla clásica + claves del admin antiguo (`api.js`) |

Dominios en Google: **`localhost`** y **`127.0.0.1`**.

**Windows — “No se pudo contactar a Google reCAPTCHA”** con la casilla bien: PHP no tiene certificados CA (cURL 60). En `APP_ENV=local` el sistema omite verificación SSL hacia Google. En producción instale [cacert.pem](https://curl.se/ca/cacert.pem) en `php.ini` (`curl.cainfo` / `openssl.cafile`) o defina `RECAPTCHA_VERIFY_SSL=true` solo cuando eso esté configurado.

**Aviso rojo en el checkbox** (*“This reCAPTCHA is for testing purposes only…”*): aparece con las **claves de prueba** de Google (las del `.env.example`). No es fallo del sistema. Para quitarlo, cree un sitio en reCAPTCHA Admin (v2 «No soy un robot»), agregue `localhost` y su dominio, y reemplace `RECAPTCHA_SITE_KEY` y `RECAPTCHA_SECRET_KEY` en `.env`.

## Flujo demo (defensa)

Guion detallado: [GUIA-DEMO-DEFENSA.md](GUIA-DEMO-DEFENSA.md).


1. **Sucursal** → Nueva solicitud → correo TI + confirmación al solicitante.
2. **Soporte** → Solicitudes → En proceso → **Atender** → correo manual opcional (“su solicitud ha sido atendida”) → inventario actualizado.
3. **Dashboard**, **Reportes** (5 tipos + PDF), **Bitácora**, **Trazabilidad**.

## API JWT (opcional)

`POST /api/auth/login` → `POST /api/auth/2fa` → `GET /api/auth/me` con header `Authorization: Bearer ...`

Definir `JWT_SECRET` en `.env`.

## Documento de tesis

Ver `docs/Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx` y `COMPARATIVO-CAMBIOS-DOCUMENTO.md`.
