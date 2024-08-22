<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un supervisor
if ($_SESSION['usuario'] != 'supervisor') {
    header('Location: login.php');
    exit();
}

// Obtener el DNI del movilizador desde la URL
if (!isset($_GET['dni']) || empty($_GET['dni'])) {
    header('Location: dirigente_details.php?dni=' . $_SESSION['dni']);
    exit();
}

$dniMovilizador = $_GET['dni'];
$error = '';

// Consultar información del movilizador
$queryMovilizador = "SELECT * FROM Movilizadores WHERE DNI = '$dniMovilizador'";
$resultMovilizador = $conn->query($queryMovilizador);

if ($resultMovilizador->num_rows == 0) {
    $error = "No se encontró al movilizador.";
}

// Consultar votantes para el movilizador
$queryVotantes = "SELECT * FROM Votantes WHERE MovilizadorDNI = '$dniMovilizador'";
$resultVotantes = $conn->query($queryVotantes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Movilizador</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Detalles del Movilizador</h1>
        <a href="dirigente_details.php?dni=<?php echo $_SESSION['dni']; ?>" class="btn btn-light">Volver a Dirigente</a>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>

    <div class="container mt-4">
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php else: ?>
            <!-- Información del movilizador -->
            <?php
            $movilizador = $resultMovilizador->fetch_assoc();
            ?>
            <h3>Movilizador: <?php echo $movilizador['Nombre'] . ' ' . $movilizador['Apellido']; ?></h3>
            <p><strong>DNI:</strong> <?php echo $movilizador['DNI']; ?></p>
            <p><strong>Circuito:</strong> <?php echo $movilizador['Circuito']; ?></p>

            <!-- Tabla de votantes -->
            <h4>Votantes</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Apellido</th>
                        <th>Nombre</th>
                        <th>Circuito</th>
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
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No hay votantes registrados</td></tr>";
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
