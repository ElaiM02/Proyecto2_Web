<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Ticket extends Model
{
    public static function all()
    {
        $statement = self::connection()->prepare("
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
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}