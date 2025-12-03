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

// Tomamos el rol desde sesión
     $rol       = strtoupper(trim($_SESSION['user']['rol_nombre'] ?? ''));
     $idUsuario = (int)($_SESSION['user']['id_usuario'] ?? 0);

 // Elegir qué tickets cargar según el rol
    if ($rol === 'OPERADOR') {
        // OPERADOR → solo tickets NO_ASIGNADO
        $tickets = Ticket::buscarTickets($usuario, $tipo, $desde, $hasta, 'NO_ASIGNADO', null);

    } elseif ($rol === 'USUARIO') {
        // USUARIO → solo tickets creados por él
       $tickets = Ticket::buscarTicketsDeUsuario($usuario, $tipo, $desde, $hasta, $idUsuario);

    } else {
        // SUPERADMIN (u otros) → todos los tickets
        $tickets = Ticket::buscarTickets($usuario, $tipo, $desde, $hasta, null, null);
    }

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
        $creador     = $_SESSION['user']['id_usuario'];


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

        // SUBIDA DE IMAGEN 
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['imagen'];
            $maxSize = 3 * 1024 * 1024;

            // 1. Validar tamaño
            if ($file['size'] > $maxSize) {
                $errores[] = "La imagen no puede pesar más de 3MB.";
            } 
            // 2. Validar extensión del archivo (100% confiable y sin extensiones)
            else {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($extension, $permitidas)) {
                    $errores[] = "Solo se permiten imágenes: JPG, JPEG, PNG, GIF o WebP.";
                }
            }
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

        // SUBIR IMAGEN SI PASÓ LAS VALIDACIONES
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK && empty($errores)) {
            $uploadDir = __DIR__ . '/../../public/uploads/tickets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'ticket_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $imagen = '/uploads/tickets/' . $filename;
            }
        }

        try {
            $id = Ticket::crear($titulo, $descripcion, $tipo, $creador, $imagen);

            $_SESSION['ticket_success'] = "Ticket creado correctamente (#{$id})";
            header('Location: /tickets');
            exit;

        } catch (\Exception $e) {
            $_SESSION['ticket_errors'] = ["Error del sistema: " . $e->getMessage()];
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
    public function misAsignados()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }

    if (strtoupper($_SESSION['user']['rol_nombre'] ?? '') !== 'OPERADOR') {
        http_response_code(403);
        echo "No autorizado";
        exit;
    }

    $idOperador = (int) $_SESSION['user']['id_usuario'];

    $tickets = Ticket::ticketsAsignados($idOperador);

    
    $rol        = strtoupper($_SESSION['user']['rol_nombre'] ?? '');
    $esOperador = ($rol === 'OPERADOR');
    $yaAsignado = !empty($ticket->id_operador_asignado);
    

    require __DIR__ . '/../View/tickets/asignacion.view.php';
}

public function asignar()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }

    if (strtoupper($_SESSION['user']['rol_nombre'] ?? '') !== 'OPERADOR') {
        http_response_code(403);
        echo "No autorizado";
        exit;
    }

    $idTicket = (int) ($_POST['id_ticket'] ?? 0);

    if ($idTicket <= 0) {
        http_response_code(400);
        echo "ID de ticket inválido";
        exit;
    }

    $idOperador = (int) $_SESSION['user']['id_usuario'];

    Ticket::asignarTicket($idTicket, $idOperador);

    // volver al detalle del ticket
    header("Location: /tickets/show?id=" . $idTicket);
    exit;
}

}
