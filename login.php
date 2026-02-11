<?php
session_start();
include('conexion.php');

// Si el usuario ya está logueado, lo redirige directamente
if (isset($_SESSION['usuario'])) {
    if ($_SESSION['admin'] == 'si') {
        header("Location: admin/admin_dashboard.php");
    } else {
        header("Location: shop.php");
    }
    exit;
}

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    // Verificar si existe el usuario
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
            
            // Guardar sesión
            $_SESSION['usuario'] = $usuario['correo'];
            $_SESSION['nombre'] = $usuario['nombre_completo'];
            $_SESSION['admin'] = $usuario['admin'];
            
            // ⚠️ NUEVA LÍNEA: guardar también el id del usuario
            $_SESSION['id_usuario'] = $usuario['id']; 

            // Redirección según tipo de usuario
            if ($usuario['admin'] === 'si') {
                header("Location: admin/admin_dashboard.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "El usuario no existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BoscoBox</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/fondo_crossfit.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }

        .login-container img {
            width: 100px;
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #ff9900;
            border: none;
        }

        .btn-primary:hover {
            background-color: #cc7a00;
        }

        a {
            color: #ffcc00;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: rgba(0,0,0,0.8);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <img src="img/logo.png" alt="Logo BoscoBox">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($mensaje)) : ?>
        <div class="alert alert-danger mt-3"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="correo" id="correo" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="contrasena" id="contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>

    <div class="mt-3">
        <p><a href="registro.php">Registrarse</a> | <a href="recuperar.php">¿Olvidaste tu contraseña?</a></p>
        <p><a href="index.php">⬅ Volver a inicio</a></p>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> BoscoBox - Todos los derechos reservados.</p>
    <p>Contacto: info@boscobox.com | Tel: +506 8888-8888</p>
    <p>Ubicación: <a href="https://www.google.com/maps/search/BoscoBox+Costa+Rica" target="_blank" style="color:#ffcc00;">Ver en Google Maps</a></p>
</footer>

</body>
</html>
