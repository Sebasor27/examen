<?php
require('db_config.php'); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $año = $_POST["año"];
    $disponible = isset($_POST["disponible"]) ? 1 : 0;
    $is_edit = isset($_POST["is_edit"]) && $_POST["is_edit"] === "true";
    
    if ($is_edit) {
        $vehiculo_id = $_POST["vehiculo_id"];
        $sql = "UPDATE vehiculos SET marca='$marca', modelo='$modelo', año='$año', disponible='$disponible' WHERE vehiculo_id='$vehiculo_id'";
    } else {
        $sql = "INSERT INTO vehiculos (marca, modelo, año, disponible) VALUES ('$marca', '$modelo', '$año', '$disponible')";
    }

    if ($conn->query($sql) === TRUE) {
        echo 'Registro ' . ($is_edit ? 'actualizado' : 'agregado') . ' exitosamente';
    } else {
        echo 'Error: ' . $sql . '<br>' . $conn->error;
    }
}

if (isset($_GET["delete_id"])) {
    $vehiculo_id = $_GET["delete_id"];
    $sql = "DELETE FROM vehiculos WHERE vehiculo_id='$vehiculo_id'";
    if ($conn->query($sql) === TRUE) {
        echo 'Registro eliminado exitosamente';
    } else {
        echo 'Error: ' . $sql . '<br>' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vehículos</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        function cargarDatos(vehiculo_id, marca, modelo, año, disponible) {
            document.getElementById("vehiculo_id").value = vehiculo_id;
            document.getElementById("marca").value = marca;
            document.getElementById("modelo").value = modelo;
            document.getElementById("año").value = año;
            document.getElementById("disponible").checked = disponible;
            document.getElementById("is_edit").value = "true";
        }
    </script>
</head>
<body>
    <a href="vehiculo.php">Volver a la página principal</a>
    <h1>Vehículos</h1>
    <form action="carro.php" method="post">
        <input type="hidden" id="vehiculo_id" name="vehiculo_id">
        
        <label for="marca">Marca:</label>
        <input type="text" id="marca" name="marca" required>

        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo" required>

        <label for="año">Año:</label>
        <input type="number" id="año" name="año" required>
        
        <label for="disponible">Disponible:</label>
        <input type="checkbox" id="disponible" name="disponible" value="1">

        <input type="hidden" id="is_edit" name="is_edit" value="false">
        <input type="submit" value="Guardar">
    </form>

    <h2>Lista de Vehículos</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID Vehículo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Disponible</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM vehiculos";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["vehiculo_id"] . "</td>";
                    echo "<td>" . $row["marca"] . "</td>";
                    echo "<td>" . $row["modelo"] . "</td>";
                    echo "<td>" . $row["año"] . "</td>";
                    echo "<td>" . ($row["disponible"] ? 'Sí' : 'No') . "</td>";
                    echo "<td>
                         <button class='btn btn-warning' onclick=\"cargarDatos('" . $row["vehiculo_id"] . "', '" . $row["marca"] . "', '" . $row["modelo"] . "', '" . $row["año"] . "', '" . $row["disponible"] . "')\">Editar</button>
                         <a href='carro.php?delete_id=" . $row["vehiculo_id"] . "' class='btn btn-danger' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este registro?')\">Eliminar</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay registros</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
