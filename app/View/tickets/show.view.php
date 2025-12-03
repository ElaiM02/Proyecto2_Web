<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">

    <!-- TÍTULO + BOTÓN VOLVER -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="mb-1">
                Ticket #<?= $ticket->id_ticket ?>
                <span class="badge bg-<?= 
                    $ticket->estado_nombre === 'CERRADO' ? 'success' : 
                    ($ticket->estado_nombre === 'EN_PROCESO' ? 'warning' : 'secondary') ?> fs-6">
                    <?= htmlspecialchars($ticket->estado_nombre) ?>
                </span>
            </h1>
            
            <h4 class="text-muted"><?= htmlspecialchars($ticket->titulo) ?></h4>
        </div>
        <a href="/tickets" class="btn btn-outline-secondary">
            Volver al listado
        </a>
    </div>

    <!-- ALERTA SEGÚN ROL -->
    <?php if ($_SESSION['user']['rol_nombre'] === 'USUARIO'): ?>
        <div class="alert alert-info">
            <strong>Área de Usuario</strong> – Este es tu ticket. El equipo de soporte lo revisará pronto.
        </div>
    <?php elseif ($_SESSION['user']['rol_nombre'] == 'OPERADOR'): ?>
    <div class="alert alert-warning">
        <strong>Operador</strong> Puedes responder y actualizar el estado de este ticket.
    </div>

   <?php if (
    isset($_SESSION['user']) &&
    ( $_SESSION['user']['rol_nombre'] ?? '' ) === 'OPERADOR' &&
    empty($ticket->id_operador_asignado)
    ): ?>
    <form action="/tickets/asignar" method="POST" class="mb-3">
        <input type="hidden" name="id_ticket" value="<?= (int)$ticket->id_ticket ?>">
        <button type="submit" class="btn btn-primary">
            Asignación de Ticket
        </button>
    </form>
    <?php endif; ?>


<?php else: ?>
        <div class="alert alert-success">
            <strong>Administrador</strong> – Tienes control total sobre este ticket.
        </div>
    <?php endif; ?>

    <!-- CARD CON INFO DEL TICKET -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tipo:</strong> <?= htmlspecialchars($ticket->tipo_nombre) ?></p>
                    <p><strong>Creado por:</strong> <?= htmlspecialchars($ticket->creador_nombre) ?></p>
                    <p><strong>Fecha de creación:</strong> <?= date('d/m/Y H:i', strtotime($ticket->creado_en)) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Operador asignado:</strong> 
                        <?= $ticket->operador_nombre 
                            ? '<span class="text-success fw-bold">' . htmlspecialchars($ticket->operador_nombre) . '</span>' 
                            : '<span class="text-muted">No asignado</span>' ?>
                    </p>
                    <?php if ($ticket->actualizado_en): ?>
                        <p><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($ticket->actualizado_en)) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <h5 class="mb-3"><strong>Descripción inicial</strong></h5>
            <div class="bg-light p-3 rounded">
                <p class="mb-0"><?= nl2br(htmlspecialchars($ticket->descripcion_inicial)) ?></p>
            </div>
        </div>
    </div>

    <!-- BOTÓN RESPONDER (solo OPERADOR y SUPERADMIN) -->
    <?php if (in_array($_SESSION['user']['rol_nombre'], ['OPERADOR', 'SUPERADMIN'])): ?>
        <div class="mb-4">
            <a href="/tickets/reply/<?= $ticket->id_ticket ?>" class="btn btn-primary btn-lg">
                Asignación de Ticket
            </a>
        </div>
    <?php endif; ?>

    <!-- HISTORIAL -->
    <h3 class="mb-4">Historial de actualizaciones</h3>

    <?php if (empty($entradas)): ?>
        <div class="text-center py-5 text-muted">
            <p class="lead">Aún no hay respuestas ni actualizaciones.</p>
            <?php if ($_SESSION['user']['rol_nombre'] === 'USUARIO'): ?>
                <small>El equipo de soporte revisará tu solicitud pronto.</small>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="timeline">
            <?php foreach ($entradas as $e): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?= htmlspecialchars($e->autor_nombre) ?></strong>
                                <small class="text-muted">· <?= date('d/m/Y H:i', strtotime($e->creado_en)) ?></small>
                            </div>
                            <?php if ($e->estado_nuevo_nombre): ?>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($e->estado_anterior_nombre ?? '—') ?> → <?= htmlspecialchars($e->estado_nuevo_nombre) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <hr>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($e->texto)) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>