<?php
// historial.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_usuario = (int) $_SESSION['usuario_id'];

$sql = "SELECT h.id_compra,
               h.fecha_compra,
               h.cantidad,
               p.nombre,
               p.precio,
               p.imagen
        FROM historial h
        INNER JOIN producto p ON h.id_producto = p.id_producto
        WHERE h.id_usuario = ?
        ORDER BY h.fecha_compra DESC, h.id_compra DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$total_general = 0;

include 'includes/header.php';
?>

<h1 class="mb-4 historial-titulo">Historial de compras</h1>

<?php if ($result && $result->num_rows > 0): ?>

  <div class="historial-wrapper">
    <div class="card historial-card">
      <div class="card-body">

        <div class="table-responsive">
          <table class="table align-middle historial-tabla">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th class="text-center">Imagen</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()):
                $subtotal = $row['precio'] * $row['cantidad'];
                $total_general += $subtotal;
              ?>
                <tr>
                  <td class="historial-fecha">
                    <?php echo htmlspecialchars($row['fecha_compra']); ?>
                  </td>

                  <td class="historial-nombre">
                    <?php echo htmlspecialchars($row['nombre']); ?>
                  </td>

                  <td class="text-center historial-imagen-col">
                    <?php if (!empty($row['imagen'])): ?>
                      <img src="img/<?php echo htmlspecialchars($row['imagen']); ?>"
                           alt="<?php echo htmlspecialchars($row['nombre']); ?>"
                           class="img-fluid historial-imagen">
                    <?php else: ?>
                      <span class="text-muted">Sin imagen</span>
                    <?php endif; ?>
                  </td>

                  <td>
                    $<?php echo number_format($row['precio'], 2); ?>
                  </td>

                  <td>
                    <?php echo (int)$row['cantidad']; ?>
                  </td>

                  <td>
                    $<?php echo number_format($subtotal, 2); ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <div class="historial-total-box mt-3">
          <div class="historial-total-texto">
            <span>Total acumulado de compras:</span>
            <strong>$<?php echo number_format($total_general, 2); ?> MXN</strong>
          </div>

          <a href="catalogo.php" class="btn btn-primary historial-boton-catalogo">
            Seguir comprando
          </a>
        </div>

      </div>
    </div>
  </div>

<?php else: ?>

  <div class="historial-vacio text-center my-5">
    <h4 class="mb-3">Aún no has realizado compras</h4>
    <p class="mb-4">Cuando completes una compra, aparecerá aquí tu historial.</p>
    <a href="catalogo.php" class="btn btn-primary">
      Ir al catálogo
    </a>
  </div>

<?php endif; ?>

<?php
$stmt->close();
include 'includes/footer.php';
?>