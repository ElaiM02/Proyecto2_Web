<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1>Mis tickets asignados</h1>

<?php if (empty($tickets)): ?>
    <p>No tienes tickets asignados actualmente.</p>
<?php else: ?>
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Creado en</th>
                <th>Ver</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $t): ?>
                <tr>
                    <td><?= (int) $t->id_ticket ?></td>
                    <td><?= htmlspecialchars($t->titulo) ?></td>
                    <td><?= htmlspecialchars($t->tipo_ticket) ?></td>
                    <td><?= htmlspecialchars($t->estado_ticket) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($t->creado_en)) ?></td>
                    <td>
                        <a href="/tickets/show?id=<?= (int) $t->id_ticket ?>"
                        class="btn btn-sm btn-outline-primary" title="Ver detalles">Ver</a>>Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
