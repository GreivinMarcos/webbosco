<?php
// Página de entrenamientos Bosco Box
$entrenamientos = [
    ["titulo" => "CrossFit", "desc" => "Entrenamiento funcional de alta intensidad que combina levantamiento de pesas, gimnasia y cardio.", "img" => "entrenamiento_crossfit.jpg"],
    ["titulo" => "Weightlifting", "desc" => "Levantamiento olímpico enfocado en fuerza, técnica y potencia máxima.", "img" => "entrenamiento_weightlifting.jpg"],
    ["titulo" => "MetCon", "desc" => "Entrenamiento metabólico para mejorar resistencia cardiovascular y muscular en sesiones cortas e intensas.", "img" => "entrenamiento_metcon.jpg"],
    ["titulo" => "Movilidad y Flexibilidad", "desc" => "Ejercicios enfocados en rango de movimiento, prevención de lesiones y recuperación activa.", "img" => "entrenamiento_movilidad.jpg"],
    ["titulo" => "Resistencia y Cardio", "desc" => "Entrenamientos para mejorar capacidad aeróbica, resistencia muscular y salud cardiovascular.", "img" => "entrenamiento_resistencia.jpg"],
    ["titulo" => "Nutrición y Coaching Personalizado", "desc" => "Asesoramiento personalizado en nutrición deportiva y planes de entrenamiento individualizados.", "img" => "entrenamiento_nutricion.jpg"]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bosco Box - Entrenamientos</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- NAVBAR -->
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
        <li class="nav-item"><a class="nav-link" href="#">Shop</a></li>
        <li class="nav-item"><a class="nav-link active" href="entrenamientos.php">Entrenamientos</a></li>
        <li class="nav-item"><a class="nav-link" href="registro.php">Registrarse</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ENTRENAMIENTOS -->
<section class="py-5 mt-5">
  <div class="container">
    <h1 class="text-center fw-bold mb-5">Tipos de Entrenamientos en Bosco Box</h1>

    <div class="row g-4">
      <?php foreach ($entrenamientos as $entreno): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0 entrenamiento-card">
          <img src="img/<?php echo $entreno['img']; ?>" class="card-img-top" alt="<?php echo $entreno['titulo']; ?>">
          <div class="card-body">
            <h5 class="card-title fw-bold"><?php echo $entreno['titulo']; ?></h5>
            <p class="card-text"><?php echo $entreno['desc']; ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer bg-dark text-light py-5 mt-5">
  <div class="container text-center">
    <img src="img/logo.png" alt="Bosco Box Logo" class="footer-logo mb-3">
    <p class="mb-1 small">© <?php echo date('Y'); ?> Bosco Box. Todos los derechos reservados.</p>
    <p class="mb-1 small">Contacto: <a href="mailto:info@boscobox.com" class="text-decoration-none text-light">info@boscobox.com</a></p>
    <p class="mb-0 small">Ubicación: San José, Costa Rica</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
