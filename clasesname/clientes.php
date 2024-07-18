<?php
require('db_config.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $licencia = $_POST["licencia"];
    $telefono = $_POST["telefono"];
    $is_edit = isset($_POST["is_edit"]) && $_POST["is_edit"] === "true";
    
    if ($is_edit) {
        $cliente_id = $_POST["cliente_id"];
        $sql = "UPDATE clientes SET nombre='$nombre', apellido='$apellido', licencia='$licencia', telefono='$telefono' WHERE cliente_id='$cliente_id'";
    } else {
        $sql = "INSERT INTO clientes (nombre, apellido, licencia, telefono) VALUES ('$nombre', '$apellido', '$licencia', '$telefono')";
    }

    if ($conn->query($sql) === TRUE) {
        echo 'Registro ' . ($is_edit ? 'actualizado' : 'agregado') . ' exitosamente';
    } else {
        echo 'Error: ' . $sql . '<br>' . $conn->error;
    }
}

if (isset($_GET["delete_id"])) {
    $cliente_id = $_GET["delete_id"];
    $sql = "DELETE FROM clientes WHERE cliente_id='$cliente_id'";
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
    <title>Clientes</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        function cargarDatos(cliente_id, nombre, apellido, licencia, telefono) {
            document.getElementById("cliente_id").value = cliente_id;
            document.getElementById("nombre").value = nombre;
            document.getElementById("apellido").value = apellido;
            document.getElementById("licencia").value = licencia;
            document.getElementById("telefono").value = telefono;
            document.getElementById("is_edit").value = "true";
        }
    </script>
</head>
<body>
    <a href="vehiculo.php">Volver a la página principal</a>
    <h1>Clientes</h1>
    <form action="clientes.php" method="post">
        <input type="hidden" id="cliente_id" name="cliente_id">
        
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>

        <label for="licencia">Licencia:</label>
        <input type="text" id="licencia" name="licencia" required>
        
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required>

        <input type="hidden" id="is_edit" name="is_edit" value="false">
        <input type="submit" value="Guardar">
    </form>

    <h2>Lista de Clientes</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID Cliente</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Licencia</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM clientes";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["cliente_id"] . "</td>";
                    echo "<td>" . $row["nombre"] . "</td>";
                    echo "<td>" . $row["apellido"] . "</td>";
                    echo "<td>" . $row["licencia"] . "</td>";
                    echo "<td>" . $row["telefono"] . "</td>";
                    echo "<td>
                         <button class='btn btn-warning' onclick=\"cargarDatos('" . $row["cliente_id"] . "', '" . $row["nombre"] . "', '" . $row["apellido"] . "', '" . $row["licencia"] . "', '" . $row["telefono"] . "')\">Editar</button>
                         <a href='clientes.php?delete_id=" . $row["cliente_id"] . "' class='btn btn-danger' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este registro?')\">Eliminar</a></td>";
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
