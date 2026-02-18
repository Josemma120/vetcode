<?php
// 1. Inclusión de seguridad y conexión
include __DIR__ . "/../Controlador/seguridad.php"; 
// Solo Recepcionista y Administrador tienen acceso a Clientes
check_access(['administrador', 'recepcionista']); 
include __DIR__ . "/../Modelo/conexion.php";
// ------------------------------------

// NOTA: La lógica de DELETE ha sido eliminada de aquí y debe ser manejada por clientes_controller.php

// Buscar clientes
$busqueda = isset($_GET['buscar']) ? $con->real_escape_string($_GET['buscar']) : '';
// ... (El resto de la lógica SQL es correcta) ...
$sql = "SELECT * FROM clientes";
if ($busqueda !== '') {
    $sql .= " WHERE nombre LIKE '%$busqueda%' 
              OR apellido_paterno LIKE '%$busqueda%' 
              OR apellido_materno LIKE '%$busqueda%' 
              OR domicilio LIKE '%$busqueda%'
              OR whatsapp LIKE '%$busqueda%'
              OR email LIKE '%$busqueda%'";
}
$sql .= " ORDER BY id ASC";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Veterinaria - Clientes</title>
    <link rel="stylesheet" href="../Vista/Css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    <script src="https://kit.fontawesome.com/b2643188f2.js" crossorigin="anonymous"></script>
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
            <h2>Lista de Clientes</h2>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:15px;">
                <a href="../Controlador/clientes_controller.php?action=add" class="Agregar" style="text-decoration:none;">+ Agregar</a>
                <form action="clientes.php" method="GET" style="display:flex; align-items:center; gap:8px;">
                    <input type="text" name="buscar" placeholder="Buscar clientes..." style="padding:6px 10px; border:1px solid #ccc; border-radius:4px;" value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="Agregar"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Nombre</th>
                        <th>Domicilio</th>
                        <th>Whatsapp</th>
                        <th>Email</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row['id']."</td>";
                            echo "<td>".htmlspecialchars($row['apellido_paterno'])."</td>";
                            echo "<td>".htmlspecialchars($row['apellido_materno'])."</td>";
                            echo "<td>".htmlspecialchars($row['nombre'])."</td>";
                            echo "<td>".htmlspecialchars($row['domicilio'])."</td>";
                            echo "<td>".htmlspecialchars($row['whatsapp'])."</td>";
                            echo "<td>".htmlspecialchars($row['email'])."</td>";
                            echo "<td><a href='../Controlador/clientes_controller.php?action=edit&id=".$row['id']."' class='edit-btn'>Editar</a></td>";
                            // 3. El botón de Eliminar apunta al controlador
                            echo "<td>
                                    <form action='../Controlador/clientes_controller.php?action=delete' method='POST' onsubmit='return confirm(\"¿Eliminar cliente?\");' style='display:inline;'>
                                        <input type='hidden' name='id' value='".$row['id']."'>
                                        <button type='submit' class='delete-btn'>Eliminar</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No se encontraron clientes.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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