<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <?php if ($_SESSION['user']['rol_nombre'] === 'USUARIO'): ?>
            Mis Tickets
        <?php else: ?>
            Listado de Tickets
        <?php endif; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/tickets/create" class="btn btn-sm btn-primary">
            Crear Ticket
        </a>
    </div>
</div>

<!-- MENSAJE CONTEXTUAL SEGÚN EL ROL -->
<?php if ($_SESSION['user']['rol_nombre'] === 'USUARIO'): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Área de Usuario</strong> – Aquí solo puedes ver los tickets que creaste tú.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($_SESSION['user']['rol_nombre'] === 'OPERADOR'): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <strong>Cola de Operador</strong> – Aquí ves los tickets pendientes de asignar. ¡Tomá uno y ayudá al usuario!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

<?php else: /* SUPERADMIN */ ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Panel de Administración</strong> – Tienes acceso completo a todos los tickets.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- TABLA DE TICKETS -->
<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Estado Actual</th>
                <th>Creado el</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <?php if ($_SESSION['user']['rol_nombre'] === 'USUARIO'): ?>
                            Aún no has creado ningún ticket.
                        <?php else: ?>
                            No hay tickets registrados en el sistema.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><strong>#<?= $ticket->id_ticket ?></strong></td>
                        <td><?= htmlspecialchars($ticket->titulo) ?></td>
                        <td><?= htmlspecialchars($ticket->tipo_ticket) ?></td>
                        <td>
                            <span class="badge bg-<?= $ticket->estado_ticket === 'CERRADO' ? 'success' : ($ticket->estado_ticket === 'EN_PROCESO' ? 'warning' : 'secondary') ?>">
                                <?= htmlspecialchars($ticket->estado_ticket) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($ticket->creado_en)) ?></td>
                        <td>
                            <a href="/tickets/show?id=<?= $ticket->id_ticket ?>" 
                               class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                Ver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>