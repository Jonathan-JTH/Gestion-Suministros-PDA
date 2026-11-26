# Comparativo de cambios en el documento de tesis

**Original:** `Gestion-Suministros-Proyecto-G1-ORIGINAL.docx`  
**Actualizado:** `Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx`  

Actualización alineada a la implementación (2026): reemplazos en Capítulos I, III y V; **Capítulo VI nuevo** antes de Referencias; referencias APA adicionales; **Apéndice A** después de Referencias.

## Reemplazos por capítulo

- **Capítulo I — Alcance / Introducción** — sección «Introducción», página aprox. **4**
- **Capítulo I — Alcance / Introducción** — sección «Introducción», página aprox. **4**
- **Capítulo I — 1.6.1 Alcance funcional** — sección «Alcances del Proyecto», página aprox. **10**
- **Capítulo I — 1.6.1 Alcance funcional** — sección «Alcances del Proyecto», página aprox. **10**
- **Capítulo I — 1.6.1 Alcance funcional** — sección «Alcances del Proyecto», página aprox. **10**
- **Capítulo I — 1.6.1 Alcance funcional** — sección «Alcances del Proyecto», página aprox. **10**
- **Capítulo I — 1.5 Viabilidad técnica** — sección «1.5 Viabilidad del Proyecto», página aprox. **8**
- **Capítulo III — Marco teórico (Laravel / datos)** — sección «3.1 Conceptos Generales», página aprox. **24**
- **Capítulo III — Marco teórico (Laravel / datos)** — sección «3.1 Conceptos Generales», página aprox. **24**
- **Capítulo III — Marco teórico (Laravel / datos)** — sección «3.1 Conceptos Generales», página aprox. **24**
- **Capítulo III — 3.1.6 Laravel** — sección «3.1 Conceptos Generales», página aprox. **27**
- **Capítulo III — 3.1.6 Laravel** — sección «3.1 Conceptos Generales», página aprox. **27**
- **Capítulo V — 5.14.1 Integraciones** — sección «5.14 Consideraciones del diseño», página aprox. **107**
- **Capítulo V — 5.14.1 Integraciones** — sección «5.14 Consideraciones del diseño», página aprox. **107**

## Capítulo VI insertado

Nuevo **Capítulo VI — Implementación y evaluación de la solución** (secciones 6.1–6.7), insertado **antes de Referencias** (página aprox. **108–111**, según maquetación Word).

## Referencias APA 7 agregadas

- Barryvdh. (2026). Laravel DomPDF (Documentación). https://github.com/barryvdh/laravel-dompdf
- Chart.js. (2026). Chart.js documentation. https://www.chartjs.org/docs/latest/
- Google. (2026). reCAPTCHA Developer Guide. https://developers.google.com/recaptcha/docs

## Apéndice A. Nota de alineación diseño–implementación (Capítulos V y VI)

### Alcance del apéndice

El Capítulo V (modelo lógico de once entidades, diagrama de colores y casos de uso) se mantiene como diseño aprobado. Este apéndice no sustituye figuras, MER ni diagramas de secuencia; complementa el Capítulo VI con equivalencias técnicas.

### Capítulo V — modelo lógico (11 tablas de negocio)

Las once entidades del MER — roles, permisos, rol_permiso, usuarios, sucursales, suministros, inventario, solicitudes, detalle_solicitudes, movimiento_inventario y bitácora — corresponden a las tablas homónimas en MySQL. El ciclo de vida de la solicitud (pendiente, en_proceso, atendida, rechazada) se respeta en la implementación.

### Capítulo VI — base de datos física

Además de las once tablas de negocio, la base gestion_suministros incluye tablas auxiliares de Laravel (migrations, cache, jobs, sessions, password_reset_tokens) y parametros_sistema (notificaciones). La versión desplegada utiliza TOTP (Google Authenticator) con totp_secreto en usuarios.

### Equivalencias de nomenclatura (lógico → físico)

En Laravel: id_usuario/id_rol → id y claves foráneas rol_id, sucursal_id; password_hash → password (bcrypt); mfa_secret → totp_secreto; existencia → cantidad en inventario. La solicitud incluye correo_solicitante, correo_destinatario_despacho, nota_despacho y despachado_por_usuario_id para trazabilidad.

## Cómo obtener números de página exactos en Word

1. Abra `Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx`.
2. **Referencias → Actualizar tabla** (si aplica) y active numeración de páginas.
3. **Revisar → Comparar** contra el ORIGINAL para resaltar solo cambios.
4. Consulte `MAPA-CAMBIOS-POR-CAPITULO.md` para el índice por capítulo.
