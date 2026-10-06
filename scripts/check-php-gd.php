<?php

echo 'PHP ' . PHP_VERSION . PHP_EOL;
echo 'php.ini: ' . (php_ini_loaded_file() ?: '(ninguno)') . PHP_EOL;
echo 'GD: ' . (extension_loaded('gd') ? 'activa' : 'NO activa — descomente extension=gd en php.ini y reinicie artisan serve') . PHP_EOL;
