<?php
require 'conexion.php';
include 'includes/header.php';
?>

<!-- HERO BANNER -->
<div class="hero-banner mb-5">
  <div class="hero-overlay">
    <h1 class="hero-title">Nueva colección 2025</h1>
    <p class="hero-subtitle">
      Bolsas y joyería diseñadas para inspirar cada día.
    </p>
    <a href="catalogo.php" class="btn btn-primary hero-btn">Comprar ahora</a>
  </div>
</div>

<!-- HERO SECUNDARIO (con imagen y contenido alineado a la derecha) -->
<div class="hero-banner-sec mb-5">
  <div class="hero-overlay-sec d-flex flex-column align-items-end text-end">

    <h1 class="hero-title">PEONIA STORE</h1>

    <?php if (isset($_SESSION['usuario_id'])): ?>
      <p class="hero-subtitle">
        Bienvenida, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>.<br>
        Explora el catálogo y revisa tu carrito o historial de compras.
      </p>
      <a href="catalogo.php" class="btn btn-primary hero-btn">Ver catálogo</a>

    <?php else: ?>
      <p class="hero-subtitle">
        Crea tu cuenta para guardar tus compras y descubrir nuestra colección exclusiva.
      </p>
    <div class="d-flex justify-content-start align-items-center gap-3 mt-3 flex-wrap">
      <a href="registro.php" class="btn btn-primary btn-lg px-4">Crear cuenta</a>
      <a href="login.php" class="btn btn-outline-secondary btn-lg px-4">Iniciar sesión</a>
    </div>

    <?php endif; ?>

  </div>
</div>


<?php include 'includes/footer.php'; 
?>