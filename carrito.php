<?php
// carrito.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_usuario = (int) $_SESSION['usuario_id'];


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
        $id_carrito = (int) ($_POST['id_carrito'] ?? 0);
        $cantidad   = (int) ($_POST['cantidad'] ?? 0);

        if ($id_carrito > 0) {
            if ($cantidad <= 0) {

                $sql_del = "DELETE FROM carrito 
                            WHERE id_carrito = ? AND id_usuario = ?";
                $stmt_del = $conn->prepare($sql_del);
                $stmt_del->bind_param("ii", $id_carrito, $id_usuario);
                $stmt_del->execute();
                $stmt_del->close();
            } else {

                $sql_upd = "UPDATE carrito 
                            SET cantidad = ? 
                            WHERE id_carrito = ? AND id_usuario = ?";
                $stmt_upd = $conn->prepare($sql_upd);
                $stmt_upd->bind_param("iii", $cantidad, $id_carrito, $id_usuario);
                $stmt_upd->execute();
                $stmt_upd->close();
            }
        }
    }


    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $id_carrito = (int) ($_POST['id_carrito'] ?? 0);

        if ($id_carrito > 0) {
            $sql_del = "DELETE FROM carrito 
                        WHERE id_carrito = ? AND id_usuario = ?";
            $stmt_del = $conn->prepare($sql_del);
            $stmt_del->bind_param("ii", $id_carrito, $id_usuario);
            $stmt_del->execute();
            $stmt_del->close();
        }
    }
}


$sql = "SELECT c.id_carrito,
               c.cantidad,
               p.id_producto,
               p.nombre,
               p.precio,
               p.imagen
        FROM carrito c
        INNER JOIN producto p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$total_general = 0;

include 'includes/header.php';
?>

<h1 class="mb-4 carrito-titulo">Mi carrito</h1>

<?php if ($result && $result->num_rows > 0): ?>

  <div class="carrito-wrapper">
    <div class="card carrito-card">
      <div class="card-body">

        <div class="table-responsive">
          <table class="table align-middle carrito-tabla">
            <thead>
              <tr>
                <th>Producto</th>
                <th class="text-center">Imagen</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              <?php while ($row = $result->fetch_assoc()): 
                $subtotal = $row['precio'] * $row['cantidad'];
                $total_general += $subtotal;
              ?>
                <tr>
                  <td class="carrito-nombre">
                    <?php echo htmlspecialchars($row['nombre']); ?>
                  </td>

                  <td class="text-center carrito-imagen-col">
                    <?php if (!empty($row['imagen'])): ?>
                      <img src="img/<?php echo htmlspecialchars($row['imagen']); ?>"
                           alt="<?php echo htmlspecialchars($row['nombre']); ?>"
                           class="img-fluid carrito-imagen">
                    <?php else: ?>
                      <span class="text-muted">Sin imagen</span>
                    <?php endif; ?>
                  </td>

                  <td>
                    $<?php echo number_format($row['precio'], 2); ?>
                  </td>

                  <td>
                    <!-- Formulario para actualizar cantidad -->
                    <form method="post" action="carrito.php" class="d-flex carrito-form-cantidad">
                      <input type="hidden" name="accion" value="actualizar">
                      <input type="hidden" name="id_carrito" value="<?php echo (int)$row['id_carrito']; ?>">

                      <input type="number" name="cantidad"
                             class="form-control form-control-sm me-2 carrito-input-cantidad"
                             value="<?php echo (int)$row['cantidad']; ?>"
                             min="0">
                      <button type="submit" class="btn btn-sm btn-primary">
                        Actualizar
                      </button>
                    </form>
                  </td>

                  <td>
                    $<?php echo number_format($subtotal, 2); ?>
                  </td>

                  <td>
                    <!-- Formulario para eliminar -->
                    <form method="post" action="carrito.php" class="carrito-form-eliminar">
                      <input type="hidden" name="accion" value="eliminar">
                      <input type="hidden" name="id_carrito" value="<?php echo (int)$row['id_carrito']; ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        Eliminar
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>

            </tbody>
          </table>
        </div>

        <div class="carrito-total-box mt-3">
          <div class="carrito-total-texto">
            <span>Total:</span>
            <strong>$<?php echo number_format($total_general, 2); ?> MXN</strong>
          </div>
          <a href="finalizar_compra.php" class="btn btn-primary carrito-boton-finalizar">
            Finalizar compra
          </a>
        </div>

      </div>
    </div>
  </div>

<?php else: ?>

  <div class="carrito-vacio text-center my-5">
    <h4 class="mb-3">Tu carrito está vacío</h4>
    <p class="mb-4">Explora nuestro catálogo y descubre joyas y bolsas para ti.</p>
    <a href="catalogo.php" class="btn btn-primary">
      Ir al catálogo
    </a>
  </div>

<?php endif; ?>

<?php
$stmt->close();
include 'includes/footer.php';
?>