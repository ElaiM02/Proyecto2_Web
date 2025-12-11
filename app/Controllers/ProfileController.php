<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public function edit()
    {
        $user = User::find($_SESSION['user']['id_usuario']);
        return $this->view('profile/edit', ['user' => $user]);
    }

    public function update()
    {
        $id = $_SESSION['user']['id_usuario'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (!empty($password) && $password !== $confirm_password) {
            $user = User::find($id);
            return $this->view('profile/edit', ['user' => $user, 'error' => 'Las contraseñas no coinciden']);
        }

        $existingUser = User::findByUsername($username);
        if ($existingUser && $existingUser->id_usuario != $id) {
            $user = User::find($id);
            return $this->view('profile/edit', ['user' => $user, 'error' => 'El nombre de usuario ya está en uso']);
        }

        $data = ['username' => $username];
        if (!empty($password)) {
            $data['password'] = $password;
        }

        User::update($id, $data);
        
        $_SESSION['user']['username'] = $username;

        $user = User::find($id);
        return $this->view('profile/edit', ['user' => $user, 'success' => 'Perfil actualizado correctamente']);
    }
}
