<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Usuario</h1>
    <a href="/users" class="btn btn-sm btn-outline-secondary">Volver al listado</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/users/edit/<?= $usuario->id_usuario ?>" method="POST" class="needs-validation" novalidate>
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" name="nombre_completo" 
                               value="<?= htmlspecialchars($usuario->nombre_completo ?? '') ?>" required>
                        <div class="invalid-feedback">Campo obligatorio</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre de usuario (username)</label>
                        <input type="text" class="form-control" name="username" 
                               value="<?= htmlspecialchars($usuario->username ?? '') ?>" required>
                        <div class="invalid-feedback">Campo obligatorio</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña <small class="text-muted">(dejar en blanco si no desea cambiarla)</small></label>
                        <input type="password" class="form-control" name="password" placeholder="Nueva contraseña">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol del sistema</label>
                        <select class="form-select" name="id_rol" required>
                            <option value="">-- Seleccione un rol --</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol['id_rol'] ?? $rol->id_rol ?>" 
                                    <?= ($usuario->id_rol ?? $usuario->id_rol) == ($rol['id_rol'] ?? $rol->id_rol) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rol['nombre'] ?? $rol->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Debe seleccionar un rol</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="/users" class="btn btn-secondary ms-2">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>