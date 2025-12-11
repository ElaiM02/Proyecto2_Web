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
        <a href="/tickets/create" class="btn btn-sm btn-primary">Crear Ticket</a>
    </div>
</div>

<!-- Mensaje segun el rol -->
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

<?php else: ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Panel de Administración</strong> – Tienes acceso completo a todos los tickets.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Info de Tickets -->
 <form method="GET" action="/tickets" class="row g-3 mb-4">

    <?php if ($_SESSION['user']['rol_nombre'] !== 'USUARIO'): ?>
        <div class="col-md-4">
            <label class="form-label">Usuario (nombre o username)</label>
            <input type="text" name="usuario" class="form-control"
                   value="<?= htmlspecialchars($_GET['usuario'] ?? '') ?>">
        </div>

    <?php else: ?>
        <div class="col-md-4">
            <label class="form-label">Estado del ticket</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>

                <?php foreach ($estados as $e): ?>
                    <option value="<?= $e->id_estado_ticket ?>"
                        <?= (($_GET['estado'] ?? '') == $e->id_estado_ticket) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e->nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="col-md-3">
        <label class="form-label">Tipo de ticket</label>
        <select name="tipo" class="form-select">
            <option value="0">Todos</option>
            <?php foreach($tipos as $t): ?>
                <option value="<?= $t->id_tipo_ticket ?>"
                    <?= (($_GET['tipo'] ?? 0) == $t->id_tipo_ticket) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t->nombre) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Desde</label>
        <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>">
    </div>

    <div class="col-md-2">
        <label class="form-label">Hasta</label>
        <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>">
    </div>

    <div class="col-md-1 d-flex align-items-end">
        <button class="btn btn-primary w-100">Buscar</button>
    </div>

</form>

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
                            <a href="/tickets/show?id=<?= $ticket->id_ticket ?>" class="btn btn-sm btn-outline-primary" title="Ver detalles">VerS</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>