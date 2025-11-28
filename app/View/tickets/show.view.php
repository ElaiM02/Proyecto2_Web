<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">

    <h1 class="mb-4">
        Detalle del Ticket #<?= htmlspecialchars($ticket->id_ticket) ?>
    </h1>

    <a href="/tickets" class="btn btn-secondary mb-4">
        Volver al listado
    </a>

    <!-- Información general del ticket -->
    <div class="card mb-4">
        <div class="card-body">

            <h4 class="card-title mb-3">
                <?= htmlspecialchars($ticket->titulo) ?>
            </h4>

            <p>
                <strong>Tipo:</strong>
                <?= htmlspecialchars($ticket->tipo_nombre) ?>
            </p>

            <p>
                <strong>Estado actual:</strong>
                <?= htmlspecialchars($ticket->estado_nombre) ?>
            </p>

            <p>
                <strong>Creado por:</strong>
                <?= htmlspecialchars($ticket->creador_nombre) ?>
            </p>

            <p>
                <strong>Operador asignado:</strong>
                <?= $ticket->operador_nombre
                    ? htmlspecialchars($ticket->operador_nombre)
                    : 'No asignado' ?>
            </p>

            <p>
                <strong>Fecha de creación:</strong>
                <?= htmlspecialchars($ticket->creado_en) ?>
            </p>

            <?php if ($ticket->actualizado_en): ?>
                <p>
                    <strong>Última actualización:</strong>
                    <?= htmlspecialchars($ticket->actualizado_en) ?>
                </p>
            <?php endif; ?>

            <hr>

            <h5 class="mb-2">
                <strong>Descripción inicial:</strong>
            </h5>
            <p><?= nl2br(htmlspecialchars($ticket->descripcion_inicial)) ?></p>

        </div>
    </div>

    <!-- Historial de entradas -->
    <h3 class="mb-3">Historial de actualizaciones</h3>

    <?php if (empty($entradas)): ?>
        <p>No hay entradas registradas aún.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Autor</th>
                    <th>Comentario</th>
                    <th>Cambio de estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entradas as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e->creado_en) ?></td>

                        <td><?= htmlspecialchars($e->autor_nombre) ?></td>

                        <td><?= nl2br(htmlspecialchars($e->texto)) ?></td>

                        <td>
                            <?php if ($e->estado_nuevo_nombre): ?>
                                <?= htmlspecialchars($e->estado_anterior_nombre ?? '—') ?>
                                →
                                <?= htmlspecialchars($e->estado_nuevo_nombre) ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
