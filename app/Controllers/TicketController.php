<?php

namespace App\Controllers;

use App\Models\Ticket;

class TicketController
{
    /**
     * Lista de tickets
     */
    public function index()
    {
        $tickets = Ticket::all();
        require __DIR__ . '/../View/tickets/index.view.php';

    }



    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // // Solo usuarios (rol 3) pueden crear tickets
        // if ($_SESSION['user']['id_rol'] != 3) {
        //     http_response_code(403);
        //     echo "No autorizado";
        //     exit;
        // }

        $tipos = Ticket::tipos();
        $errors  = $_SESSION['ticket_errors'] ?? [];
        $old     = $_SESSION['ticket_old'] ?? ['titulo'=>'','descripcion'=>'','tipo'=>''];
        $success = $_SESSION['ticket_success'] ?? null;

        unset($_SESSION['ticket_errors'], $_SESSION['ticket_old'], $_SESSION['ticket_success']);

        require __DIR__ . '/../View/tickets/create.view.php';
    }



    /**
     * Guardar ticket
     */
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $tipo = (int)($_POST['tipo'] ?? 0);

        $errores = [];

        if ($titulo === '') { $errores[] = "El título es obligatorio."; }
        if ($descripcion === '') { $errores[] = "La descripción es obligatoria."; }
        if ($tipo <= 0) { $errores[] = "Debes seleccionar un tipo."; }

        if (!empty($errores)) {
            $_SESSION['ticket_errors'] = $errores;
            $_SESSION['ticket_old'] = [
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'tipo' => $tipo
            ];
            header('Location: /tickets/create');
            exit;
        }

        // USER ID desde la sesión
        $creador = $_SESSION['user']['id_usuario'];

        try {
            $id = Ticket::crear($titulo, $descripcion, $tipo, $creador);

            $_SESSION['ticket_success'] = "Ticket creado correctamente (#$id)";
            header('Location: /tickets/create');
            exit;

        } catch (\Exception $e) {
            $_SESSION['ticket_errors'] = ["Error: " . $e->getMessage()];
            header('Location: /tickets/create');
            exit;
        }
    }
}
