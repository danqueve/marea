<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un dirigente
if ($_SESSION['usuario'] != 'dirigente') {
    header('Location: login.php');
    exit();
}

$dniDirigente = $_SESSION['dni'];

// Obtener datos del dirigente
$queryDirigente = "SELECT Nombre, Apellido FROM Dirigentes WHERE DNI = '$dniDirigente'";
$resultDirigente = $conn->query($queryDirigente);

if ($resultDirigente->num_rows > 0) {
    $dirigente = $resultDirigente->fetch_assoc();
    $nombreDirigente = $dirigente['Nombre'];
    $apellidoDirigente = $dirigente['Apellido'];
} else {
    // En caso de error, redirigir al login
    header('Location: login.php');
    exit();
}

// Obtener los movilizadores vinculados al dirigente
$queryMovilizadores = "SELECT * FROM Movilizadores WHERE DirigenteDNI = '$dniDirigente'";
$resultMovilizadores = $conn->query($queryMovilizadores);

// Obtener la lista de votantes registrados por cada movilizador
$movilizadores = [];
while ($row = $resultMovilizadores->fetch_assoc()) {
    $movilizadorDNI = $row['DNI'];
    
    // Obtener la cantidad de votantes registrados por este movilizador
    $queryCountVotantes = "SELECT COUNT(*) AS total FROM Votantes WHERE MovilizadorDNI = '$movilizadorDNI'";
    $resultCountVotantes = $conn->query($queryCountVotantes);
    $countVotantes = $resultCountVotantes->fetch_assoc()['total'];
    
    $row['total_votantes'] = $countVotantes;
    $movilizadores[] = $row;
}

// Obtener los votantes registrados por el dirigente y los detalles del movilizador
$queryVotantes = "
    SELECT v.*, m.Apellido AS MovilizadorApellido, m.Nombre AS MovilizadorNombre 
    FROM Votantes v 
    JOIN Movilizadores m ON v.MovilizadorDNI = m.DNI 
    WHERE m.DirigenteDNI = '$dniDirigente'
";
$resultVotantes = $conn->query($queryVotantes);

// Calcular la suma total de votantes
$totalVotantes = 0;
if ($resultVotantes->num_rows > 0) {
    while ($row = $resultVotantes->fetch_assoc()) {
        $totalVotantes++;
    }
    // Reiniciar el puntero del resultado para volver a recorrerlo
    $resultVotantes->data_seek(0);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dirigente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Dirigente <?php echo $nombreDirigente . ' ' . $apellidoDirigente; ?></h1>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>

    <div class="container mt-4">
        <a href="register_movilizadores.php" class="btn btn-success mb-3">Registrar Movilizador</a>

        <h2>Movilizadores Vinculados</h2>
        
        <table class="table table-warning table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DNI</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Circuito</th>
                    <th>Registrados</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($movilizadores)) {
                    $i = 1;
                    foreach ($movilizadores as $movilizador) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$movilizador['DNI']}</td>
                                <td>{$movilizador['Apellido']}</td>
                                <td>{$movilizador['Nombre']}</td>
                                <td>{$movilizador['Circuito']}</td>
                                <td>{$movilizador['total_votantes']}</td>
                              </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No hay movilizadores vinculados</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2 class="mt-5">Votantes Registrados (Total: <?php echo $totalVotantes; ?>)</h2>
        <table class="table table-success table-striped">
            <thead>
                <tr>
                    <th>#</th>
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
                    $i = 1;
                    while ($row = $resultVotantes->fetch_assoc()) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$row['DNI']}</td>
                                <td>{$row['Apellido']}</td>
                                <td>{$row['Nombre']}</td>
                                <td>{$row['Circuito']}</td>
                                <td>{$row['MovilizadorApellido']} {$row['MovilizadorNombre']}</td>
                              </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No hay votantes registrados</td></tr>";
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
