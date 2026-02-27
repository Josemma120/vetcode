<?php
// Configuración para admitir variables de entorno (Hosting) o valores locales por defecto
$host = getenv('MYSQL_ADDON_HOST') ?: (getenv('DB_HOST') ?: "localhost");
$user = getenv('MYSQL_ADDON_USER') ?: (getenv('DB_USER') ?: "root");
$password = getenv('MYSQL_ADDON_PASSWORD') ?: (getenv('DB_PASSWORD') ?: "");
$dbname = getenv('MYSQL_ADDON_DB') ?: (getenv('DB_NAME') ?: "gestion_veterinaria");
$port = getenv('MYSQL_ADDON_PORT') ?: (getenv('DB_PORT') ?: 3306);

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

