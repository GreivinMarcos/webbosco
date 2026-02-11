<?php
require_once 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $cedula = trim($_POST['cedula']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = trim($_POST['contrasena']);
    $confirmar = trim($_POST['confirmar']);

    if (strlen($contrasena) < 8) {
        $mensaje = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($contrasena !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? OR cedula = ?");
        $check->bind_param("ss", $correo, $cedula);
        $check->execute();
        $resultado = $check->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "El usuario ya se encuentra registrado.";
        } else {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO usuarios (nombre_completo, cedula, correo, telefono, contrasena, admin) VALUES (?, ?, ?, ?, ?, 'no')");
            $insert->bind_param("sssss", $nombre, $cedula, $correo, $telefono, $hash);

            if ($insert->execute()) {
                $mensaje = "Registro exitoso. ¡Bienvenido a Bosco Box!";
            } else {
                $mensaje = "Error al registrar usuario: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro - Bosco Box</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('img/crossfit-bg.jpg') center/cover fixed no-repeat;
      position: relative;
      min-height: 100vh;
    }

    body::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: url('img/watermark-crossfit.png') center/contain no-repeat;
      opacity: 0.07;
      z-index: 0;
    }

    .form-container {
      position: relative;
      z-index: 1;
      background: rgba(0, 0, 0, 0.85);
      padding: 2rem;
      border-radius: 1rem;
      color: white;
      box-shadow: 0 0 15px rgba(0,0,0,0.6);
      max-width: 500px;
      margin: auto;
      margin-top: 120px;
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.9);
    }

    .btn-primary {
      background-color: #ff4b2b;
      border: none;
    }
    .btn-primary:hover {
      background-color: #e63b1e;
    }

    footer {
      background: #000;
      color: #bbb;
      padding: 2rem 0;
      text-align: center;
      margin-top: 80px;
    }

    .logo { height: 45px; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo.png" alt="Bosco Box Logo" class="logo me-2">
      <span class="fw-bold text-uppercase">Bosco Box</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto text-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link" href="entrenamientos.php">Entrenamientos</a></li>
        <li class="nav-item"><a class="nav-link active" href="registro.php">Registrarse</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="form-container">
  <h2 class="text-center mb-4">Registro Bosco Box</h2>

  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-info text-center"><?php echo $mensaje; ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre completo</label>
      <input type="text" name="nombre" id="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="cedula" class="form-label">Cédula</label>
      <input type="text" name="cedula" id="cedula" class="form-control" required>
      <small id="cedula-msg" class="text-warning"></small>
    </div>

    <div class="mb-3">
      <label for="correo" class="form-label">Correo electrónico</label>
      <input type="email" name="correo" id="correo" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="telefono" class="form-label">Teléfono</label>
      <input type="text" name="telefono" id="telefono" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="contrasena" class="form-label">Contraseña</label>
      <input type="password" name="contrasena" id="contrasena" class="form-control" required minlength="8">
    </div>

    <div class="mb-3">
      <label for="confirmar" class="form-label">Confirmar contraseña</label>
      <input type="password" name="confirmar" id="confirmar" class="form-control" required minlength="8">
    </div>

    <button type="submit" class="btn btn-primary w-100 mt-3" id="btn-registrar">Registrarse</button>
  </form>

  <div class="text-center mt-3">
    <a href="recuperar.php" class="text-light">¿Olvidaste tu contraseña?</a>
  </div>

  <div class="text-center mt-3">
    <a href="index.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver al inicio</a>
  </div>
</div>

<footer>
  <div class="container">
    <img src="img/logo.png" alt="Bosco Box Logo" height="60">
    <p class="mb-1 small">© <?php echo date('Y'); ?> Bosco Box. Todos los derechos reservados.</p>
    <p class="mb-1 small">Correo: <a href="mailto:info@boscobox.com" class="text-decoration-none text-light">info@boscobox.com</a></p>
    <p class="mb-0 small">Ubicación: San José, Costa Rica</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('cedula').addEventListener('blur', function() {
    const cedula = this.value.trim();
    const msg = document.getElementById('cedula-msg');
    const btn = document.getElementById('btn-registrar');

    if (cedula.length > 0) {
        fetch('validar_cedula.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'cedula=' + encodeURIComponent(cedula)
        })
        .then(res => res.json())
        .then(data => {
            if (data.existe) {
                msg.textContent = "Esta cédula ya se encuentra registrada.";
                btn.disabled = true;
            } else {
                msg.textContent = "";
                btn.disabled = false;
            }
        });
    }
});
</script>
</body>
</html>
