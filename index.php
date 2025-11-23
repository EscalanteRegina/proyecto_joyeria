<?php
require 'conexion.php';
include 'includes/header.php';
?>

<div class="p-5 mb-4 bg-light rounded-3">
  <div class="container-fluid py-5">
    <h1 class="display-5 fw-bold">Joyas & Bolsas</h1>

    <?php if (isset($_SESSION['usuario_id'])): ?>
      <p class="col-md-8 fs-4">
        Bienvenida, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>.  
        Explora el catálogo y revisa tu carrito o historial de compras.
      </p>
      <a href="catalogo.php" class="btn btn-primary btn-lg">Ver catálogo</a>
    <?php else: ?>
      <p class="col-md-8 fs-4">
        Crea tu cuenta para guardar tus compras, ver tu historial y hacer pedidos
        de nuestras joyas y bolsas favoritas.
      </p>
      <a href="registro.php" class="btn btn-primary btn-lg me-2">Crear cuenta</a>
      <a href="login.php" class="btn btn-outline-secondary btn-lg">Iniciar sesión</a>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

