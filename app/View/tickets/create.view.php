<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>Crear Ticket de Soporte</h1>

    <form action="/tickets/store" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Título de la solicitud</label>
            <input type="text" 
                   class="form-control" 
                   id="title" 
                   name="title" 
                   maxlength="200" 
                   required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Tipo de solicitud</label>
            <select class="form-select" id="type" name="type" required>
                <option value="">Seleccione una opción</option>
                <option value="Petición">Petición</option>
                <option value="Incidente">Incidente</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" 
                      id="description" 
                      name="description" 
                      rows="4" 
                      required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Crear Ticket</button>
        <a href="/tickets" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
