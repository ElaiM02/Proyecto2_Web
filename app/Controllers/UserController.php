<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Model;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // OPCIONAL: Solo SUPERADMIN puede gestionar usuarios
        if ($_SESSION['user']['rol_nombre'] !== 'SUPERADMIN') {
            header('Location: /home');
            exit;
        }
    }

    // Listar todos los usuarios
    public function index()
    {
        $usuarios = User::all();
        return $this->view('users/index', ['usuarios' => $usuarios]);
    }

    // Mostrar formulario para crear usuario
    public function create()
    {
        // Obtener roles para el select
        $roles = Model::connection()->query("SELECT id_rol, nombre FROM rol ORDER BY nombre")->fetchAll();
        return $this->view('users/create', ['roles' => $roles]);
    }

    // Guardar nuevo usuario
    public function store()
    {
        $data = [
            'nombre_completo' => trim($_POST['nombre_completo']),
            'username'        => trim($_POST['username']),
            'password'        => $_POST['password'],
            'id_rol'          => (int)$_POST['id_rol']
        ];

        // Validaciones básicas
        if (empty($data['nombre_completo']) || empty($data['username']) || empty($data['password'])) {
            return $this->view('users/create', [
                'error' => 'Todos los campos son obligatorios',
                'roles' => Model::connection()->query("SELECT id_rol, nombre FROM rol")->fetchAll()
            ]);
        }

        // Verificar si el username ya existe
        if (User::findByUsername($data['username'])) {
            return $this->view('users/create', [
                'error' => 'El nombre de usuario ya está en uso',
                'roles' => Model::connection()->query("SELECT id_rol, nombre FROM rol")->fetchAll()
            ]);
        }

        User::create($data);
        header('Location: /users');
        exit;
    }

    // Mostrar formulario para editar usuario
    public function edit($id)
    {
        $usuario = User::find($id);
        $roles = Model::connection()->query("SELECT id_rol, nombre FROM rol ORDER BY nombre")->fetchAll();

        if (!$usuario) {
            header('Location: /users');
            exit;
        }

        return $this->view('users/edit', [
            'usuario' => $usuario,
            'roles'   => $roles
        ]);
    }

    // Actualizar usuario
    public function update($id)
    {
        $usuarioActual = User::find($id);
        if (!$usuarioActual) {
            header('Location: /users');


            exit;
        }

        $data = [
            'username' => trim($_POST['username'])
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        // Validar username único (excepto el usuario actual)
        $existe = User::findByUsername($data['username']);
        if ($existe && $existe->id_usuario != $id) {
            $roles = Model::connection()->query("SELECT id_rol, nombre FROM rol")->fetchAll();
            return $this->view('users/edit', [
                'usuario' => $usuarioActual,
                'roles'   => $roles,
                'error'   => 'El nombre de usuario ya está en uso'
            ]);
        }

        User::update($id, $data);
        header('Location: /users');
        exit;
    }

    // Desactivar usuario (soft delete)
    public function deactivate($id)
    {
        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = 'No puedes desactivarte a ti mismo';
        } else {
            User::deactivate($id);
            $_SESSION['success'] = 'Usuario desactivado correctamente';
        }
        header('Location: /users');
        exit;
    }
}