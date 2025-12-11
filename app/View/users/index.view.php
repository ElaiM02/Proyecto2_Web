<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Usuarios del Sistema</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/users/create" class="btn btn-sm btn-outline-success">
            <i class="bi bi-person-plus"></i> Crear Nuevo Usuario
        </a>
    </div>
</div>

<!-- Mensajes de exito o error -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre Completo</th>
                <th scope="col">Usuario</th>
                <th scope="col">Rol</th>
                <th scope="col">Estado</th>
                <th scope="col" class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No hay usuarios registrados aún.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario->id_usuario) ?></td>
                        <td><?= htmlspecialchars($usuario->nombre_completo) ?></td>
                        <td><?= htmlspecialchars($usuario->username) ?></td>
                        <td>
                            <span class="badge 
                                <?php
                                    switch (strtoupper($usuario->rol_descripcion ?? $usuario->rol_nombre ?? '')) {
                                        case 'SUPERADMIN': echo 'bg-danger'; break;
                                        case 'OPERADOR':   echo 'bg-primary'; break;
                                        case 'USUARIO':    echo 'bg-secondary'; break;
                                        default:           echo 'bg-info';
                                    }
                                ?>">
                                <?= strtoupper($usuario->rol_descripcion ?? $usuario->rol_nombre ?? 'SIN ROL') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $usuario->activo ? 'bg-success' : 'bg-danger' ?>">
                                <?= $usuario->activo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="/users/edit/<?= $usuario->id_usuario ?>" 
                                   class="btn btn-sm btn-warning" title="Editar">
                                    Editar
                                </a>
                                <?php if ($usuario->id_usuario != $_SESSION['user']['id_usuario']): ?>
                                    <a href="/users/deactivate/<?= $usuario->id_usuario ?>" 
                                       class="btn btn-sm btn-danger" title="Desactivar"
                                       onclick="return confirm('¿Estás seguro de desactivar este usuario?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        Yo
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>