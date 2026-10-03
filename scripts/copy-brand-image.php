<?php

$assets = 'C:/Users/Jonathan/.cursor/projects/c-Users-Jonathan-Desktop-Proyecto-Gestion-de-Suministros-gestion-suministros/assets';
$dest = dirname(__DIR__) . '/frontend/public/images/fabrigas-planta.png';

$matches = glob($assets . '/*8b02d299*');
if ($matches === [] || ! is_readable($matches[0])) {
    fwrite(STDERR, "No se encontró la imagen en assets de Cursor. Copie manualmente a frontend/public/images/fabrigas-planta.png\n");
    exit(1);
}

if (! copy($matches[0], $dest)) {
    fwrite(STDERR, "Error al copiar.\n");
    exit(1);
}

echo "Imagen copiada a {$dest}\n";
