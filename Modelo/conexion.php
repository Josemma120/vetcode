<?php
// Configuración para admitir variables de entorno (Hosting) o valores locales por defecto
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "gestion_veterinaria";
$port = getenv('DB_PORT') ?: 3306;

// Habilitar reporte de errores de MySQLi para que lance excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Conexión usando el puerto también (importante para algunos hostings)
    $con = new mysqli($host, $user, $password, $dbname, (int) $port);
    $con->set_charset("utf8mb4"); // Asegurar codificación correcta
} catch (mysqli_sql_exception $e) {
    // Si estás en producción, no muestres el error real al usuario, pero sí regístralo
    error_log("Error de conexión a la BD: " . $e->getMessage());
    die("<h1>Error de conexión a la base de datos</h1><p>Verifica los datos de conexión en el archivo .env o las variables de entorno.</p><p>Detalle técnico (solo para el desarrollador): " . $e->getMessage() . "</p>");
}

