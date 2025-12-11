<?php

namespace App\Controllers;

use App\Models\Ticket;

class TicketController
{
   public function index()
{
    // Parametros de busqueda
    $usuario = trim($_GET['usuario'] ?? '');
    $tipo    = isset($_GET['tipo']) ? (int) $_GET['tipo'] : 0;
    $desde   = $_GET['desde'] ?? '';
    $hasta   = $_GET['hasta'] ?? '';
    $estado  = $_GET['estado'] ?? '';

    $rol       = strtoupper(trim($_SESSION['user']['rol_nombre'] ?? ''));
    $idUsuario = (int)($_SESSION['user']['id_usuario'] ?? 0);

    // Busqueda de Operador
    if ($rol === 'OPERADOR') {
        $tickets = Ticket::buscarTickets(
            $usuario,
            $tipo,
            $desde,
            $hasta,
            'NO_ASIGNADO',
            null
        );

    } elseif ($rol === 'USUARIO') {
        // Busqueda de Usuarios
        $tickets = Ticket::buscarTicketsDeUsuario(
            $usuario,
            $tipo,
            $estado,
            $desde,
            $hasta,
            $idUsuario
        );

    } else {

        // Busqueda de Superadmin
        $tickets = Ticket::buscarTickets(
            $usuario,
            $tipo,
            $desde,
            $hasta,
            null,
            null
        );
    }

    $tipos = Ticket::tipos();
    $estados = Ticket::estados();

    require __DIR__ . '/../View/tickets/index.view.php';
}

    // Crear Ticket 
    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

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

    // Guardar ticket
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

        // Agregar Imagen 
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['imagen'];
            $maxSize = 3 * 1024 * 1024;

            if ($file['size'] > $maxSize) {
                $errores[] = "La imagen no puede pesar más de 3MB.";
            } else {
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

        // User de la sesión
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
     
    // Ver detalles de ticket
    public function show()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo "ID de ticket inválido.";
            exit;
        }

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

        header("Location: /tickets/show?id=" . $idTicket);
        exit;
    }

    public function cambiarEstado()
    {
        if (!in_array($_SESSION['user']['rol_nombre'] ?? '', ['OPERADOR', 'SUPERADMIN'])) {
            $_SESSION['error'] = 'Solo operadores pueden cambiar el estado.';
            header('Location: /tickets');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $nuevoEstado = $_POST['nuevo_estado'] ?? '';
        $comentario = trim($_POST['comentario'] ?? '');

        if ($id <= 0 || empty($nuevoEstado)) {
            $_SESSION['error'] = 'Datos inválidos.';
            header("Location: /tickets/show?id=$id");
            exit;
        }

        try {
            $estadoId = Ticket::estadoId($nuevoEstado);
            Ticket::cambiarEstado($id, $estadoId, $_SESSION['user']['id_usuario'], $comentario);
            $_SESSION['success'] = "Estado cambiado a: $nuevoEstado";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: /tickets/show?id=$id");
        exit;
    }

    public function aceptarSolucion()
    {


        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /tickets');
            exit;
        }

        try {
            $estadoCerrado = Ticket::estadoId('CERRADO');
            Ticket::usuarioCambiarEstado($id, $estadoCerrado, $_SESSION['user']['id_usuario'], 'Usuario aceptó la solución - ticket cerrado');
            $_SESSION['success'] = '¡Gracias! El ticket ha sido cerrado permanentemente.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cerrar el ticket: ' . $e->getMessage();
        }
        
        header("Location: /tickets/show?id=$id");
        exit;
    }

    public function denegarSolucion()
    {

        $id = (int)($_POST['id'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($id <= 0) {
            $_SESSION['error'] = 'Ticket inválido.';
            header('Location: /tickets');
            exit;
        }

        if (empty($comentario)) {
            $_SESSION['error'] = 'Debes explicar por qué rechazas la solución.';
            header("Location: /tickets/show?id=$id");
            exit;
        }

        try {
            $estadoAsignado = Ticket::estadoId('ASIGNADO');
            Ticket::usuarioCambiarEstado($id, $estadoAsignado, $_SESSION['user']['id_usuario'], 'Usuario rechazó la solución: ' . $comentario);
            $_SESSION['success'] = 'Ticket reabierto. El operador revisará tu comentario.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al reabrir el ticket: ' . $e->getMessage();
        }

        header("Location: /tickets/show?id=$id");
        exit;
    }
}