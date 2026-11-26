# Guía para redactar el Capítulo VI (usar con ChatGPT u otro asistente)

**Proyecto:** Diseño, implementación y evaluación de un sistema web para gestión de suministros de impresión — UMG PG2  
**Autor:** Erwin Jonathan González Estrada  
**Sistema real:** Laravel 12, MySQL 8, repo https://github.com/Jonathan-JTH/Gestion-Suministros-PDA  
**Documento base tesis (PDF G2):** Capítulos I–V + Referencias (p. ~207). **No incluye cuerpo del Capítulo VI** — usted lo escribe ahora.

Copie **desde la línea “PROMPT PARA CHATGPT”** hasta el final y péguelo en ChatGPT. Adjunte su Word/PDF de tesis si el chat lo permite.

---

## PROMPT PARA CHATGPT (copiar todo lo siguiente)

```
Eres asistente académico para un proyecto de graduación UMG (Ingeniería en Sistemas). Ayúdame a redactar el CAPÍTULO VI — Implementación y evaluación de la solución en español, norma APA 7 (tercera persona, tiempos pasados para lo ya construido, citas y referencias al final del documento, no al final del capítulo).

REGLAS:
1. NO reescribas el Capítulo V ni cambies el MER de 11 tablas, casos de uso ni diagramas; el Cap. V es diseño aprobado.
2. En Cap. VI explica CÓMO se materializó ese diseño y las DECISIONES de implementación donde el software difiere del texto antiguo de la tesis.
3. Enlaza cada sección con RF/CU del Cap. IV–V cuando sea posible (tabla de trazabilidad).
4. Propón figuras/capturas con leyenda APA: "Figura X. … Nota. Elaboración propia, 2026."
5. Entrega el capítulo con numeración 6.1, 6.2, … y párrafos listos para Word (sin markdown en el texto final si te pido versión Word).
6. Al final, dame una lista breve "Ajustes pendientes en Cap. I–V" (solo frases concretas, no reescribir capítulos enteros).

CONTEXTO DEL SISTEMA IMPLEMENTADO (verdad técnica — no inventar):
```

*(Pegue debajo el bloque “MOCKUP: TESIS vs SISTEMA” y “ESPECIFICACIÓN CAPÍTULO VI” de este mismo archivo.)*

---

## MOCKUP: lo que decía la tesis (G2) vs lo que hace el sistema hoy

Use esta tabla para que ChatGPT no contradiga el software ni el diseño sin explicar el puente.

| Tema | Tesis / diseño (texto o diagramas antiguos) | Sistema implementado (evidencia) | Cómo tratarlo en Cap. VI |
|------|---------------------------------------------|----------------------------------|---------------------------|
| **Acceso sucursal** | Portal o registro “sin autenticación” / solicitante anónimo | Usuario **rol sucursal** con login; sucursal y solicitante salen de la sesión | 6.2 + 6.4: decisión de trazabilidad (RNF seguridad). Nota: diagramas Cap. V pueden quedar como diseño; aquí se documenta la variante implementada. |
| **Roles** | A veces “admin y usuario” | **administrador**, **soporte técnico**, **sucursal** + **permisos** por módulo | 6.2, 6.4; tabla roles vs rutas |
| **2FA** | Autenticación administrativa con segundo factor (diseño) | **TOTP** (Google Authenticator), secreto en `usuarios.totp_secreto`; obligatorio admin/soporte tras login | 6.4 |
| **reCAPTCHA** | No en diseño original | Opcional en **login** (`RECAPTCHA_*`, v2 o Enterprise) | 6.4; integración externa opcional (complementa 5.14.1) |
| **Formulario solicitud** | Muchos campos manuales (ubicación, puesto, modelo impresora, etc.) | **Suministro** del catálogo, **cantidad**, **observaciones**; resto del contexto por usuario/sucursal | 6.2; comparar con prototipo Fig. 28 |
| **Identificadores** | ENT / DEV / OC por tipo de movimiento | **Número secuencial** por solicitud + detalle de líneas | 6.2; evolución del diseño |
| **Impresoras en BD** | Entidad impresora vinculada a suministro | **Catálogo institucional** + **inventario por sucursal** (sin tabla impresoras) | 6.2 + nota de equivalencia MER |
| **Correos** | Confirmación al solicitante | TI al crear solicitud + confirmación sucursal; al **atender**: copia TI + opcional destinatario manual; toggles en **Configuración** y `parametros_sistema` | 6.5 |
| **Bitácora** | Auditoría en diseño | Tabla **bitácora** (módulo, acción, detalle, usuario, IP); distinto de `laravel.log` | 6.7 |
| **Reportes** | CU-11, reportes gráficos en alcance | **5 tipos** de reporte, **Chart.js** en web, **PDF** DomPDF, gráfica SVG servidor, **membrete Fabrigas/PDA** A4 | 6.6 |
| **Infraestructura** | Ubuntu, Laravel 12, MySQL (diseño despliegue) | Mismo stack; desarrollo local `php artisan serve`, MySQL `gestion_suministros` | 6.1 |
| **API** | Enfoque web | Web principal; **JWT opcional** (`/api/auth/*`) si se menciona | 6.1 u 6.2 breve |
| **Figuras Cap. V** | Prototipos estáticos Fig. 28–33 | **Capturas reales** del sistema | Pedir a ChatGPT leyendas para reemplazar o anexo de evidencias |

