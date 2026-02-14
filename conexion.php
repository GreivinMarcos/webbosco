<?php
$servername = "boscoboxfitnesscenter-server.mysql.database.azure.com";
$username   = "uqvqtymxlq";
$password   = 'eVrIw$XRhNvs0Vy4';
$database   = "boscobox_fitbar";
$port       = 3306;

// Inicializar conexión
$conn = mysqli_init();

// Configurar SSL
mysqli_ssl_set(
    $conn,
    NULL,
    NULL,
    __DIR__ . "/certs/MysqlflexGlobalRootCA.crt.pem",
    NULL,
    NULL
);

// Realizar conexión
if (!mysqli_real_connect(
    $conn,
    $servername,
    $username,
    $password,
    $database,
    $port,
    NULL,
    MYSQLI_CLIENT_SSL
)) {
    die("Error de conexión: " . mysqli_connect_error());
}

// No imprimir absolutamente nada aquí
?>
