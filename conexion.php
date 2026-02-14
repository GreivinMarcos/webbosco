<?php
$servername = "boscoboxfitnesscenter-server.mysql.database.azure.com";
$username   = "uqvqtymxlq"; // usuario con sufijo @servidor
$password   = 'eVrIw$XRhNvs0Vy4'; // tu contraseña real
$database   = "boscobox_fitbar";
$port       = 3306;

// Conexión con SSL requerido
$conn = mysqli_init();
mysqli_ssl_set(
    $conn,
    NULL,
    NULL,
    __DIR__ . "/certs/MysqlflexGlobalRootCA.crt.pem", // ruta relativa al archivo actual
    NULL,
    NULL
);

mysqli_real_connect($conn, $servername, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL);

if (mysqli_connect_errno()) {
    die("Error de conexión: " . mysqli_connect_error());
}

?>