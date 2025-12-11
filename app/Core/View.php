<?php

namespace App\Core;

class View
{
    public static function render($name, $data = [])
    {
        extract($data);

        $file = __DIR__ . '/../../app/View/' . strtolower($name) . '.view.php';

        require $file;
    }
}