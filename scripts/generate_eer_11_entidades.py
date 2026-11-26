#!/usr/bin/env python3
"""Genera diagrama EER estilo MySQL Workbench — solo 11 tablas de negocio."""

from __future__ import annotations

from pathlib import Path

from PIL import Image, ImageDraw, ImageFont

OUT = Path(__file__).resolve().parents[1] / "docs" / "figuras" / "Figura-MER-11-tablas-estilo-Workbench.png"
W, H = 3200, 2200
HEADER = "#3595AC"
HEADER_TEXT = "#FFFFFF"
BORDER = "#999999"
BG = "#E5E5E5"
PK_COLOR = "#E8A317"
FK_COLOR = "#CC4444"
ROW_ALT = "#F7F7F7"

# (name, [(col, type, pk|fk|""), ...])
TABLES: dict[str, list[tuple[str, str, str]]] = {
    "permisos": [
        ("id", "BIGINT", "pk"),
        ("nombre", "VARCHAR(80)", ""),
        ("descripcion", "VARCHAR(255)", ""),
        ("modulo", "VARCHAR(80)", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "roles": [
        ("id", "BIGINT", "pk"),
        ("nombre", "VARCHAR(50)", ""),
        ("descripcion", "VARCHAR(255)", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "rol_permiso": [
        ("rol_id", "BIGINT", "pkfk"),
        ("permiso_id", "BIGINT", "pkfk"),
    ],
    "usuarios": [
        ("id", "BIGINT", "pk"),
        ("nombre", "VARCHAR(100)", ""),
        ("correo", "VARCHAR(100)", ""),
        ("password", "VARCHAR(255)", ""),
        ("rol_id", "BIGINT", "fk"),
        ("sucursal_id", "BIGINT", "fk"),
        ("segundo_factor_habilitado", "TINYINT(1)", ""),
        ("totp_secreto", "TEXT", ""),
        ("activo", "TINYINT(1)", ""),
        ("remember_token", "VARCHAR(100)", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "sucursales": [
        ("id", "BIGINT", "pk"),
        ("nombre", "VARCHAR(120)", ""),
        ("codigo", "VARCHAR(20)", ""),
        ("direccion", "VARCHAR(255)", ""),
        ("activa", "TINYINT(1)", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "bitacora": [
        ("id", "BIGINT", "pk"),
        ("usuario_id", "BIGINT", "fk"),
        ("modulo", "VARCHAR(80)", ""),
        ("accion", "VARCHAR(80)", ""),
        ("detalle", "TEXT", ""),
        ("ip_address", "VARCHAR(45)", ""),
        ("created_at", "TIMESTAMP", ""),
    ],
    "suministros": [
        ("id", "BIGINT", "pk"),
        ("nombre", "VARCHAR(120)", ""),
        ("tipo", "VARCHAR(80)", ""),
        ("marca", "VARCHAR(80)", ""),
        ("modelo", "VARCHAR(80)", ""),
        ("unidad_medida", "VARCHAR(30)", ""),
        ("stock_minimo", "INT UNSIGNED", ""),
        ("activo", "TINYINT(1)", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "inventario": [
        ("id", "BIGINT", "pk"),
        ("sucursal_id", "BIGINT", "fk"),
        ("suministro_id", "BIGINT", "fk"),
        ("cantidad", "INT UNSIGNED", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "solicitudes": [
        ("id", "BIGINT", "pk"),
        ("usuario_id", "BIGINT", "fk"),
        ("sucursal_id", "BIGINT", "fk"),
        ("estado", "ENUM", ""),
        ("observacion", "TEXT", ""),
        ("correo_destinatario_despacho", "VARCHAR(255)", ""),
        ("nota_despacho", "TEXT", ""),
        ("despachado_por_usuario_id", "BIGINT", "fk"),
        ("despachado_at", "TIMESTAMP", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "detalle_solicitudes": [
        ("id", "BIGINT", "pk"),
        ("solicitud_id", "BIGINT", "fk"),
        ("suministro_id", "BIGINT", "fk"),
        ("cantidad", "INT UNSIGNED", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
    "movimiento_inventario": [
        ("id", "BIGINT", "pk"),
        ("inventario_id", "BIGINT", "fk"),
        ("tipo", "ENUM", ""),
        ("cantidad", "INT", ""),
        ("usuario_id", "BIGINT", "fk"),
        ("solicitud_id", "BIGINT", "fk"),
        ("observacion", "VARCHAR(255)", ""),
        ("fecha_movimiento", "TIMESTAMP", ""),
        ("created_at", "TIMESTAMP", ""),
        ("updated_at", "TIMESTAMP", ""),
    ],
}

# Posiciones (x, y) — layout tipo Workbench
POS: dict[str, tuple[int, int]] = {
    "permisos": (120, 80),
    "rol_permiso": (120, 380),
    "roles": (120, 620),
    "usuarios": (520, 520),
    "sucursales": (920, 120),
    "bitacora": (920, 520),
    "suministros": (1320, 80),
    "inventario": (1320, 480),
    "solicitudes": (520, 980),
    "detalle_solicitudes": (120, 980),
    "movimiento_inventario": (1320, 920),
}

# FK lines: (from_table, from_col, to_table, to_col)
FKS = [
    ("rol_permiso", "rol_id", "roles", "id"),
    ("rol_permiso", "permiso_id", "permisos", "id"),
    ("usuarios", "rol_id", "roles", "id"),
    ("usuarios", "sucursal_id", "sucursales", "id"),
    ("bitacora", "usuario_id", "usuarios", "id"),
    ("solicitudes", "usuario_id", "usuarios", "id"),
    ("solicitudes", "sucursal_id", "sucursales", "id"),
    ("solicitudes", "despachado_por_usuario_id", "usuarios", "id"),
    ("inventario", "sucursal_id", "sucursales", "id"),
    ("inventario", "suministro_id", "suministros", "id"),
    ("detalle_solicitudes", "solicitud_id", "solicitudes", "id"),
    ("detalle_solicitudes", "suministro_id", "suministros", "id"),
    ("movimiento_inventario", "inventario_id", "inventario", "id"),
    ("movimiento_inventario", "usuario_id", "usuarios", "id"),
    ("movimiento_inventario", "solicitud_id", "solicitudes", "id"),
]


def load_font(size: int) -> ImageFont.FreeTypeFont | ImageFont.ImageFont:
    for name in ("arial.ttf", "Arial.ttf", "segoeui.ttf"):
        try:
            return ImageFont.truetype(name, size)
        except OSError:
            continue
    return ImageFont.load_default()


def table_size(cols: list, font: ImageFont.ImageFont, font_sm: ImageFont.ImageFont) -> tuple[int, int]:
    tw = 340
    hh = 36
    rh = 26
    return tw, hh + len(cols) * rh


def draw_table(
    draw: ImageDraw.ImageDraw,
    name: str,
    x: int,
    y: int,
    font: ImageFont.ImageFont,
    font_sm: ImageFont.ImageFont,
) -> tuple[int, int, int, int, dict[str, int]]:
    cols = TABLES[name]
    tw, th = table_size(cols, font, font_sm)
    draw.rectangle([x, y, x + tw, y + th], fill="#FFFFFF", outline=BORDER, width=1)
    draw.rectangle([x, y, x + tw, y + 36], fill=HEADER)
    draw.text((x + 10, y + 8), name, fill=HEADER_TEXT, font=font)
    col_y: dict[str, int] = {}
    rh = 26
    for i, (col, typ, kind) in enumerate(cols):
        ry = y + 36 + i * rh
        if i % 2 == 1:
            draw.rectangle([x + 1, ry, x + tw - 1, ry + rh], fill=ROW_ALT)
        ix = x + 8
        if "pk" in kind:
            draw.rectangle([ix, ry + 6, ix + 10, ry + 18], fill=PK_COLOR, outline="#AA7700")
            ix += 18
        elif kind == "fk":
            draw.polygon(
                [(ix + 5, ry + 5), (ix + 12, ry + 10), (ix + 5, ry + 15), (ix - 2, ry + 10)],
                fill=FK_COLOR,
            )
            ix += 18
        elif kind == "pkfk":
            draw.rectangle([ix, ry + 6, ix + 10, ry + 18], fill=PK_COLOR, outline="#AA7700")
            draw.polygon(
                [(ix + 14, ry + 5), (ix + 21, ry + 10), (ix + 14, ry + 15), (ix + 7, ry + 10)],
                fill=FK_COLOR,
            )
            ix += 28
        draw.text((ix, ry + 4), col, fill="#111111", font=font_sm)
        draw.text((x + tw - 120, ry + 4), typ, fill="#555555", font=font_sm)
        col_y[col] = ry + rh // 2
    return x, y, x + tw, y + th, col_y


def main() -> None:
    img = Image.new("RGB", (W, H), BG)
    draw = ImageDraw.Draw(img)
    font = load_font(18)
    font_sm = load_font(14)

    boxes: dict[str, tuple[int, int, int, int, dict[str, int]]] = {}
    for name, (px, py) in POS.items():
        boxes[name] = draw_table(draw, name, px, py, font, font_sm)

    for ft, fc, tt, tc in FKS:
        x1, y1, x2, y2, cy1 = boxes[ft]
        tx1, ty1, tx2, ty2, cy2 = boxes[tt]
        sx, sy = x2, y1 + 36 + list(c[0] for c in TABLES[ft]).index(fc) * 26 + 13
        # anchor from right side of from-table to left of to-table
        sy = y1 + cy1[fc]
        ty_mid = ty1 + cy2[tc]
        sx = x2
        tx = tx1
        mx = (sx + tx) // 2
        draw.line([(sx, sy), (mx, sy), (mx, ty_mid), (tx, ty_mid)], fill="#666666", width=2)

    OUT.parent.mkdir(parents=True, exist_ok=True)
    img.save(OUT, "PNG", optimize=True)
    desktop = Path.home() / "Desktop" / OUT.name
    img.save(desktop, "PNG", optimize=True)
    print(f"Guardado: {OUT}")
    print(f"Copia: {desktop}")


if __name__ == "__main__":
    main()
