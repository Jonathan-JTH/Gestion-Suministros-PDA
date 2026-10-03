# Gestión de Suministros

Sistema web (Laravel 12, MySQL) para solicitudes de suministros por sucursal, gestión TI, inventario, reportes, bitácora y notificaciones por correo.

## Estructura

| Carpeta | Rol |
|---------|-----|
| **[backend/](backend/)** | API, servicios, modelos, **base de datos** (migraciones/seeders) |
| **[frontend/](frontend/)** | **Interfaz gráfica** (vistas Blade, CSS/JS, `public`) |
| **[docs/](docs/)** | Guías y documento de tesis actualizado |

Detalle: [ESTRUCTURA-PROYECTO.md](ESTRUCTURA-PROYECTO.md)

Documentación operativa: **[docs/GUIA-PROYECTO-FUNCIONAL.md](docs/GUIA-PROYECTO-FUNCIONAL.md)**

## Inicio rápido

Desde la **raíz** del repo (el `artisan` de la raíz delega en `backend/`):

```bash
cd backend
composer install
cp .env.example .env
cd ..
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Abrir `http://127.0.0.1:8000` e iniciar sesión (ver usuarios en la guía).

## Comandos útiles

```bash
php artisan sistema:verificar
php artisan notificacion:probar-correo
php artisan config:clear
```

## Proyecto de graduación

Alineado con RF-01–RF-13 (Cap. IV–V). Ajustes de redacción al implementado: `docs/Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx`.
