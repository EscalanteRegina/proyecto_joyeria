<?php
require 'conexion.php';

$errores = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre    = trim($_POST['nombre'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nombre === "" || $email === "" || $password === "" || $password2 === "") {
        $errores = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores = "El correo no tiene un formato válido.";
    } elseif ($password !== $password2) {
        $errores = "Las contraseñas no coinciden.";
    } else {
        // ¿ya existe ese correo?
        $sql  = "SELECT id_usuario FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores = "Ya existe una cuenta con ese correo.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql_insert  = "INSERT INTO usuario (nombre, email, password) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $nombre, $email, $hash);

            if ($stmt_insert->execute()) {
                $exito = "Cuenta creada correctamente. Ya puedes iniciar sesión.";
            } else {
                $errores = "Error al crear la cuenta.";
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }
}

include 'includes/header.php';
?>

<h1 class="mb-4">Crear cuenta</h1>

<?php if ($errores != ""): ?>
  <div class="alert alert-danger"><?php echo $errores; ?></div>
<?php endif; ?>

<?php if ($exito != ""): ?>
  <div class="alert alert-success"><?php echo $exito; ?></div>
<?php endif; ?>

<form method="post" action="registro.php" class="card p-4">
  <div class="mb-3">
    <label for="nombre" class="form-label">Nombre completo</label>
    <input type="text" name="nombre" id="nombre" class="form-control"
           value="<?php echo htmlspecialchars($nombre ?? ''); ?>">
  </div>

  <div class="mb-3">
    <label for="email" class="form-label">Correo electrónico</label>
    <input type="email" name="email" id="email" class="form-control"
           value="<?php echo htmlspecialchars($email ?? ''); ?>">
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Contraseña</label>
    <input type="password" name="password" id="password" class="form-control">
  </div>

  <div class="mb-3">
    <label for="password2" class="form-label">Repetir contraseña</label>
    <input type="password" name="password2" id="password2" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Crear cuenta</button>
  <a href="login.php" class="btn btn-link">Ya tengo cuenta</a>
</form>

<?php include 'includes/footer.php'; ?>