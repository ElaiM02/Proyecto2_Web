<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1>Listado de Tickets</h1>
    <a href="/tickets/create" class="btn btn-primary mb-3">
    Crear Ticket
</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Tipo</th>
            <th>Estado Actual</th>
            <th>Fecha de creación</th>
            <th>Detalles</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?= $ticket->id_ticket ?></td>
                    <td><?= htmlspecialchars($ticket->titulo) ?></td>
                    <td><?= htmlspecialchars($ticket->tipo_ticket) ?></td>
                    <td><?= htmlspecialchars($ticket->estado_ticket) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($ticket->creado_en)) ?></td>
                    <td><a href="/tickets/show?id=<?= $ticket->id_ticket ?>">Ver</a></td>

                </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../layouts/footer.php'; ?>