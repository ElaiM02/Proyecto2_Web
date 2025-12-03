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
    // Leer parámetros de búsqueda
    $usuario = trim($_GET['usuario'] ?? '');
    $tipo    = isset($_GET['tipo']) ? (int) $_GET['tipo'] : 0;
    $desde   = $_GET['desde'] ?? '';
    $hasta   = $_GET['hasta'] ?? '';

    // Obtener tickets filtrados
    $tickets = Ticket::buscarTickets($usuario, $tipo, $desde, $hasta);

    // Tipos de ticket para el selector
    $tipos = Ticket::tipos();

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

        // Si quieres restringir solo a rol 3, descomenta esto:
        /*
        if ($_SESSION['user']['id_rol'] != 3) {
            http_response_code(403);
            echo "No autorizado";
            exit;
        }
        */

        $tipos   = Ticket::tipos();
        $errors  = $_SESSION['ticket_errors']  ?? [];
        $old     = $_SESSION['ticket_old']     ?? [
            'titulo'      => '',
            'descripcion' => '',
            'tipo'        => '',
        ];
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

        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $tipo        = (int)($_POST['tipo'] ?? 0);

        $errores = [];

        if ($titulo === '') {
            $errores[] = "El título es obligatorio.";
        }

        if ($descripcion === '') {
            $errores[] = "La descripción es obligatoria.";
        }

        if ($tipo <= 0) {
            $errores[] = "Debes seleccionar un tipo.";
        }

        if (!empty($errores)) {
            $_SESSION['ticket_errors'] = $errores;
            $_SESSION['ticket_old'] = [
                'titulo'      => $titulo,
                'descripcion' => $descripcion,
                'tipo'        => $tipo,
            ];
            header('Location: /tickets/create');
            exit;
        }

        // USER ID desde la sesión
        $creador = $_SESSION['user']['id_usuario'];

        try {
            $id = Ticket::crear($titulo, $descripcion, $tipo, $creador);

            $_SESSION['ticket_success'] = "Ticket creado correctamente (#{$id})";
            header('Location: /tickets/create');
            exit;

        } catch (\Exception $e) {
            $_SESSION['ticket_errors'] = ["Error: " . $e->getMessage()];
            header('Location: /tickets/create');
            exit;
        }
    }

    /**
     * Ver detalle de un ticket (usando ?id=)
     */
    public function show()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Tomamos el id desde la query string: /tickets/show?id=26
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo "ID de ticket inválido.";
            exit;
        }

        // Obtener ticket con relaciones
        $ticket = Ticket::findWithRelations($id);

        if (!$ticket) {
            http_response_code(404);
            echo "Ticket no encontrado";
            exit;
        }

        // Obtener historial
        $entradas = Ticket::entradas($id);

        require __DIR__ . '/../View/tickets/show.view.php';
    }
}
