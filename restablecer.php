<?php
require_once 'conexion.php';
$mensaje = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    die("Token no válido.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contrasena = trim($_POST['contrasena']);
    $confirmar = trim($_POST['confirmar']);

    if ($contrasena !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } elseif (strlen($contrasena) < 8) {
        $mensaje = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        $query = $conn->prepare("SELECT * FROM usuarios WHERE token = ?");
        $query->bind_param("s", $token);
        $query->execute();
        $resultado = $query->get_result();

        if ($resultado->num_rows > 0) {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE usuarios SET contrasena = ?, token = NULL WHERE token = ?");
            $update->bind_param("ss", $hash, $token);
            $update->execute();
            $mensaje = "Tu contraseña ha sido restablecida correctamente.";
        } else {
            $mensaje = "Token inválido o expirado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Restablecer contraseña - Bosco Box</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('img/crossfit-bg.jpg') center/cover fixed no-repeat;
      min-height: 100vh;
    }
    .form-container {
      background: rgba(0, 0, 0, 0.85);
      padding: 2rem;
      border-radius: 1rem;
      color: white;
      max-width: 500px;
      margin: auto;
      margin-top: 120px;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h3 class="text-center mb-4">Restablecer contraseña</h3>

  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-info text-center"><?php echo $mensaje; ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label for="contrasena" class="form-label">Nueva contraseña</label>
      <input type="password" name="contrasena" id="contrasena" class="form-control" required minlength="8">
    </div>

    <div class="mb-3">
      <label for="confirmar" class="form-label">Confirmar contraseña</label>
      <input type="password" name="confirmar" id="confirmar" class="form-control" required minlength="8">
    </div>

    <button type="submit" class="btn btn-primary w-100">Guardar nueva contraseña</button>
  </form>
</div>
</body>
</html>
