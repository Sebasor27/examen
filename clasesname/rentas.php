<?php
require('db_config.php'); 

function fetchAlquileres($conn) {
    $sql = "SELECT 
                a.alquiler_id, 
                c.nombre AS cliente_nombre, 
                v.marca, 
                v.modelo, 
                v.año, 
                v.disponible,
                a.fecha_inicio, 
                a.fecha_fin, 
                a.costo 
            FROM alquileres a
            INNER JOIN clientes c ON a.cliente_id = c.cliente_id
            INNER JOIN vehiculos v ON a.vehiculo_id = v.vehiculo_id";
    $result = mysqli_query($conn, $sql);
    return $result;
}

function markAsCompleted($conn, $alquiler_id) {
    $sql = "UPDATE vehiculos v
            INNER JOIN alquileres a ON v.vehiculo_id = a.vehiculo_id
            SET v.disponible = 1
            WHERE a.alquiler_id = $alquiler_id";
    mysqli_query($conn, $sql);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete'])) {
    $alquiler_id = $_POST['alquiler_id'];
    markAsCompleted($conn, $alquiler_id);
}

$result = fetchAlquileres($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alquileres</title>
</head>
<body>
<a href="vehiculo.php">Volver a la página principal</a>
    <h1>Alquileres</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Disponible</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Costo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['alquiler_id']; ?></td>
                    <td><?php echo $row['cliente_nombre']; ?></td>
                    <td><?php echo $row['marca']; ?></td>
                    <td><?php echo $row['modelo']; ?></td>
                    <td><?php echo $row['año']; ?></td>
                    <td><?php echo $row['disponible'] ? 'Sí' : 'No'; ?></td>
                    <td><?php echo $row['fecha_inicio']; ?></td>
                    <td><?php echo $row['fecha_fin']; ?></td>
                    <td><?php echo $row['costo']; ?></td>
                    <td>
                        <?php if (!$row['disponible']) { ?>
                            <form method="post" action="">
                                <input type="hidden" name="alquiler_id" value="<?php echo $row['alquiler_id']; ?>">
                                <button type="submit" name="complete">Renta Completada</button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
