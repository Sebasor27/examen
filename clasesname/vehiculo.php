<?php
    require('db_config.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $fechaInicio = $_POST['fecha_inicio'];
        $fechaFin = $_POST['fecha_fin'];
        $vehiculoId = $_POST['vehiculo_id'];
        $clienteId = $_POST['cliente_id'];

        $datetime1 = new DateTime($fechaInicio);
        $datetime2 = new DateTime($fechaFin);
        $interval = $datetime1->diff($datetime2);
        $dias = $interval->format('%a');
        $costoTotal = $dias * 15;
        $sql = "INSERT INTO alquileres (cliente_id, vehiculo_id, fecha_inicio, fecha_fin, costo)
                VALUES ('$clienteId', '$vehiculoId', '$fechaInicio', '$fechaFin', '$costoTotal')";

        if (mysqli_query($conn, $sql)) {

            $sql_update = "UPDATE vehiculos SET disponible = 0 WHERE vehiculo_id = '$vehiculoId'";
            mysqli_query($conn, $sql_update);

            echo "Alquiler registrado correctamente.";
        } else {
            echo "Error al registrar el alquiler: " . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Renta de autos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Renta de autos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="carro.php">Carros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clientes.php">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rentas.php">Rentas</a>
                    </li>
                </ul>
                <form class="d-flex" method="GET" action="">
                    <select class="form-select me-2" name="tipo">
                        <option value="modelo">Modelo</option>
                        <option value="marca">Marca</option>
                        <option value="disponible">Disponibilidad</option>
                    </select>
                    <input class="form-control me-2" type="search" name="busqueda" placeholder="Buscar" aria-label="Search">
                    <button class="btn btn-outline-success mr-6" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Resultados de Búsqueda</h1>
        <hr>

        <div class="row">
            <?php
            require('db_config.php');
            $whereClause = "WHERE disponible = 1";
            $searchTerm = '';
            if (isset($_GET['busqueda'])) {
                $searchTerm = mysqli_real_escape_string($conn, $_GET['busqueda']);
                $tipo = $_GET['tipo'];
                switch ($tipo) {
                    case 'modelo':
                        $whereClause .= " AND modelo LIKE '%$searchTerm%'";
                        break;
                    case 'marca':
                        $whereClause .= " AND marca LIKE '%$searchTerm%'";
                        break;
                    case 'color':
                        break;
                    case 'disponible':
                        break;
                    default:
                        $whereClause .= " AND modelo LIKE '%$searchTerm%'";
                        break;
                }
            }
            $query = "SELECT * FROM vehiculos $whereClause";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $row['marca'] . ' ' . $row['modelo'] . '</h5>';
                    echo '<p class="card-text">Año: ' . $row['año'] . '</p>';
                    echo '<form>';
                    echo '<input type="hidden" name="vehiculo_id" value="' . $row['vehiculo_id'] . '">';
                    echo '<select class="form-select mb-2 cliente-select" name="cliente_id" onchange="updateRentButton(this, \'' . $row['vehiculo_id'] . '\')">';

                    $queryClientes = "SELECT cliente_id, nombre, apellido FROM clientes";
                    $resultClientes = mysqli_query($conn, $queryClientes);
                    if (mysqli_num_rows($resultClientes) > 0) {

                        mysqli_data_seek($resultClientes, 0);

                        while ($rowCliente = mysqli_fetch_assoc($resultClientes)) {
                            echo '<option value="' . $rowCliente['cliente_id'] . '">' . $rowCliente['nombre'] . ' ' . $rowCliente['apellido'] . '</option>';
                        }
                    } else {
                        echo '<option value="">No hay clientes disponibles</option>';
                    }

                    echo '</select>';
                    echo '<button type="button" class="btn btn-primary rent-button" data-bs-toggle="modal" data-bs-target="#rentModal" data-vehiculo-id="' . $row['vehiculo_id'] . '" data-cliente-id="">Rentar</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No se encontraron vehículos disponibles.</p>';
            }

            mysqli_close($conn);
            ?>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="rentModal" tabindex="-1" aria-labelledby="rentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rentModalLabel">Rentar Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rentForm" method="POST" action="vehiculo.php">
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaFin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="fechaFin" name="fecha_fin" required>
                        </div>
                        <p>Valor diario: $15</p>
                        <input type="hidden" id="vehiculoId" name="vehiculo_id">
                        <input type="hidden" id="clienteId" name="cliente_id">
                        <button type="submit" class="btn btn-primary">Rentar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var rentModal = document.getElementById('rentModal');
        rentModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var vehiculoId = button.getAttribute('data-vehiculo-id');
            var clienteId = button.getAttribute('data-cliente-id');
            document.getElementById('vehiculoId').value = vehiculoId;
            document.getElementById('clienteId').value = clienteId;
        });

        function updateRentButton(selectElement, vehiculoId) {
            var clienteId = selectElement.value;
            var rentButton = selectElement.closest('form').querySelector('button[data-bs-toggle="modal"]');
            rentButton.setAttribute('data-cliente-id', clienteId);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