---

## ESPECIFICACIÓN CAPÍTULO VI (contenido que debe salir)

Título sugerido (alineado al nombre del proyecto):

**Capítulo VI. Implementación y evaluación de la solución**

### 6.1 Entorno de desarrollo y estructura del repositorio

**Debe incluir:**

- Herramientas: PHP 8.2+, Composer, MySQL 8, Laravel 12, Bootstrap 5, Vite (opcional para assets).
- Estructura monorepo: `backend/` (app, migrations, routes, `.env`) y `frontend/` (views Blade, `public/`).
- Base de datos: `gestion_suministros`; migraciones + seeders (`migrate:fresh --seed`).
- Control de versiones: GitHub `Jonathan-JTH/Gestion-Suministros-PDA`.
- Comandos de verificación: `php artisan sistema:verificar`, `php artisan config:clear`.

**Figura sugerida:** diagrama simple carpeta backend/frontend (o captura del repo en GitHub).

---

### 6.2 Módulos funcionales implementados

**Módulos y actores (mapear a CU del Cap. V):**

| Módulo | Rol principal | Casos de uso / RF relacionados |
|--------|---------------|--------------------------------|
| Login / logout / 2FA | Todos (2FA admin/soporte) | CU autenticación, RF seguridad |
| Dashboard | admin, soporte | RF panel / indicadores |
| Solicitudes (crear, listar, detalle) | sucursal | CU registro solicitud |
| Gestión solicitudes (aprobar, rechazar, atender) | soporte, admin | CU gestión solicitudes |
| Suministros (CRUD catálogo) | admin, soporte | RF catálogo |
| Inventario (entrada, ajuste, stock bajo) | admin, soporte | RF inventario |
| Trazabilidad movimientos | admin, soporte | CU trazabilidad |
| Reportes + PDF | admin, soporte (permiso `reportes.generar`) | CU-11 |
| Bitácora (filtros, paginación) | admin, soporte | CU-10 / bitácora |
| Configuración notificaciones | admin | RF notificaciones |
| Usuarios (CRUD) | admin | RF administración usuarios |

**Flujo crítico para narrar (demo defensa):**

1. Sucursal login → nueva solicitud → correos (o log si SMTP off).
2. Soporte → gestión → en proceso → **atender** → descuenta inventario, estado **atendida**, bitácora.
3. Reporte → gráfica → exportar PDF.

**Estados solicitud implementados:** `pendiente`, `en_proceso`, `atendida`, `rechazada` (minúsculas en ENUM).

---

### 6.3 Interfaz de usuario

- Bootstrap 5, Bootstrap Icons, layout con barra lateral según permisos.
- Formularios unificados (suministros, inventario, usuarios).
- Pantallas a capturar: login, dashboard, listado solicitudes, modal atender, reportes resultado, PDF.

**Relación con Cap. V:** los prototipos Fig. 28–33 son referencia; la implementación refinó UI (decirlo sin invalidar el diseño).

---

### 6.4 Seguridad de acceso

- Sesión Laravel, middleware `auth`, `2fa`, `role:…`.
- Permisos granulares (`tienePermiso`).
- TOTP setup en primer uso admin/soporte.
- reCAPTCHA opcional en login (desarrollo puede ir desactivado).
- Throttle en login (6 intentos/minuto).
- **Sucursal:** solo rutas `/solicitudes*` (sin panel admin).

---

### 6.5 Notificaciones por correo electrónico

- SMTP vía `.env` (`MAIL_*`, `NOTIFICACION_SOPORTE_EMAIL`, ej. soporteti@grupofabrigas.com).
- Sin SMTP: driver `log` (explicar en pruebas).
- Eventos: al registrar solicitud; al atender (TI + opcional manual).
- Bitácora: `email_enviado` / `email_fallido`.

---

### 6.6 Reportes, visualización analítica y exportación PDF

**Tipos de reporte (`ReporteController`):**

1. `consumo` — consumo por suministro  
2. `solicitudes` — solicitudes por estado  
3. `sucursal` — solicitudes por sucursal  
4. `movimientos` — movimientos de inventario  
5. `inventario_bajo` — inventario bajo mínimo  

**Gráficas en pantalla:** estilos `bar`, `horizontal`, `line`, `pie`, `doughnut` (Chart.js).

