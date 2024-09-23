<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un supervisor
if ($_SESSION['usuario'] != 'supervisor') {
    header('Location: login.php');
    exit();
}

// Obtener el DNI del dirigente desde la URL
if (!isset($_GET['dni']) || empty($_GET['dni'])) {
    header('Location: supervisor_dashboard.php');
    exit();
}

$dniDirigente = $_GET['dni'];
$error = '';

// Consultar información del dirigente
$queryDirigente = "SELECT * FROM Dirigentes WHERE DNI = '$dniDirigente'";
$resultDirigente = $conn->query($queryDirigente);

if ($resultDirigente->num_rows == 0) {
    $error = "No se encontró al dirigente.";
}

// Consultar movilizadores para el dirigente, ordenados por apellido
$queryMovilizadores = "
    SELECT m.DNI, m.Nombre, m.Apellido, m.Circuito, 
           (SELECT COUNT(*) FROM Votantes WHERE MovilizadorDNI = m.DNI) AS CantidadVotantes
    FROM Movilizadores m
    WHERE m.DirigenteDNI = '$dniDirigente'
    ORDER BY m.Apellido ASC";
$resultMovilizadores = $conn->query($queryMovilizadores);

// Consultar votantes registrados por los movilizadores, ordenados por apellido
$queryVotantes = "
    SELECT v.DNI, v.Nombre, v.Apellido, v.Circuito, m.Apellido AS MovilizadorApellido, m.Nombre AS MovilizadorNombre
    FROM Votantes v
    JOIN Movilizadores m ON v.MovilizadorDNI = m.DNI
    WHERE m.DirigenteDNI = '$dniDirigente'
    ORDER BY v.MovilizadorDNI ASC";
$resultVotantes = $conn->query($queryVotantes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Dirigente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Detalles del Dirigente</h1>
        <a href="supervisor_dashboard.php" class="btn btn-light">Volver al Dashboard</a>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>

    <div class="container mt-4">
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php else: ?>
            <!-- Información del dirigente -->
            <?php
            $dirigente = $resultDirigente->fetch_assoc();
            ?>
            <h3>Dirigente: <?php echo $dirigente['Nombre'] . ' ' . $dirigente['Apellido']; ?></h3>
            <p><strong>DNI:</strong> <?php echo $dirigente['DNI']; ?></p>
            <p><strong>Circuito:</strong> <?php echo $dirigente['Circuito']; ?></p>

            <!-- Botón para exportar a PDF -->
            <a href="export_to_pdf.php?dni=<?php echo $dniDirigente; ?>" target="_blank" class="btn btn-warning mb-3">Exportar a PDF</a>

            <!-- Tabla de movilizadores -->
            <h4>Movilizadores</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Circuito</th>
                        <th>Cantidad de Votantes</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultMovilizadores->num_rows > 0) {
                        while ($movilizador = $resultMovilizadores->fetch_assoc()) {
                            $dniMovilizador = $movilizador['DNI'];

                            echo "<tr>
                                    <td>{$movilizador['DNI']}</td>
                                    <td>{$movilizador['Nombre']}</td>
                                    <td>{$movilizador['Apellido']}</td>
                                    <td>{$movilizador['Circuito']}</td>
                                    <td>{$movilizador['CantidadVotantes']}</td>
                                    <td><a href='movilizador_details.php?dni={$dniMovilizador}' class='btn btn-info btn-sm'>Ver Detalles</a></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No hay movilizadores registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Tabla de votantes -->
            <h4 class="mt-4">Votantes Registrados</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Apellido</th>
                        <th>Nombre</th>
                        <th>Circuito</th>
                        <th>Movilizador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultVotantes->num_rows > 0) {
                        while ($votante = $resultVotantes->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$votante['DNI']}</td>
                                    <td>{$votante['Apellido']}</td>
                                    <td>{$votante['Nombre']}</td>
                                    <td>{$votante['Circuito']}</td>
                                    <td>{$votante['MovilizadorApellido']} {$votante['MovilizadorNombre']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No hay votantes registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
