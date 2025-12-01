<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use Exception;

class Ticket extends Model
{
    /**
     * Listar todos los tickets para el index
     */
public static function all()
{
    $sql = "
        SELECT
            t.id_ticket,
            t.titulo,
            tt.nombre AS tipo_ticket,
            te.nombre AS estado_ticket,
            t.creado_en,
            uc.nombre_completo AS creador_nombre
        FROM ticket t
        JOIN ticket_tipo tt ON t.id_tipo_ticket = tt.id_tipo_ticket
        JOIN ticket_estado te ON t.id_estado_ticket = te.id_estado_ticket
        JOIN usuario uc ON t.id_usuario_creador = uc.id_usuario
        WHERE 1=1
    ";

    $rol = $_SESSION['user']['rol_nombre'] ?? '';

    // 1. USUARIO → solo ve sus propios tickets
    if ($rol === 'USUARIO') {
        $sql .= " AND t.id_usuario_creador = :user_id";

    // 2. OPERADOR → solo ve tickets NO_ASIGNADO
    } elseif ($rol === 'OPERADOR') {
        $sql .= " AND te.nombre = 'NO_ASIGNADO'";

    }
    // 3. SUPERADMIN → ve TODO (no agregamos nada)

    $sql .= " ORDER BY t.creado_en DESC";

    $stmt = self::connection()->prepare($sql);

    // Solo bind si es USUARIO
    if ($rol === 'USUARIO') {
        $stmt->bindValue(':user_id', $_SESSION['user']['id_usuario'], PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

    /**
     * Tipos de ticket para el SELECT
     */
    public static function tipos()
    {
        $stmt = self::connection()->prepare("
            SELECT id_tipo_ticket, nombre
            FROM ticket_tipo
            ORDER BY nombre
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener ID de un estado por nombre (ej: 'NO_ASIGNADO')
     */
    protected static function estadoId(string $nombre): int
    {
        $stmt = self::connection()->prepare("
            SELECT id_estado_ticket
            FROM ticket_estado
            WHERE nombre = :nombre
            LIMIT 1
        ");

        $stmt->execute([':nombre' => $nombre]);

        $id = $stmt->fetchColumn();

        if (!$id) {
            throw new Exception("No existe el estado '$nombre' en ticket_estado");
        }

        return (int) $id;
    }

    /**
     * Crear ticket + primera entrada en ticket_entrada
     */
    public static function crear(string $titulo, string $descripcion, int $tipo, int $creador)
    {
        $pdo           = self::connection();
        $estadoInicial = self::estadoId('NO_ASIGNADO');

        try {
            $pdo->beginTransaction();

            // Insertar ticket
            $stmt = $pdo->prepare("
                INSERT INTO ticket (
                    titulo,
                    descripcion_inicial,
                    id_tipo_ticket,
                    id_estado_ticket,
                    id_usuario_creador,
                    id_operador_asignado
                ) VALUES (
                    :titulo,
                    :descripcion,
                    :tipo,
                    :estado,
                    :creador,
                    NULL
                )
            ");

            $stmt->execute([
                ':titulo'      => $titulo,
                ':descripcion' => $descripcion,
                ':tipo'        => $tipo,
                ':estado'      => $estadoInicial,
                ':creador'     => $creador,
            ]);

            $ticketId = (int) $pdo->lastInsertId();

            // Insertar entrada inicial
            $stmt2 = $pdo->prepare("
                INSERT INTO ticket_entrada (
                    id_ticket,
                    id_autor,
                    texto,
                    id_estado_anterior,
                    id_estado_nuevo
                )
                VALUES (
                    :ticket,
                    :autor,
                    :texto,
                    NULL,
                    :estado
                )
            ");

            $stmt2->execute([
                ':ticket' => $ticketId,
                ':autor'  => $creador,
                ':texto'  => $descripcion,
                ':estado' => $estadoInicial,
            ]);

            $pdo->commit();

            return $ticketId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener un ticket con toda la información relacionada
     */
    public static function findWithRelations(int $id)
    {
        $pdo = self::connection();

        $stmt = $pdo->prepare("
            SELECT
                t.*,
                tt.nombre AS tipo_nombre,
                te.nombre AS estado_nombre,
                uc.nombre_completo AS creador_nombre,
                uo.nombre_completo AS operador_nombre
            FROM ticket t
            JOIN ticket_tipo   tt ON t.id_tipo_ticket   = tt.id_tipo_ticket
            JOIN ticket_estado te ON t.id_estado_ticket = te.id_estado_ticket
            JOIN usuario       uc ON t.id_usuario_creador = uc.id_usuario
            LEFT JOIN usuario  uo ON t.id_operador_asignado = uo.id_usuario
            WHERE t.id_ticket = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_OBJ); // null si no existe
    }

    /**
     * Historial (entradas) de un ticket
     */
    public static function entradas(int $idTicket): array
    {
        $pdo = self::connection();

        $stmt = $pdo->prepare("
            SELECT
                e.*,
                u.nombre_completo AS autor_nombre,
                ea.nombre AS estado_anterior_nombre,
                en.nombre AS estado_nuevo_nombre
            FROM ticket_entrada e
            JOIN usuario u
                ON e.id_autor = u.id_usuario
            LEFT JOIN ticket_estado ea
                ON e.id_estado_anterior = ea.id_estado_ticket
            LEFT JOIN ticket_estado en
                ON e.id_estado_nuevo = en.id_estado_ticket
            WHERE e.id_ticket = :id
            ORDER BY e.creado_en ASC
        ");

        $stmt->execute([':id' => $idTicket]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
