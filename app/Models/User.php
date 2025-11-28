<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    /**
     * Buscar un usuario por username (para login)
     */
    public static function findByUsername($username)
{
    $sql = "SELECT u.*, r.nombre AS rol_nombre 
            FROM usuario u 
            INNER JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.username = :username AND u.activo = 1";
    
    $statement = self::connection()->prepare($sql);
    $statement->bindValue(':username', $username);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_OBJ);
}
    
    /**
     * Obtener todos los usuarios
     */
    public static function all()
    {
        $statement = self::connection()->query("SELECT usuario.*, rol.descripcion AS rol_descripcion
            FROM usuario INNER JOIN rol ON usuario.id_rol = rol.id_rolORDER BY usuario.id_usuario DESC");
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Crear un nuevo usuario
     */
    public static function create($data)
    {
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $statement = self::connection()->prepare("INSERT INTO usuario (nombre_completo, username, password_hash, id_rol, activo)
            VALUES (:nombre_completo, :username, :password_hash, :id_rol, 1)");
        $statement->bindValue(':nombre_completo', $data['nombre_completo']);
        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':password_hash', $passwordHash);
        $statement->bindValue(':id_rol', $data['id_rol']);
        return $statement->execute();
    }
   
    /**
     * Buscar usuario por ID
     */
    public static function find($id)
{
    $sql = "SELECT u.*, r.nombre AS rol_nombre 
            FROM usuario u 
            INNER JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.id_usuario = :id";
    
    $statement = self::connection()->prepare($sql);
    $statement->bindValue(':id', $id);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_OBJ);
}

    /**
     * Actualizar un usuario
     */
    public static function update($id, $data)
{
    // Campos permitidos
    $campos = [];
    $valores = [];

    // Solo actualizamos lo que viene en $data y que está permitido
    if (isset($data['username'])) {
        $campos[] = "username = :username";
        $valores[':username'] = trim($data['username']);
    }

    if (!empty($data['password'])) {
        $campos[] = "password_hash = :password_hash";
        $valores[':password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
    }

    // Si no hay nada que actualizar, salimos
    if (empty($campos)) {
        return true;
    }

    // Siempre actualizamos la fecha
    $campos[] = "actualizado_en = NOW()";

    $sql = "UPDATE usuario SET " . implode(', ', $campos) . " WHERE id_usuario = :id";
    $valores[':id'] = $id;

    $stmt = self::connection()->prepare($sql);
    return $stmt->execute($valores);
}

    /**
     * Desactivar usuario (no borrar)
     */
    public static function deactivate($id)
    {
        $statement = self::connection()->prepare("UPDATE usuario SET activo = 0, actualizado_en = NOW() WHERE id_usuario = :id");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }
}
