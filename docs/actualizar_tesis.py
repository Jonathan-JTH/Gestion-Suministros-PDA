#!/usr/bin/env python3
"""Actualiza el docx de tesis: reemplazos alineados a la implementación, Cap. VI, referencias APA 7 y anexo."""

from __future__ import annotations

import sys
from pathlib import Path

from docx import Document
from docx.oxml import OxmlElement
from docx.text.paragraph import Paragraph

BASE = Path(__file__).resolve().parent
ORIGINAL = BASE / "Gestion-Suministros-Proyecto-G1-ORIGINAL.docx"
OUTPUT = BASE / "Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx"
LOG = BASE / "COMPARATIVO-CAMBIOS-DOCUMENTO.md"
MAPA = BASE / "MAPA-CAMBIOS-POR-CAPITULO.md"

CHARS_PER_PAGE = 2800

REPLACEMENTS: list[tuple[str, str, str]] = [
    (
        "Capítulo I — Alcance / Introducción",
        "permitiendo realizar solicitudes de suministros sin necesidad de autenticación",
        "permitiendo que cada sucursal registre solicitudes mediante usuarios institucionales autenticados (rol sucursal), vinculados a su sede",
    ),
    (
        "Capítulo I — Alcance / Introducción",
        "mientras que el personal administrativo contará con una consola de gestión con control de acceso por roles",
        "mientras que el personal de soporte técnico y administración contará con una consola interna con control de acceso por roles y permisos",
    ),
    (
        "Capítulo I — 1.6.1 Alcance funcional",
        "registro de solicitudes sin autenticación",
        "registro de solicitudes con autenticación de usuario de sucursal (credenciales asignadas por administración)",
    ),
    (
        "Capítulo I — 1.6.1 Alcance funcional",
        "captura estructurada de datos obligatorios: sucursal, ubicación, puesto o área, correo electrónico, modelo del equipo, tipo de solicitud y cantidad requerida",
        "captura de datos: suministro del catálogo, cantidad solicitada y observaciones; la sucursal y el solicitante se obtienen del usuario autenticado",
    ),
    (
        "Capítulo I — 1.6.1 Alcance funcional",
        "generación automática de identificadores únicos por tipo de movimiento:",
        "generación de un identificador único secuencial por solicitud (número de registro). En la implementación se utiliza catálogo de suministros y detalle de cantidades (los códigos ENT, DEV y OC del análisis inicial se documentan como evolución del diseño en la nota final)",
    ),
    (
        "Capítulo I — 1.6.1 Alcance funcional",
        "envío automático de confirmación al solicitante mediante correo electrónico",
        "envío automático de correo de confirmación al solicitante (correo del usuario de sucursal) y notificación al buzón de soporte técnico (TI) configurado en el sistema",
    ),
    (
        "Capítulo I — 1.5 Viabilidad técnica",
        "Portal de solicitudes sin autenticación para usuarios solicitantes.",
        "Módulo de solicitudes para usuarios de sucursal autenticados (rol sucursal), con acceso restringido a registro y consulta de sus propias solicitudes.",
    ),
    (
        "Capítulo III — Marco teórico (Laravel / datos)",
        "cada suministro estará vinculado a la impresora correspondiente",
        "cada suministro forma parte de un catálogo institucional y se relaciona con el inventario por sucursal",
    ),
    (
        "Capítulo III — Marco teórico (Laravel / datos)",
        "como usuarios, impresoras, suministros, solicitudes y bitacora_movimientos",
        "como usuarios, roles, permisos, sucursales, suministros, solicitudes, detalle de solicitudes, inventario, movimiento de inventario y bitácora",
    ),
    (
        "Capítulo III — Marco teórico (Laravel / datos)",
        "Los modelos representan las tablas de la base de datos (usuarios, impresoras, suministros, solicitudes, bitacora_movimientos).",
        "Los modelos representan las tablas de la base de datos (usuarios, roles, permisos, sucursales, suministros, solicitudes, detalle de solicitudes, inventario, movimientos de inventario y bitácora).",
    ),
    (
        "Capítulo III — 3.1.6 Laravel",
        "Se implementa autenticación y roles (admin y usuario) para controlar quién puede registrar solicitudes o despachar suministros.",
        "Se implementa autenticación, roles (administrador, soporte técnico y sucursal) y permisos granulares; el segundo factor de autenticación (TOTP) aplica al personal administrativo y de soporte.",
    ),
    (
        "Capítulo III — 3.1.6 Laravel",
        "Laravel facilita el envío de correos automáticos cuando se genera una nueva solicitud de tóner o se despacha un suministro, manteniendo a los administradores y usuarios informados en tiempo real.",
        "Laravel facilita el envío de correos automáticos: aviso a TI al registrar una solicitud, confirmación al solicitante, copia obligatoria a TI al atender la solicitud y aviso al correo que soporte indique manualmente al despachar; la activación se administra desde el módulo de configuración y en la tabla parametros_sistema.",
    ),
    (
        "Capítulo V — 5.14.1 Integraciones",
        "En cuanto a integraciones externas, la primera versión de la solución se plantea como un sistema independiente para la gestión de suministros de impresión. Por lo tanto, no se contempla como dependencia obligatoria la integración con sistemas externos de inventario, contabilidad, compras u otras plataformas institucionales. Cualquier integración futura deberá evaluarse como una ampliación del alcance y analizarse previamente para determinar sus efectos sobre la arquitectura, seguridad y modelo de datos.",
        "En cuanto a integraciones externas, la versión implementada permanece como sistema independiente (sin ERP obligatorio). Se incorporaron integraciones opcionales, descritas en el Capítulo VI: servicio SMTP para notificaciones transaccionales y Google reCAPTCHA en el inicio de sesión, activables mediante variables de entorno. Cualquier integración con inventario corporativo, contabilidad o compras deberá evaluarse como ampliación del alcance y analizarse en arquitectura, seguridad y modelo de datos.",
    ),
    (
        "Capítulo V — 5.14.1 Integraciones",
        "La ausencia de integraciones externas obligatorias en la primera versión permite mantener el sistema enfocado en los procesos de gestión de suministros definidos dentro del alcance. En caso de requerirse interoperabilidad con otras plataformas institucionales en etapas posteriores, dicha integración deberá evaluarse como una ampliación del diseño.",
        "La ausencia de integraciones corporativas obligatorias mantiene el foco en la gestión de suministros del alcance. En la implementación, las integraciones externas habituales son correo (SMTP) y verificación reCAPTCHA ante Google; ambas pueden desactivarse en desarrollo sin afectar la lógica de negocio.",
    ),
]

