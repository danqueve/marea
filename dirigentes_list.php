<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un supervisor
if ($_SESSION['usuario'] != 'supervisor') {
    header('Location: login.php');
    exit();
}

// Consultar todos los dirigentes
$queryDirigentes = "SELECT * FROM Dirigentes";
$resultDirigentes = $conn->query($queryDirigentes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Dirigentes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Lista de Dirigentes</h1>
        <a href="supervisor_dashboard.php" class="btn btn-light">INICIO</a>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>

    <div class="container mt-4">
    <a href="export_dirigentes_to_pdf.php" target="_blank" class="btn btn-success mb-3">Exportar a PDF</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DNI</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Circuito</th>
                    <th>Movilizadores</th>
                    <th>Votantes</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultDirigentes->num_rows > 0) {
                    $i = 1;
                    while ($row = $resultDirigentes->fetch_assoc()) {
                        $dniDirigente = $row['DNI'];

                        // Contar movilizadores para el dirigente
                        $queryMovilizadoresCount = "SELECT COUNT(*) AS total FROM Movilizadores WHERE DirigenteDNI = '$dniDirigente'";
                        $resultMovilizadoresCount = $conn->query($queryMovilizadoresCount);
                        $countMovilizadores = $resultMovilizadoresCount->fetch_assoc()['total'];

                        // Contar votantes para el dirigente
                        $queryVotantesCount = "SELECT COUNT(*) AS total FROM Votantes WHERE MovilizadorDNI IN (SELECT DNI FROM Movilizadores WHERE DirigenteDNI = '$dniDirigente')";
                        $resultVotantesCount = $conn->query($queryVotantesCount);
                        $countVotantes = $resultVotantesCount->fetch_assoc()['total'];

                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$row['DNI']}</td>
                                <td>{$row['Apellido']}</td>
                                <td>{$row['Nombre']}</td>
                                <td>{$row['Circuito']}</td>
                                <td>{$countMovilizadores}</td>
                                <td>{$countVotantes}</td>
                                <td><a href='dirigente_details.php?dni={$row['DNI']}' class='btn btn-info'>Ver Detalles</a></td>
                              </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No hay dirigentes registrados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
