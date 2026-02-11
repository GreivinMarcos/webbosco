<?php
// Página de contacto Bosco Box
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bosco Box - Contacto</title>

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
        <li class="nav-item"><a class="nav-link" href="entrenamientos.php">Entrenamientos</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Registrarse</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Login</a></li>
        <li class="nav-item"><a class="nav-link active" href="contacto.php">Contacto</a></li>
      </ul>

      <a href="#" class="text-light d-flex align-items-center carrito-icon">
        <i class="bi bi-cart-fill fs-4"></i>
        <span class="badge bg-danger ms-1" id="cart-count">0</span>
      </a>
    </div>
  </div>
</nav>

<!-- SECCIÓN CONTACTO -->
<section class="py-5 mt-5">
  <div class="container text-center">
    <h1 class="fw-bold mb-5">Contáctanos</h1>

    <div class="row justify-content-center g-4">

      <!-- Redes Sociales -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body">
            <h5 class="card-title fw-bold mb-3">Síguenos en Redes Sociales</h5>
            <p>
              <a href="https://www.facebook.com/p/Bosco-Box-Fitness-Center-100023131157853/" target="_blank" class="text-decoration-none me-3">
                <i class="bi bi-facebook fs-2 text-primary"></i>
              </a>
              <a href="https://www.instagram.com/bosco_box/" target="_blank" class="text-decoration-none">
                <i class="bi bi-instagram fs-2 text-danger"></i>
              </a>
            </p>
          </div>
        </div>
      </div>

      <!-- Teléfonos -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body">
            <h5 class="card-title fw-bold mb-3">Teléfonos</h5>
            <p class="mb-1">+506 8800 2603</p>
            <p class="mb-0">+506 8820 8000</p>
          </div>
        </div>
      </div>

      <!-- Ubicación y WhatsApp -->
      <div class="col-12 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body">
            <h5 class="card-title fw-bold mb-3">Ubicación y WhatsApp</h5>
            <p class="mb-2">San José, Costa Rica</p>
            
            <!-- Google Maps incrustado -->
            <div class="ratio ratio-16x9 mb-3">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.783171376833!2d-84.08633512493546!3d9.951990373858893!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8fa0e551c5ab2b0d%3A0x79adce8bc2bf85e!2sBosco%20Box%20Fitness%20Center!5e0!3m2!1ses!2scr!4v1760837352413!5m2!1ses!2scr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <a href="https://wa.me/50688002603" target="_blank" class="btn btn-success">
              <i class="bi bi-whatsapp me-2"></i> Enviar mensaje
            </a>
          </div>
        </div>
      </div>

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