APPENDIX_TITLE = "Apéndice A. Nota de alineación diseño–implementación (Capítulos V y VI)"

APPENDIX_SECTIONS: list[tuple[str, str]] = [
    (
        "Alcance del apéndice",
        "El Capítulo V (modelo lógico de once entidades, diagrama de colores y casos de uso) se mantiene como diseño aprobado. "
        "Este apéndice no sustituye figuras, MER ni diagramas de secuencia; complementa el Capítulo VI con equivalencias técnicas.",
    ),
    (
        "Capítulo V — modelo lógico (11 tablas de negocio)",
        "Las once entidades del MER — roles, permisos, rol_permiso, usuarios, sucursales, suministros, inventario, solicitudes, detalle_solicitudes, movimiento_inventario y bitácora — "
        "corresponden a las tablas homónimas en MySQL. El ciclo de vida de la solicitud (pendiente, en_proceso, atendida, rechazada) se respeta en la implementación.",
    ),
    (
        "Capítulo VI — base de datos física",
        "Además de las once tablas de negocio, la base gestion_suministros incluye tablas auxiliares de Laravel (migrations, cache, jobs, sessions, password_reset_tokens) "
        "y parametros_sistema (notificaciones). La versión desplegada utiliza TOTP (Google Authenticator) con totp_secreto en usuarios.",
    ),
    (
        "Equivalencias de nomenclatura (lógico → físico)",
        "En Laravel: id_usuario/id_rol → id y claves foráneas rol_id, sucursal_id; password_hash → password (bcrypt); mfa_secret → totp_secreto; existencia → cantidad en inventario. "
        "La solicitud incluye correo_solicitante, correo_destinatario_despacho, nota_despacho y despachado_por_usuario_id para trazabilidad.",
    ),
]

