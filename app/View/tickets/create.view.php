<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>Crear Ticket de Soporte</h1>
    
    <a href="/tickets/create" class="btn btn-primary mb-3">
    Crear Ticket
</a>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form action="/tickets/create" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label class="form-label">Título de la solicitud</label>
            <input type="text" class="form-control" name="titulo"
                   value="<?= htmlspecialchars($old['titulo'] ?? '') ?>" maxlength="200" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de solicitud</label>
            <select class="form-select" name="tipo" required>
                <option value="">Seleccione un tipo</option>

                <?php foreach($tipos as $t): ?>
                    <option value="<?= $t->id_tipo_ticket ?>"
                        <?= ($old['tipo'] ?? '') == $t->id_tipo_ticket ? 'selected':'' ?>>
                        <?= htmlspecialchars($t->nombre) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="5"
                      required><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del problema (opcional)</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            <div class="form-text">
                Subí una captura de pantalla si te ayuda a explicar el problema. Máximo 2MB.
            </div>
        </div>

        <button class="btn btn-primary">Crear Ticket</button>
        <a href="/tickets" class="btn btn-secondary">Cancelar</a>

    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>