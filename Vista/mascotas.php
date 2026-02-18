<?php
// 1. Inclusión de seguridad y verificación de acceso (CORRECCIÓN DE RUTA DE CONEXIÓN)
include __DIR__ . "/../Controlador/seguridad.php";
check_access(['administrador', 'veterinario', 'recepcionista']); 
// Se cambia la ruta relativa a absoluta para evitar problemas:
include __DIR__ . "/../Modelo/conexion.php"; 

$busqueda = isset($_GET['buscar']) ? $con->real_escape_string($_GET['buscar']) : "";

// Consulta SQL con JOIN a la tabla clientes
$sql = "SELECT m.*, c.nombre AS nombre_cliente, c.apellido_paterno, c.apellido_materno 
        FROM mascotas m
        INNER JOIN clientes c ON m.id_cliente = c.id";

if ($busqueda !== '') {
    $sql .= " WHERE m.nombre LIKE '%$busqueda%' 
              OR m.tipo LIKE '%$busqueda%' 
              OR m.raza LIKE '%$busqueda%' 
              OR c.nombre LIKE '%$busqueda%' 
              OR c.apellido_paterno LIKE '%$busqueda%'";
}
$sql .= " ORDER BY m.id ASC";
$result = $con->query($sql);

if ($result === false) {
    die("Error en la consulta SQL: " . $con->error . "<br>SQL: <code>" . htmlspecialchars($sql) . "</code>");
}

// Lógica de permisos para mostrar botones
$can_crud = in_array($_SESSION['usuario_rol'], ['administrador', 'veterinario', 'recepcionista']);
$can_view_historial = in_array($_SESSION['usuario_rol'], ['administrador', 'veterinario']);


