# Comparativo de cambios en el documento de tesis

**Original:** `Gestion-Suministros-Proyecto-G1-ORIGINAL.docx`  
**Actualizado:** `Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx`  

Se aplicaron **solo sustituciones de texto** en párrafos que contenían frases desalineadas con la implementación. **No se modificaron** el MER de colores (11 tablas), casos de uso ni diagramas del Capítulo V. Se agregó un **anexo de aclaraciones** (Cap. V y VI) al final.

## Reemplazos aplicados

### (1 párrafo(s))

**Antes (fragmento):**
> permitiendo realizar solicitudes de suministros sin necesidad de autenticación

**Después (fragmento):**
> permitiendo que cada sucursal registre solicitudes mediante usuarios institucionales autenticados (rol sucursal), vinculados a su sede

### (1 párrafo(s))

**Antes (fragmento):**
> mientras que el personal administrativo contará con una consola de gestión con control de acceso por roles

**Después (fragmento):**
> mientras que el personal de soporte técnico y administración contará con una consola interna con control de acceso por roles y permisos

### (2 párrafo(s))

**Antes (fragmento):**
> registro de solicitudes sin autenticación

**Después (fragmento):**
> registro de solicitudes con autenticación de usuario de sucursal (credenciales asignadas por administración)

### (1 párrafo(s))

**Antes (fragmento):**
> captura estructurada de datos obligatorios: sucursal, ubicación, puesto o área, correo electrónico, modelo del equipo, tipo de solicitud y cantidad requerida

**Después (fragmento):**
> captura de datos: suministro del catálogo, cantidad solicitada y observaciones; la sucursal y el solicitante se obtienen del usuario autenticado

### (1 párrafo(s))

**Antes (fragmento):**
> generación automática de identificadores únicos por tipo de movimiento:

**Después (fragmento):**
> generación de un identificador único secuencial por solicitud (número de registro). En la implementación se utiliza catálogo de suministros y detalle de cantidades (los códigos ENT, DEV y OC del análisis inicial se documentan como evolución del diseño en la nota final)

### (1 párrafo(s))

**Antes (fragmento):**
> envío automático de confirmación al solicitante mediante correo electrónico

**Después (fragmento):**
> envío automático de correo de confirmación al solicitante (correo del usuario de sucursal) y notificación al buzón de soporte técnico (TI) configurado en el sistema

### (1 párrafo(s))

**Antes (fragmento):**
> Portal de solicitudes sin autenticación para usuarios solicitantes.

**Después (fragmento):**
> Módulo de solicitudes para usuarios de sucursal autenticados (rol sucursal), con acceso restringido a registro y consulta de sus propias solicitudes.

### (1 párrafo(s))

**Antes (fragmento):**
> cada suministro estará vinculado a la impresora correspondiente

**Después (fragmento):**
> cada suministro forma parte de un catálogo institucional y se relaciona con el inventario por sucursal

### (1 párrafo(s))

**Antes (fragmento):**
> como usuarios, impresoras, suministros, solicitudes y bitacora_movimientos

**Después (fragmento):**
> como usuarios, roles, permisos, sucursales, suministros, solicitudes, detalle de solicitudes, inventario, movimiento de inventario y bitácora

### (1 párrafo(s))

**Antes (fragmento):**
> Los modelos representan las tablas de la base de datos (usuarios, impresoras, suministros, solicitudes, bitacora_movimientos).

**Después (fragmento):**
> Los modelos representan las tablas de la base de datos (usuarios, roles, permisos, sucursales, suministros, solicitudes, detalle de solicitudes, inventario, movimientos de inventario y bitácora).

### (1 párrafo(s))

**Antes (fragmento):**
> Se implementa autenticación y roles (admin y usuario) para controlar quién puede registrar solicitudes o despachar suministros.

**Después (fragmento):**
> Se implementa autenticación, roles (administrador, soporte técnico y sucursal) y permisos granulares; el segundo factor de autenticación (TOTP) aplica al personal administrativo y de soporte.

### (1 párrafo(s))

