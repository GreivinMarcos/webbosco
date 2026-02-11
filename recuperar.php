<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'conexion.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);

    $query = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $query->bind_param("s", $correo);
    $query->execute();
    $resultado = $query->get_result();

    if ($resultado->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $url_recuperacion = "http://localhost/Tienda%20e-commerce%20BoscoBox/restablecer.php?token=$token";

        $update = $conn->prepare("UPDATE usuarios SET token = ? WHERE correo = ?");
        $update->bind_param("ss", $token, $correo);
        $update->execute();

        // Configuración de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            
            // 🟢 Cambia estos dos datos por los tuyos
            $mail->Username = 'gmarcos.netcom@gmail.com'; // tu correo Gmail
            $mail->Password = 'aazf cdrq krhh lozj'; // tu contraseña o clave de aplicación
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('gmarcos.netcom@gmail.com', 'Bosco Box');
            $mail->addAddress($correo);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña - Bosco Box';
            $mail->Body = "
                <h3>Hola,</h3>
                <p>Has solicitado restablecer tu contraseña.</p>
                <p>Haz clic en el siguiente enlace para continuar:</p>
                <a href='$url_recuperacion'>$url_recuperacion</a>
                <br><br>
                <small>Si no solicitaste este cambio, ignora este mensaje.</small>
            ";

            $mail->send();
            $mensaje = "Se ha enviado un correo con instrucciones para restablecer tu contraseña.";
        } catch (Exception $e) {
            $mensaje = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        $mensaje = "No existe una cuenta con ese correo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar contraseña - Bosco Box</title>

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
  <h3 class="text-center mb-4">Recuperar contraseña</h3>

  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-info text-center"><?php echo $mensaje; ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label for="correo" class="form-label">Correo registrado</label>
      <input type="email" name="correo" id="correo" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
  </form>
</div>
</body>
</html>
