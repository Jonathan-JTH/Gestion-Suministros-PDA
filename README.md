# Gestión de Suministros

Sistema web (Laravel 12, MySQL) para solicitudes de suministros por sucursal, gestión TI, inventario, reportes, bitácora y notificaciones por correo.

Repositorio: **https://github.com/Jonathan-JTH/Gestion-Suministros-PDA.git**

## Estructura

| Carpeta | Rol |
|---------|-----|
| **[backend/](backend/)** | Laravel: app, config, **base de datos** (migraciones/seeders), `.env` |
| **[frontend/](frontend/)** | Interfaz: vistas Blade, CSS/JS, `public/` (entrada web) |
| **[docs/](docs/)** | Guías y documento de tesis |

Detalle: [ESTRUCTURA-PROYECTO.md](ESTRUCTURA-PROYECTO.md) · Operación: [docs/GUIA-PROYECTO-FUNCIONAL.md](docs/GUIA-PROYECTO-FUNCIONAL.md) · Demo defensa: [docs/GUIA-DEMO-DEFENSA.md](docs/GUIA-DEMO-DEFENSA.md)

---

## Requisitos (qué instalar en el equipo o servidor)

Instale **antes** de clonar:

| Software | Versión recomendada | Para qué |
|----------|---------------------|----------|
| **Git** | 2.x | Clonar y actualizar el repo |
| **PHP** | 8.2 o superior | Backend Laravel |
| **Composer** | 2.x | Dependencias PHP (`backend/vendor`) |
| **MySQL** | 8.0+ (o MariaDB compatible) | Base de datos `gestion_suministros` |
| **Node.js** | 20+ (opcional) | Compilar assets con Vite (`npm run build`) |

Extensiones PHP necesarias (habilitar en `php.ini`):

- `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

En Windows suele usarse [XAMPP/Laragon](https://laragon.org/) o PHP + MySQL por separado. En Linux: paquetes `php8.2-cli`, `php8.2-mysql`, etc.

**Servicios que deben estar en ejecución** al usar la app:

1. **MySQL** (puerto 3306 por defecto).
2. **Servidor web PHP** para desarrollo: `php artisan serve` (puerto 8000), o Apache/Nginx apuntando a `frontend/public` en producción.

No hace falta Redis ni cola externa para la demo; el proyecto usa sesión/caché en base de datos por defecto.

---

## Clonar el proyecto

```bash
git clone https://github.com/Jonathan-JTH/Gestion-Suministros-PDA.git
cd Gestion-Suministros-PDA
```

Si ya tenía una copia antigua, actualice con:

```bash
git pull origin master
```

---

## Instalación (primera vez)

### 1. Base de datos MySQL

Cree la base (Workbench, phpMyAdmin o consola):

```sql
CREATE DATABASE gestion_suministros CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Dependencias PHP

```bash
cd backend
composer install
```

En **Windows** (PowerShell), copie la plantilla de entorno:

```powershell
Copy-Item .env.example .env
```

En Linux/macOS:

```bash
cp .env.example .env
```

Edite **`backend/.env`**: al menos `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` y, si usará correos, `MAIL_*` y `NOTIFICACION_SOPORTE_EMAIL` (ver [GUIA](docs/GUIA-PROYECTO-FUNCIONAL.md)).

### 3. Laravel (desde la raíz del repo)

El archivo **`artisan`** en la raíz delega en `backend/artisan`.

```bash
cd ..
php artisan key:generate
php artisan migrate:fresh --seed
php artisan sistema:verificar
```

### 4. Levantar la aplicación

```bash
php artisan serve
```

Abrir **http://127.0.0.1:8000**

### 5. Assets front (opcional)

Si modificó CSS/JS o despliega en producción:

```bash
npm install
npm run build
```

Para desarrollo con recarga en caliente: `npm run dev` (en otra terminal, desde la raíz).

---

## Usuarios de prueba (seeder)

| Rol | Correo | Contraseña |
|-----|--------|------------|
| Admin | admin@suministros.local | admin123 |
| Soporte | soporte@suministros.local | soporte123 |
| Sucursal | sucursal@suministros.local | sucursal123 |

Admin y soporte configuran **2FA (Google Authenticator)** en el primer acceso.

**reCAPTCHA v2** en login (opcional): variables `RECAPTCHA_*` en `backend/.env`; ver guía funcional. Desactivado por defecto en desarrollo.

---

## Comandos útiles

```bash
php artisan sistema:verificar
php artisan notificacion:probar-correo
php artisan config:clear
php artisan migrate:fresh --seed
```

---

## Migrar a otro PC o servidor

1. Clonar el repo (sección anterior).
2. Instalar los **mismos requisitos** (PHP, Composer, MySQL).
3. `composer install` en `backend/`, crear `.env` desde `.env.example`.
4. Importar o recrear BD con `php artisan migrate --seed` (o respaldo `.sql` si lo generaron).
5. **No subir** `backend/.env` ni `backend/vendor` a Git; se generan en cada máquina.
6. Producción: apuntar el virtual host a **`frontend/public`** y configurar SMTP real (buzón TI).

---

## Proyecto de graduación

Alineado con RF-01–RF-13 (Cap. IV–V). Documento y comparativo en `docs/`.