**Antes (fragmento):**
> Laravel facilita el envío de correos automáticos cuando se genera una nueva solicitud de tóner o se despacha un suministro, manteniendo a los administradores y usuarios informados en tiempo real.

**Después (fragmento):**
> Laravel facilita el envío de correos automáticos: aviso a TI al registrar una solicitud, confirmación al solicitante, copia obligatoria a TI al atender la solicitud y aviso al correo que soporte indique manualmente al despachar; la activación se administra desde el módulo de configuración y en la ta…

## Anexo agregado al final

**Título:** Nota de alineación con la implementación (Capítulos V y VI)

### Alcance del documento

El Capítulo V (modelo lógico de once entidades, diagrama de colores y casos de uso) se mantiene como diseño aprobado del proyecto. Las modificaciones de este anexo no sustituyen figuras, MER ni diagramas de secuencia del Capítulo V; solo aclaran cómo se materializó la solución en software.

### Capítulo I y módulo de solicitudes

Se adoptó autenticación por usuario de sucursal (rol sucursal) en lugar de un portal público anónimo, para trazabilidad y control de acceso (RNF-01 a RNF-04).

### Capítulo V — modelo lógico (11 tablas de negocio)

Las once entidades del MER — roles, permisos, rol_permiso, usuarios, sucursales, suministros, inventario, solicitudes, detalle_solicitudes, movimiento_inventario y bitácora — corresponden a las tablas homónimas en MySQL. Las relaciones y el ciclo de vida de la solicitud (pendiente, en_proceso, atendida, rechazada) se respetan en la implementación.

### Capítulo VI — base de datos física en MySQL Workbench

Además de las once tablas de negocio, la base gestion_suministros incluye tablas auxiliares del framework Laravel (migrations, cache, cache_locks, jobs, job_batches, failed_jobs, sessions, password_reset_tokens) y la tabla parametros_sistema (configuración operativa de notificaciones por correo). Las tablas desafios_2fa y codigos_2fa forman parte del diseño de seguridad; la versión desplegada utiliza TOTP (Google Authenticator) con el secreto almacenado en usuarios (totp_secreto). Estas tablas técnicas no forman parte del MER del Capítulo V y pueden aparecer vacías hasta que el framework o un flujo opcional las utilice.

### Equivalencias de nomenclatura (lógico → físico)

En Laravel convencional: id_usuario/id_rol → id y claves foráneas rol_id, sucursal_id; password_hash → password (hash bcrypt); mfa_secret → totp_secreto; existencia en inventario → cantidad; estados en minúsculas en ENUM. La solicitud incluye atributos adicionales de auditoría de despacho y correo (correo_solicitante, correo_destinatario_despacho, nota_despacho, despachado_por_usuario_id) alineados con RF de notificación y trazabilidad.

### Notificaciones por correo electrónico

El envío utiliza SMTP (cuenta remitente configurable en el servidor, por ejemplo correo institucional con contraseña de aplicación). Los avisos obligatorios a soporte técnico se dirigen al buzón NOTIFICACION_SOPORTE_EMAIL (soporteti@grupofabrigas.com). Al registrar una solicitud se notifica a TI y se confirma al solicitante; al atender se envía copia a TI y, opcionalmente, un aviso al correo que indique soporte. Cada intento de envío se registra en la bitácora del sistema (acciones email_enviado / email_fallido).

### Bitácora y logs

La auditoría funcional consultable en la aplicación corresponde a la tabla bitácora (módulo, acción, detalle, usuario, IP). Los archivos laravel.log en el servidor son registros técnicos del framework y no sustituyen la bitácora de negocio.

**Total de reemplazos en párrafos:** 13

## Cómo comparar en Word

1. Abra `Gestion-Suministros-Proyecto-G1-ORIGINAL.docx` y `Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx`.
2. Use **Revisar → Comparar** para ver solo los cambios de texto y el anexo final.
3. Entregue o defienda con el ACTUALIZADO; conserve el ORIGINAL como referencia del diseño inicial.
