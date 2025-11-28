<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use Exception;

class Ticket extends Model
{
    /**
     * Listar tickets (ya lo tenías)
     */
    public static function all()
    {
        $stmt = self::connection()->prepare("
            SELECT
                t.id_ticket,
                t.titulo,
                tt.nombre AS tipo_ticket,
                te.nombre AS estado_ticket,
                t.creado_en
            FROM ticket t
            JOIN ticket_tipo tt ON t.id_tipo_ticket = tt.id_tipo_ticket
            JOIN ticket_estado te ON t.id_estado_ticket = te.id_estado_ticket
            ORDER BY t.creado_en DESC
        ");
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
     * Obtener ID del estado (NO_ASIGNADO)
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

        return (int)$id;
    }


    /**
     * Crear ticket + primera entrada en ticket_entrada
     */
    public static function crear(string $titulo, string $descripcion, int $tipo, int $creador)
    {
        $pdo = self::connection();
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
                ':titulo'    => $titulo,
                ':descripcion' => $descripcion,
                ':tipo'      => $tipo,
                ':estado'    => $estadoInicial,
                ':creador'   => $creador
            ]);

            $ticketId = $pdo->lastInsertId();

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
                ':estado' => $estadoInicial
            ]);

            $pdo->commit();
            return $ticketId;

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
