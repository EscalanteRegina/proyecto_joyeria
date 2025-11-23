<?php
require 'conexion.php';

$errores = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === "" || $password === "") {
        $errores = "Todos los campos son obligatorios.";
    } else {
        $sql  = "SELECT id_usuario, nombre, password FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id_usuario, $nombre, $hash);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['usuario_id']     = $id_usuario;
                $_SESSION['usuario_nombre'] = $nombre;

                header("Location: index.php");
                exit();
            } else {
                $errores = "Correo o contraseña incorrectos.";
            }
        } else {
            $errores = "Correo o contraseña incorrectos.";
        }

        $stmt->close();
    }
}

include 'includes/header.php';
?>

<h1 class="mb-4">Iniciar sesión</h1>

<?php if ($errores != ""): ?>
  <div class="alert alert-danger"><?php echo $errores; ?></div>
<?php endif; ?>

<form method="post" action="login.php" class="card p-4">
  <div class="mb-3">
    <label for="email" class="form-label">Correo electrónico</label>
    <input type="email" name="email" id="email" class="form-control"
           value="<?php echo htmlspecialchars($email ?? ''); ?>">
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Contraseña</label>
    <input type="password" name="password" id="password" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Entrar</button>
  <a href="registro.php" class="btn btn-link">Crear cuenta nueva</a>
</form>

<?php include 'includes/footer.php'; ?>