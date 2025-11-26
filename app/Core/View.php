<?php

namespace App\Core;

class View
{
    public static function render($name, $data = [])
    {
        extract($data);

        // Ruta fija y segura que funciona en Windows y Linux
        $file = __DIR__ . '/../../app/View/' . strtolower($name) . '.view.php';

        // Mensaje claro si no existe (para que nunca más tengas error 500 sin saber por qué)
        if (!file_exists($file)) {
            die("<h1 style='color:red; text-align:center; margin-top:100px;'>
                     ERROR 500 - VISTA NO ENCONTRADA</h1>
                <p>Ruta buscada:</p>
                <code style='background:#f0f0f0; padding:10px; display:block;'>$file</code>
                <hr>
                <p><strong>Solución:</strong> Verifica que la carpeta se llame exactamente <code>app/View</code> (con V mayúscula) 
                y que el archivo exista: <code>$name.view.php</code></p>");
        }

        require $file;
    }
}