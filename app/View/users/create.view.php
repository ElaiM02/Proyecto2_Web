<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>Crear Usuario</h1>

    <form action="/users/store" method="POST">
        <div class="mb-3">
            <label for="fullname" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="fullname" name="fullname" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Nombre de usuario (username)</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Rol del sistema</label>
            <select class="form-select" id="role" name="role" required>
                <option value="">Seleccione un rol</option>
                <option value="Superadministrador">Superadministrador</option>
                <option value="Operador">Operador</option>
                <option value="Usuario">Usuario</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="/users" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