CHAPTER_VI: list[tuple[str, str, list[str]]] = [
    (
        "Heading 1",
        "Capítulo VI\nImplementación y evaluación de la solución",
        [],
    ),
    (
        "Heading 2",
        "6.1 Entorno de desarrollo y estructura del repositorio",
        [
            "La solución se materializó como aplicación web monolítica Laravel 12 (PHP 8.2 o superior) con base de datos MySQL 8. "
            "El código se organiza en un repositorio con carpetas backend (lógica, modelos, migraciones, rutas) y frontend (vistas Blade, recursos públicos CSS/JS e imágenes), "
            "compartiendo la misma base de datos definida en el Capítulo V.",
            "La evidencia versionada del proyecto se encuentra en el repositorio público "
            "https://github.com/Jonathan-JTH/Gestion-Suministros-PDA (rama master, commits de implementación y documentación técnica en docs/).",
        ],
    ),
    (
        "Heading 2",
        "6.2 Módulos funcionales implementados",
        [
            "Se desarrollaron los módulos alineados con los casos de uso del diseño: autenticación y roles (administrador, soporte técnico, sucursal); "
            "registro y consulta de solicitudes por sucursal; gestión de solicitudes por soporte (en proceso, atender, rechazar); catálogo de suministros; "
            "inventario por sucursal con movimientos; trazabilidad de movimientos; panel con indicadores; configuración de notificaciones; "
            "administración de usuarios, roles y permisos; y bitácora consultable con filtros y paginación.",
        ],
    ),
    (
        "Heading 2",
        "6.3 Interfaz de usuario",
        [
            "La interfaz utiliza Bootstrap 5 e iconografía Bootstrap Icons, con diseño responsive y navegación lateral según permisos del usuario. "
            "Formularios de alta y edición siguieron el mismo criterio visual en catálogo, inventario y usuarios, con mensajes de validación en español.",
        ],
    ),
    (
        "Heading 2",
        "6.4 Seguridad de acceso",
        [
            "Se implementó autenticación por sesión web, permisos granulares por módulo y segundo factor TOTP (Google Authenticator) para administrador y soporte. "
            "Opcionalmente, el login incluye Google reCAPTCHA (v2 checkbox o integración Enterprise v2/v3), configurable con RECAPTCHA_* en el entorno; "
            "en desarrollo puede desactivarse sin comprometer el resto del flujo.",
        ],
    ),
    (
        "Heading 2",
        "6.5 Notificaciones por correo electrónico",
        [
            "Las notificaciones usan el componente Mail de Laravel vía SMTP. Al registrar una solicitud se notifica a TI y se confirma al solicitante; "
            "al atender se envía copia a TI y, si está habilitado, aviso al correo indicado en despacho. "
            "Los interruptores operativos residen en Configuración → Notificaciones y en parametros_sistema; cada envío se audita en bitácora (email_enviado / email_fallido).",
        ],
    ),
    (
        "Heading 2",
        "6.6 Reportes, visualización analítica y exportación PDF",
        [
            "El módulo de reportes (CU-11) permite cinco tipos de consulta en rango de fechas: consumo por suministro, solicitudes por estado, "
            "solicitudes por sucursal, movimientos de inventario e inventario bajo mínimo. "
            "En pantalla, Chart.js renderiza gráficas intercambiables (barras verticales u horizontales, líneas, circular y dona) con paleta alineada a la interfaz.",
            "La exportación PDF utiliza barryvdh/laravel-dompdf: plantilla Blade con tabla de detalle, gráfica vectorial SVG generada en servidor (ReporteGraficoSvg) "
            "y membrete institucional Fabrigas/Productos del Aire en encabezado y pie de página A4. La extensión PHP GD es requisito para incrustar imágenes PNG del membrete.",
        ],
    ),
    (
        "Heading 2",
        "6.7 Bitácora, trazabilidad y pruebas funcionales",
        [
            "La bitácora de negocio registra módulo, acción, detalle, usuario e IP; se distingue de laravel.log (registro técnico del framework). "
            "Las pruebas funcionales se ejecutaron con datos semilla (usuarios por rol), recorriendo el flujo sucursal → soporte → inventario → reportes → PDF, "
            "documentado en docs/GUIA-DEMO-DEFENSA.md y docs/GUIA-PROYECTO-FUNCIONAL.md.",
        ],
    ),
]

