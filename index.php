<?php
// Página principal Bosco Box - efecto 3D solo en entrenadores
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bosco Box - CrossFit</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- CSS personalizado -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container">
    <!-- LOGO Y NOMBRE -->
    <a class="navbar-brand d-flex align-items-center" href="#">
<div class="d-flex align-items-center">
    <img src="img/logo.png" alt="Bosco Box Logo" class="logo me-2">
</div>

      <span class="fw-bold text-uppercase">Bosco Box</span>
    </a>

    <!-- BOTÓN HAMBURGUESA -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- MENÚ -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto text-center">
        <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="shop.php">FitBar</a></li>
        <li class="nav-item"><a class="nav-link" href="entrenamientos.php">Entrenamientos</a></li>
        <li class="nav-item"><a class="nav-link" href="registro.php">Registrarse</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<header class="hero d-flex align-items-center justify-content-center text-center text-light">
  <div class="container">
    <!-- <img src="img/logo.png" alt="Bosco Box Logo" class="hero-logo mb-3"> -->
    <h1 class="fw-bold">Bienvenido a Bosco Box</h1>

    <!-- CONTENEDOR DE CUADROS -->
    <div class="row g-4 justify-content-center">

      <!-- CUADRO 1: Bosco Box Fitness Center -->
      <div class="col-md-10 col-lg-8">
        <div class="p-4 rounded" style="background: rgba(0,0,0,0.6); border-left: 5px solid #0dcaf0;">
          <p class="lead">
            Bosco Box Fitness Center es un centro de acondicionamiento físico en el cual utilizan una metodología 
            de entrenamiento para que las personas alcancen su mejor versión, además puedan cumplir con sus objetivos (halterofilia, Crossfit/funcional, fitness) 
            o de salud (entrenamientos personalizados a adultos mayores). Esta empresa fue fundada el 15 de mayo del 2017 por José Miguel Herrera Martínez 
            con una idea innovadora que incentiva a las personas a realizar ejercicio para mejorar su estilo de vida y salud.
          </p>
        </div>
      </div>

      <!-- CUADRO 2: Misión -->
      <div class="col-md-5">
        <div class="p-4 rounded h-100" style="background: rgba(0,0,0,0.6); border-left: 5px solid #0dcaf0;">
          <p class="lead mb-0">
            <strong>Misión:</strong> Ofrecer entrenamiento funcional con un enfoque profesional, personalizado y motivacional. 
            Nos destacamos por brindar un ambiente familiar y lleno de apoyo.
          </p>
        </div>
      </div>

      <!-- CUADRO 3: Visión -->
      <div class="col-md-5">
        <div class="p-4 rounded h-100" style="background: rgba(0,0,0,0.6); border-left: 5px solid #0dcaf0;">
          <p class="lead mb-0">
            <strong>Visión:</strong> Ser un lugar de transformación física y emocional, donde cualquier persona, sin importar su nivel físico, 
            encuentre motivación, comunidad y herramientas para superarse día a día.
          </p>
        </div>
      </div>

      <!-- CUADRO 4: FitBar -->
      <div class="col-md-10 col-lg-8">
        <div class="p-4 rounded" style="background: rgba(0,0,0,0.6); border-left: 5px solid #0dcaf0;">
          <p class="lead mb-0">
            <strong>FitBar:</strong> Ofrecemos accesorios deportivos, batidos proteicos, bebidas energéticas, hidratación y snacks con el fin de apoyar su rendimiento deportivo.
          </p>
        </div>
      </div>

    </div>

    <a href="contacto.php" class="btn btn-primary btn-lg px-4 mt-4" class="nav-link" href="contacto.php">Comienza Hoy</a>
  </div>
</header>


<!-- ENTRENADORES -->
<section id="entrenadores" class="py-5 bg-light">
  <div class="container text-center">
    <h2 class="fw-bold mb-5">Nuestros Entrenadores</h2>
    <div class="row g-4">

      <?php
      $entrenadores = [
        ["nombre" => "José Herrera Martinez", "desc" => "Especialista en levantamiento olímpico y fuerza funcional.", "img" => "entrenador1.jpg"],
        ["nombre" => "Andres Herrera Martinez", "desc" => "Coach de movilidad y acondicionamiento físico integral.", "img" => "entrenador2.jpg"],
        ["nombre" => "Monica Solano Roldan", "desc" => "Entrenador CrossFit nivel 2, experto en técnica y motivación.", "img" => "entrenador3.jpeg"],
        ["nombre" => "Dinorah Batista Varela", "desc" => "Especialista en nutrición deportiva y entrenamiento funcional.", "img" => "entrenador4.jpeg"],
        ["nombre" => "Joselyn Ramirez Sandi", "desc" => "Coach de resistencia, metcon y mentalidad de alto rendimiento.", "img" => "entrenador5.jpeg"]
      ];

      foreach ($entrenadores as $entrenador) {
        echo '
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mx-auto">
          <div class="card trainer-card h-100 border-0 shadow-sm">
            <img src="img/'.$entrenador["img"].'" class="card-img-top tilt-entrenador" alt="'.$entrenador["nombre"].'" 
                 data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2">
            <div class="card-body">
              <h5 class="card-title">'.$entrenador["nombre"].'</h5>
              <p class="card-text">'.$entrenador["desc"].'</p>
            </div>
          </div>
        </div>';
      }
      ?>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer bg-dark text-light py-5">
  <div class="container text-center">
    <img src="img/logo.png" alt="Bosco Box Logo" class="footer-logo mb-3">
    <p class="mb-1 small">© <?php echo date('Y'); ?> Bosco Box. Todos los derechos reservados.</p>
    <p class="mb-1 small">Contacto: <a href="mailto:info@boscobox.com" class="text-decoration-none text-light">info@boscobox.com</a></p>
    <p class="mb-0 small">Ubicación: San José, Costa Rica</p>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.0/vanilla-tilt.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
