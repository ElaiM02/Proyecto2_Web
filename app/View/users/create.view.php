<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>Crear Usuario</h1>

    <form action="/users/create" method="POST">
        <div class="mb-3">
            <label for="nombre_completo" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
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
            <label class="form-label">Rol</label>
            <select class="form-select" name="id_rol" required>
                <option value="">-- Seleccione un rol --</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id_rol'] ?>"
                        <?= ($_POST['id_rol'] ?? '') == $rol['id_rol'] ? 'selected' : '' ?>>                        
                        <?= htmlspecialchars($rol['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Debe seleccionar un rol válido</div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="/users" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
