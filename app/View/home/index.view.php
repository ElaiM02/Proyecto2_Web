<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Bienvenido a HelpDesk</h1>
        <p class="col-md-8 fs-4">Esta es una aplicación de resolucion de tickets</p>
        <?php if (isset($_SESSION['user'])): ?>
            <a class="btn btn-primary btn-lg" href="/tickets" role="button">Ver Tickets</a>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>