NEW_REFERENCES: list[str] = [
    "Barryvdh. (2026). Laravel DomPDF (Documentación). https://github.com/barryvdh/laravel-dompdf",
    "Chart.js. (2026). Chart.js documentation. https://www.chartjs.org/docs/latest/",
    "Google. (2026). reCAPTCHA Developer Guide. https://developers.google.com/recaptcha/docs",
]


def iter_all_paragraphs(doc: Document):
    for p in doc.paragraphs:
        yield p
    for table in doc.tables:
        for row in table.rows:
            for cell in row.cells:
                for p in cell.paragraphs:
                    yield p


def replace_in_paragraph(paragraph, old: str, new: str) -> bool:
    full = paragraph.text
    if old not in full:
        return False
    new_text = full.replace(old, new, 1)
    if len(paragraph.runs) == 0:
        paragraph.add_run(new_text)
        return True
    paragraph.runs[0].text = new_text
    for run in paragraph.runs[1:]:
        run.text = ""
    return True


class DocContext:
    def __init__(self, doc: Document):
        self.doc = doc
        self.chars = 0
        self.h1 = "Portada / preliminares"
        self.h2 = ""

    def page_est(self) -> int:
        return max(1, self.chars // CHARS_PER_PAGE + 1)

    def feed(self, paragraph) -> None:
        st = paragraph.style.name if paragraph.style else ""
        text = paragraph.text.replace("\n", " ").strip()
        if st == "Heading 1" and text:
            self.h1 = text
            self.h2 = ""
        elif st == "Heading 2" and text:
            self.h2 = text
        self.chars += len(paragraph.text) + 1


def insert_paragraph_before(anchor: Paragraph, text: str = "", style: str | None = None) -> Paragraph:
    new_el = OxmlElement("w:p")
    anchor._element.addprevious(new_el)
    para = Paragraph(new_el, anchor._parent)
    if style:
        try:
            para.style = style
        except KeyError:
            pass
    if text:
        if style and "Heading" in style:
            para.add_run(text)
        elif text.startswith("\t"):
            para.add_run(text)
        else:
            para.add_run("\t" + text)
    return para


def insert_chapter_vi_block(anchor: Paragraph) -> None:
    flat: list[tuple[str, str]] = []
    for kind, title, bodies in CHAPTER_VI:
        flat.append((title, kind))
        for body in bodies:
            flat.append((body, "Normal"))
    cursor = anchor
    for text, style in reversed(flat):
        cursor = insert_paragraph_before(cursor, text, style)
    insert_paragraph_before(cursor, "")


def find_referencias_paragraph(doc: Document) -> Paragraph | None:
    for p in doc.paragraphs:
        if p.style and p.style.name == "Heading 1" and p.text.strip().startswith("Referencias"):
            return p
    return None


def insert_chapter_vi(doc: Document) -> int:
    anchor = find_referencias_paragraph(doc)
    if anchor is None:
        return 0
    insert_chapter_vi_block(anchor)
    return 1


def add_references_after_list(doc: Document) -> None:
    """Insert new APA entries after the last existing reference paragraph."""
    ref_heading_idx = None
    for i, p in enumerate(doc.paragraphs):
        if p.style and p.style.name == "Heading 1" and p.text.strip().startswith("Referencias"):
            ref_heading_idx = i
            break
    if ref_heading_idx is None:
        return
    last_ref = doc.paragraphs[ref_heading_idx]
    for j in range(ref_heading_idx + 1, len(doc.paragraphs)):
        t = doc.paragraphs[j].text.strip()
        if not t:
            continue
        if doc.paragraphs[j].style and doc.paragraphs[j].style.name == "Heading 1":
            break
        last_ref = doc.paragraphs[j]
    cursor = last_ref
    for ref in NEW_REFERENCES:
        new_el = OxmlElement("w:p")
        cursor._element.addnext(new_el)
        para = Paragraph(new_el, cursor._parent)
        para.add_run(ref)
        cursor = para


def add_appendix(doc: Document) -> None:
    doc.add_page_break()
    title_p = doc.add_paragraph()
    title_p.add_run(APPENDIX_TITLE).bold = True
    doc.add_paragraph(
        "Las siguientes aclaraciones complementan el Capítulo VI y enlazan el diseño documentado con la implementación Laravel 12, "
        "sin modificar figuras ni diagramas del Capítulo V (norma APA 7: apéndice posterior a referencias)."
    )
    for heading, body in APPENDIX_SECTIONS:
        h = doc.add_paragraph()
        h.add_run(heading).bold = True
        doc.add_paragraph(body)


def main() -> int:
    if not ORIGINAL.exists():
        print(f"No encontrado: {ORIGINAL}", file=sys.stderr)
        return 1

    doc = Document(str(ORIGINAL))
    ctx = DocContext(doc)
    replacement_log: list[dict] = []

    locations: list[tuple[int, str, str]] = []
    ctx_scan = DocContext(doc)
    for p in doc.paragraphs:
        ctx_scan.feed(p)
        for cap_label, old, _new in REPLACEMENTS:
            if old in p.text:
                locations.append((ctx_scan.page_est(), cap_label, ctx_scan.h2 or ctx_scan.h1))

    for cap_label, old, new in REPLACEMENTS:
        count = 0
        for p in iter_all_paragraphs(doc):
            if replace_in_paragraph(p, old, new):
                count += 1
        if count:
            page = next((pg for pg, cap, _ in locations if cap == cap_label), 0)
            sect = next((s for pg, cap, s in locations if cap == cap_label), "—")
            replacement_log.append(
                {"capitulo": cap_label, "seccion": sect, "pagina_aprox": page, "antes": old[:120]}
            )
        else:
            replacement_log.append(
                {"capitulo": cap_label, "seccion": "—", "pagina_aprox": 0, "antes": old[:80], "nota": "No encontrado en ORIGINAL"}
            )

    chapter_vi_ok = insert_chapter_vi(doc)
    add_references_after_list(doc)
    add_appendix(doc)

    doc.save(str(OUTPUT))

    cap_vi_page_est = 108
    appendix_page_est = 112

    log_lines = [
        "# Comparativo de cambios en el documento de tesis",
        "",
        f"**Original:** `{ORIGINAL.name}`  ",
        f"**Actualizado:** `{OUTPUT.name}`  ",
        "",
        "Actualización alineada a la implementación (2026): reemplazos en Capítulos I, III y V; **Capítulo VI nuevo** antes de Referencias; "
        "referencias APA adicionales; **Apéndice A** después de Referencias.",
        "",
        "## Reemplazos por capítulo",
        "",
    ]
    for item in replacement_log:
        if item.get("nota"):
            log_lines.append(f"- **{item['capitulo']}**: {item['nota']}")
            continue
        log_lines.append(
            f"- **{item['capitulo']}** — sección «{item['seccion']}», página aprox. **{item['pagina_aprox']}**"
        )

    log_lines.extend(["", "## Capítulo VI insertado", ""])
    log_lines.append(
        f"Nuevo **Capítulo VI — Implementación y evaluación de la solución** (secciones 6.1–6.7), "
        f"insertado **antes de Referencias** (página aprox. **{cap_vi_page_est}–{cap_vi_page_est + 3}**, según maquetación Word)."
    )
    log_lines.extend(["", "## Referencias APA 7 agregadas", ""])
    for r in NEW_REFERENCES:
        log_lines.append(f"- {r}")
    log_lines.extend(["", f"## {APPENDIX_TITLE}", ""])
    for heading, body in APPENDIX_SECTIONS:
        log_lines.append(f"### {heading}")
        log_lines.append("")
        log_lines.append(body)
        log_lines.append("")
    log_lines.extend(
        [
            "## Cómo obtener números de página exactos en Word",
            "",
            "1. Abra `Gestion-Suministros-Proyecto-G1-ACTUALIZADO.docx`.",
            "2. **Referencias → Actualizar tabla** (si aplica) y active numeración de páginas.",
            "3. **Revisar → Comparar** contra el ORIGINAL para resaltar solo cambios.",
            "4. Consulte `MAPA-CAMBIOS-POR-CAPITULO.md` para el índice por capítulo.",
            "",
        ]
    )
    LOG.write_text("\n".join(log_lines), encoding="utf-8")

    mapa = [
        "# Mapa de cambios por capítulo (APA 7)",
        "",
        "Documento generado automáticamente. Las **páginas son estimadas** (~2 800 caracteres/página); "
        "use Word para el número definitivo tras actualizar saltos y tabla de contenido.",
        "",
        "| Capítulo | Qué se modificó o agregó | Páginas aprox. |",
        "|----------|---------------------------|----------------|",
        "| Introducción / alcance | Texto de solicitudes autenticadas y consola TI (si aparecía en intro) | 4–6 |",
        "| **Capítulo I** — Marco conceptual | 1.5 Viabilidad, 1.6.1 Alcance funcional (auth, captura, correos, IDs) | 8–12 |",
        "| **Capítulo III** — Marco teórico | Modelos Eloquent, roles/TOTP, correos, catálogo vs impresoras | 27–30 |",
        "| **Capítulo V** — Diseño | 5.14.1 Integraciones (SMTP + reCAPTCHA opcionales) | 107–108 |",
        "| **Capítulo VI** *(nuevo)* | 6.1–6.7 Implementación completa (repo, módulos, UI, seguridad, reportes PDF, bitácora) | "
        f"{cap_vi_page_est}–{cap_vi_page_est + 3} |",
        "| **Referencias** | Entradas Chart.js, DomPDF, Google reCAPTCHA | Tras Cap. VI |",
        f"| **{APPENDIX_TITLE.split('.')[0]}** | Equivalencias MER ↔ MySQL y nomenclatura | {appendix_page_est}+ |",
        "",
        "## Capítulo VI — contenido agregado (para defensa)",
        "",
        "1. **6.1** Repositorio GitHub y estructura backend/frontend.",
        "2. **6.2** Módulos (solicitudes, inventario, usuarios, configuración, etc.).",
        "3. **6.3** UI Bootstrap 5.",
        "4. **6.4** TOTP + reCAPTCHA opcional.",
        "5. **6.5** Notificaciones SMTP y parametros_sistema.",
        "6. **6.6** Reportes (5 tipos), Chart.js, PDF DomPDF, membrete Fabrigas, SVG.",
        "7. **6.7** Bitácora, trazabilidad y guías de prueba en docs/.",
        "",
        "## Norma APA 7 aplicada",
        "",
        "- Orden: cuerpo → **Referencias** → **Apéndice A** (material suplementario).",
        "- Referencias con autor, fecha y URL para software/documentación (Chart.js, DomPDF, Google).",
        "- Títulos de capítulo en estilo **Heading 1/2** del documento original (no alteramos márgenes ni interlineado).",
        "",
        "## Archivos",
        "",
        f"- Entregar: `{OUTPUT.name}`",
        f"- Comparativo: `{LOG.name}`",
        "",
    ]
    MAPA.write_text("\n".join(mapa), encoding="utf-8")

    print(f"Guardado: {OUTPUT}")
    print(f"Log: {LOG}")
    print(f"Mapa: {MAPA}")
    print(f"Capítulo VI insertado: {bool(chapter_vi_ok)}")
    print(f"Reemplazos registrados: {len([x for x in replacement_log if not x.get('nota')])}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