**PDF:** DomPDF, vista `reportes/pdf.blade.php`, gráfica SVG (`ReporteGraficoSvg`), membrete PNG Fabrigas/PDA, requiere extensión PHP **GD**.

---

### 6.7 Bitácora, trazabilidad y pruebas funcionales

- Bitácora consultable: filtros por módulo, acción, fechas.
- Trazabilidad: movimientos de inventario ligados al negocio.
- **Pruebas:** escenarios con usuarios seed (admin, soporte, sucursal); referencia guías `docs/GUIA-DEMO-DEFENSA.md` y `docs/GUIA-PROYECTO-FUNCIONAL.md`.
- Tabla sugerida: caso de prueba | pasos | resultado esperado | resultado obtenido.

---

### 6.8 Evaluación de resultados (recomendado para el título del proyecto)

Redactar aunque sea breve:

- Cumplimiento de RF críticos (tabla sí/no + observación).
- Indicadores del Cap. II (tiempo de registro, trazabilidad, etc.) — si no hay medición formal, **evaluación cualitativa** por prueba demo + capturas.
- Limitaciones: DomPDF vs navegador, dependencia GD, SMTP en producción.
- Trabajo futuro: integración ERP, app móvil, etc.

---

## Base de datos física (párrafo tipo para 6.1 o 6.2)

- **11 tablas de negocio** del MER Cap. V en MySQL.
- **Adicionales:** `parametros_sistema`, tablas Laravel (sessions, jobs, cache…).
- **Equivalencias:** `password_hash` → `password`; FK `rol_id`, `sucursal_id`; campos extra en `solicitudes` para despacho y correos.

---

## Referencias APA 7 a agregar (si no están en su lista)

- Barryvdh. (2026). Laravel DomPDF. https://github.com/barryvdh/laravel-dompdf  
- Chart.js. (2026). Chart.js documentation. https://www.chartjs.org/docs/latest/  
- Google. (2026). reCAPTCHA Developer Guide. https://developers.google.com/recaptcha/docs  
- Laravel. (2026). Laravel Documentation. https://laravel.com/docs (ya suele estar)

---

## CHECKLIST: correcciones en Cap. I–V (NO las hace ChatGPT en Cap. VI; son tareas aparte en Word)

Indique a ChatGPT que al final liste esto para usted:

| Capítulo | Sección aprox. | Corrección necesaria |
|----------|----------------|----------------------|
| I | 1.5 / 1.6.1 | Quitar “sin autenticación”; sucursal autenticada; campos reales del formulario |
| III | 3.1.6 Laravel | Roles admin/soporte/sucursal, TOTP, correos detallados, modelos sin impresoras |
| V | 5.14.1 | Mencionar SMTP y reCAPTCHA como integraciones opcionales |
| V | Fig. 17, 20, 28 | Nota al pie o leyenda: implementación con login sucursal |
| IV | Tabla RF | Revisar RF de registro si hablan de portal público |

*(El repo incluye borrador automático en `docs/Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx` con parte de Cap. I, III, V ya tocada; usted puede usar su PDF G2 + este Cap. VI nuevo.)*

---

## MENSAJES DE SEGUIMIENTO PARA CHATGPT (copiar uno por uno)

**1. Primera entrega**

> Redacta el Capítulo VI completo (6.1 a 6.8) con base en la especificación. Extensión orientativa: 15–25 páginas en Word (doble espacio). Incluye tablas de módulos y de casos de prueba.

**2. Afinar estilo UMG**

> Revisa que no haya primera persona ("yo", "nosotros"); usa "se implementó", "el sistema permite". Ajusta transiciones entre secciones.

**3. Figuras**

> Propón 8 figuras numeradas (continuando numeración del Cap. V, ej. Figura 35 en adelante) con título y nota APA; indica qué captura de pantalla debo pegar en cada una.

**4. Evaluación**

> Expande 6.8 con matriz RF-01…RF-12 vs estado Implementado / Parcial / No aplica, justificando con el mockup tesis vs sistema.

**5. Solo diferencias**

> Dame un cuadro "Diseño Cap. V vs Implementación Cap. VI" de una página para pegar al inicio del capítulo como 6.0 Introducción al capítulo.

---

## Archivos de apoyo en el repo (para citar en la tesis)

| Archivo | Uso |
|---------|-----|
| `docs/GUIA-DEMO-DEFENSA.md` | Guion de defensa 15–20 min |
| `docs/GUIA-PROYECTO-FUNCIONAL.md` | Instalación, correos, reCAPTCHA |
| `docs/MAPA-CAMBIOS-POR-CAPITULO.md` | Dónde se tocaron otros capítulos |
| `README.md` | Stack y clonado del repo |

---

*Elaboración: documento de trabajo para PG2 — no sustituye criterios del asesor UMG.*
