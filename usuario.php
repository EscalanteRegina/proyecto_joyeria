<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT nombre, email, fecha_nacimiento, tarjeta, direccion, fecha_registro 
        FROM usuario 
        WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
} else {
    $usuario = null;
}

$stmt->close();

include 'includes/header.php';
?>

<h1 class="mb-4">Mi cuenta</h1>

<?php if ($usuario): ?>
  <div class="card mb-4">
    <div class="card-header">
      Información del usuario
    </div>
    <div class="card-body">
      <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
      <p><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
      <p><strong>Fecha de nacimiento:</strong> 
        <?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?>
      </p>
      <p><strong>Número de tarjeta:</strong> 
        <?php echo htmlspecialchars($usuario['tarjeta']); ?>
      </p>
      <p><strong>Dirección:</strong><br>
        <?php echo nl2br(htmlspecialchars($usuario['direccion'])); ?>
      </p>
      <p><strong>Fecha de registro:</strong> 
        <?php echo htmlspecialchars($usuario['fecha_registro']); ?>
      </p>
    </div>
  </div>

  <a href="index.php" class="btn btn-secondary">Volver al inicio</a>
  <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>

<?php else: ?>
  <div class="alert alert-danger">
    No se pudo cargar la información del usuario.
  </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>