<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
        return $this->view('auth/login');
    }

    public function authenticate()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = User::findByUsername($username);


         if ($user) {

            if (password_verify($password, $user->password_hash)) {
                $_SESSION['user'] = [
                    'id_usuario' => $user->id_usuario,
                    'username'   => $user->username,
                    'nombre'     => $user->nombre_completo,
                    'id_rol'     => $user->id_rol,
                    'rol_nombre'      => $user->rol_nombre,
                    'activo'          => $user->activo ?? 1
                ];
                header('Location: /');
                exit;
            }
        }

        return $this->view('auth/login', [
            'error' => 'Credenciales inválidas'
        ]);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}