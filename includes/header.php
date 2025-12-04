<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>PEONIA STORE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <link rel="stylesheet" href="css/estilos.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">

    <a class="navbar-brand" href="index.php">PEONIA STORE</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <!-- Siempre visibles -->
        <li class="nav-item">
          <a class="nav-link" href="catalogo.php">Catálogo</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="contacto.php">Contacto</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="admin.php">Admin</a>
        </li>

        <?php if (isset($_SESSION['usuario_id'])): ?>

          <li class="nav-item">
            <a class="nav-link" href="usuario.php">Mi cuenta</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="carrito.php">Carrito</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="historial.php">Historial</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar sesión</a>
          </li>

        <?php else: ?>

          <!-- Visible solo si NO hay sesión -->
          <li class="nav-item">
            <a class="nav-link" href="registro.php">Crear cuenta</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="login.php">Iniciar sesión</a>
          </li>

        <?php endif; ?>

      </ul>
    </div>

  </div>
</nav>

<main class="container my-4">