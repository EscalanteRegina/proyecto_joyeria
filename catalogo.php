<?php
require 'conexion.php';
include 'includes/header.php';


$sql = "SELECT id_producto, nombre, descripcion, precio, stock, imagen
        FROM producto
        WHERE activo = 1
        ORDER BY nombre";

$result = $conn->query($sql);
?>

<h1 class="mb-4">Catálogo de productos</h1>

<?php if ($result && $result->num_rows > 0): ?>

  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col">
        <div class="card h-100">

          <?php if (!empty($row['imagen'])): ?>

            <img src="img/<?php echo htmlspecialchars($row['imagen']); ?>"
                 class="card-img-top img-fluid"
                 alt="<?php echo htmlspecialchars($row['nombre']); ?>">
          <?php else: ?>

            <div class="card-img-top d-flex align-items-center justify-content-center"
                 style="height: 200px; background-color: #f8f9fa;">
              <span class="text-muted">Sin imagen</span>
            </div>
          <?php endif; ?>

          <div class="card-body d-flex flex-column">
            <h5 class="card-title">
              <?php echo htmlspecialchars($row['nombre']); ?>
            </h5>

            <?php if (!empty($row['descripcion'])): ?>
              <p class="card-text small text-muted mb-2">
                <?php echo nl2br(htmlspecialchars($row['descripcion'])); ?>
              </p>
            <?php endif; ?>

            <p class="fw-bold mb-1">
              $<?php echo number_format($row['precio'], 2); ?> MXN
            </p>

            <p class="text-muted mb-3">
              En stock: <?php echo (int)$row['stock']; ?>
            </p>

            <form method="post" action="agregar_carrito.php" class="mt-auto">
              <input type="hidden" name="id_producto"
                     value="<?php echo (int)$row['id_producto']; ?>">
              <button type="submit" class="btn btn-primary w-100">
                Agregar al carrito
              </button>
            </form>

          </div>
        </div>
      </div>
    <?php endwhile; ?>

  </div>

<?php else: ?>

  <div class="alert alert-info">
    Por el momento no hay productos disponibles.
  </div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
