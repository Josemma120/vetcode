<?php
// Configuración para admitir variables de entorno (Hosting) o valores locales por defecto
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "gestion_veterinaria";
$port = getenv('DB_PORT') ?: 3306;

// Conexión usando el puerto también (importante para algunos hostings)
$con = new mysqli($host, $user, $password, $dbname, $port);

if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

