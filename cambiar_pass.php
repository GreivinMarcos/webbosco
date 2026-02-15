<?php
include('conexion.php');
$id = $_GET['id'] ?? null;
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
    $stmt->bind_param("si", $nueva, $id);
    $stmt->execute();
    $mensaje = "Contraseña actualizada correctamente.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cambiar Contraseña | BoscoBox</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header class="header">
 <img src="img/logo.png" alt="Bosco Box Logo" class="hero-logo mb-3">
    <h1>Cambiar Contraseña</h1>
</header>
<main class="main-content">
<section class="content-box">
    <form method="POST" class="form">
        <input type="password" name="contrasena" placeholder="Nueva contraseña (mínimo 8 caracteres)" minlength="8" required>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
    <p style="color:green"><?= $mensaje ?></p>
</section>
</main>
<footer class="footer">
    <p>© <?php echo date('Y'); ?> BoscoBox | Contacto: info@boscobox.com</p>
</footer>
<div class="background-watermark"></div>
</body>
</html>