// Mensajes flash opcionales desde el controlador
$msg_code = isset($_GET['msg']) ? $_GET['msg'] : '';
$alert_text = '';
$alert_style = '';
if ($msg_code !== '') {
    switch ($msg_code) {
        case 'agregado':
            $alert_text = 'Mascota agregada correctamente.';
            $alert_style = 'background:#d4edda;color:#155724;padding:10px;border-radius:4px;margin-bottom:12px;border:1px solid #c3e6cb;';
            break;
        case 'actualizado':
            $alert_text = 'Datos de la mascota actualizados.';
            $alert_style = 'background:#cce5ff;color:#004085;padding:10px;border-radius:4px;margin-bottom:12px;border:1px solid #b8daff;';
            break;
        case 'eliminado':
            $alert_text = 'Mascota eliminada correctamente.';
            $alert_style = 'background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:12px;border:1px solid #f5c6cb;';
            break;
        case 'id_invalido':
            $alert_text = 'ID inválido.';
            $alert_style = 'background:#fff3cd;color:#856404;padding:10px;border-radius:4px;margin-bottom:12px;border:1px solid #ffeeba;';
            break;
        case 'error_tipo':
            $alert_text = 'Tipo de archivo no admitido. Usa JPG, PNG, GIF o WEBP.';
            $alert_style = 'background:#fff3cd;color:#856404;padding:10px;border-radius:4px;margin-bottom:12px;border:1px solid #ffeeba;';
            break;
        case 'error_subida':
            $alert_text = 'Ocurrió un error al subir la imagen. Verifica permisos y tamaño.';
            $alert_style = 'background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:12px;border:1px solid #f5c6cb;';
            break;
        default:
            $alert_text = htmlspecialchars($msg_code);
            $alert_style = 'background:#e2e3e5;color:#383d41;padding:10px;border-radius:4px;margin-bottom:12px;border:1px solid #d6d8db;';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Veterinaria - Mascotas</title>
    <link rel="stylesheet" href="../Vista/Css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    <script src="https://kit.fontawesome.com/b2643188f2.js" crossorigin="anonymous"></script>
    <style>
        /* Estilos de imagen */
        td.img-cell {
            padding: 0; 
            text-align: center; 
            vertical-align: middle; 
            width: 180px; 
            height: 140px; 
            background: #fff;
        }
        .img-container {
            width: 180px; 
            height: 140px; 
            overflow: hidden; 
            margin: auto; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    
    <header>
        <div class="logo">
            <img src="../Vista/imagenes/one pet3.png" alt="Logo VetCode" class="logo-img">
            <h1>Gestión Veterinaria</h1>
        </div>
    </header>
    <div class="contenedor-principal">
        <?php generate_sidebar(basename(__FILE__)); ?>
        <main>
            <h2>Mascotas</h2>
            <?php if ($alert_text !== ''): ?>
                <div style="<?php echo $alert_style; ?>"><?php echo $alert_text; ?></div>
            <?php endif; ?>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:15px;">
                <?php if ($can_crud): ?>
                    <a href="../Controlador/mascotas_controller.php?action=add" class="Agregar" style="text-decoration:none;">+ Agregar</a>
                <?php else: ?>
                    <span></span> 
                <?php endif; ?>
                <form action="mascotas.php" method="GET" style="display:flex; align-items:center; gap:8px;">
                    <input type="text" name="buscar" placeholder="Buscar mascotas..." style="padding:6px 10px; border:1px solid #ccc; border-radius:4px;" value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="Agregar"><i class="fa fa-search"></i></button>
                </form>
            </div>
            
            <div class="table-responsive"> 
                <table>
                    <thead>
                        <tr>
                            <th>ID Mascota</th>
                            <th>Dueño (Cliente)</th>
                            <th>Tipo</th>
                            <th>Raza</th>
                            <th>Nombre</th>
                            <th>Domicilio</th>
                            <th>Imagen</th>
                            <th>Historial</th> 
                            <?php if ($can_crud): // Solo si puede editar/eliminar ?>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>".$row['id']."</td>";
                                echo "<td>".htmlspecialchars($row['apellido_paterno'] . ' ' . $row['apellido_materno'] . ', ' . $row['nombre_cliente'])."</td>";
                                echo "<td>".htmlspecialchars($row['tipo'])."</td>";
                                echo "<td>".htmlspecialchars($row['raza'])."</td>";
                                echo "<td>".htmlspecialchars($row['nombre'])."</td>";
                                echo "<td>".htmlspecialchars($row['domicilio'])."</td>";
                                echo "<td class='img-cell'>";
                                if (!empty($row['imagen'])) {
                                    echo "<div class='img-container'>";
                                    echo "<img src='imagenes/mascotas/" . htmlspecialchars($row['imagen']) . "' alt=''>";
                                    echo "</div>";
                                } else {
                                    echo "<span>-</span>";
                                }
                                echo "</td>";
                                
                                // CELDA DE HISTORIAL (Visible solo para Admin y Veterinario)
                                echo "<td style='text-align:center; vertical-align:middle; width:100px;'>";
                                if ($can_view_historial) {
                                    echo "<a href='historial.php?id_mascota=".$row['id']."' class='edit-btn' style='background-color:#17a2b8; text-decoration:none;'>Historial</a>";
                                } else {
                                    echo "<span>-</span>";
                                }
                                echo "</td>";
                                
                                // BOTONES DE CRUD (Visible solo si $can_crud es true)
                                if ($can_crud) {
                                    echo "<td style='text-align:center; vertical-align:middle;'><a href='../Controlador/mascotas_controller.php?action=edit&id=".$row['id']."' class='edit-btn' style='display:inline-block;margin:0 auto;'>Editar</a></td>";
                                    echo "<td style='text-align:center; vertical-align:middle;'>"
                                         . "<form action='../Controlador/mascotas_controller.php?action=delete' method='POST' onsubmit='return confirm(\"¿Eliminar mascota?\");' style='display:inline-block;margin:0;'>"
                                         . "<input type='hidden' name='id' value='".$row['id']."'>"
                                         . "<button type='submit' class='delete-btn' style='display:inline-block;margin:0 auto;'>Eliminar</button>"
                                         . "</form></td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            $colspan = $can_crud ? 10 : 8; // Ajuste del colspan si no se muestran Editar/Eliminar
                            echo "<tr><td colspan='$colspan'>No se encontraron mascotas.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div> 
        </main>
    </div>
<footer class="PiePagina">
    <table>
        <tr>
            <td>
                OnePet Centro Veterinario<br><br>
                <a href="https://www.google.com/maps/place/OnePet+Centro+Veterinario/@19.4138976,-99.0608182,18.15z/data=!4m14!1m7!3m6!1s0x85d1fd22545035fd:0xa2555f13641c2d07!2sOnePet+Centro+Veterinario!8m2!3d19.4134147!4d-99.06003!16s%2Fg%2F11rsrz6mx_!3m5!1s0x85d1fd22545035fd:0xa2555f13641c2d07!8m2!3d19.4134147!4d-99.06003!16s%2Fg%2F11rsrz6mx_?entry=ttu&g_ep=EgoyMDI1MTExNy4wIKXMDSoASAFQAw%3D%3D" target="_blank">
                    <i class="fa-solid fa-location-dot"></i>Eje 1 Norte Av. Xochimilco #50, <br>
                    Agrícola Pantitlán, Iztacalco, 08100 CDMX
                </a>
                <br>
                <br>
                <i class="fa-solid fa-phone"></i>TEL: 55 6308 8151<br>
                <p>&copy; 2025 VetCode - Todos los derechos reservados.</p>
            </td>
            <td>
                <a href="https://www.facebook.com/share/1FRjPmVmGe/?mibextid=wwXIfr" target="_blank">
                    <i class="fa-brands fa-facebook-f"></i><br>Facebook<br><br>
                </a>
                
                <a href="https://wa.link/h255ft" target="_blank">
                    <i class="fa-brands fa-whatsapp"></i><br>WhatsApp<br><br>
                </a>
                
                <a href="https://www.instagram.com/onepetvet?igsh=MXZodHVvcmpiNmZ6cA==" target="_blank">
                    <i class="fa-brands fa-instagram"></i><br>Instagram<br><br>
                </a>
            </td>
        </tr>
    </table>
</footer>
</body>
</html>