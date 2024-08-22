<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un movilizador
if ($_SESSION['usuario'] != 'movilizador') {
    header('Location: movilizador_login.php');
    exit();
}

$dniMovilizador = $_SESSION['dni'];

// Obtener el dirigente vinculado al movilizador
$queryDirigente = "SELECT DirigenteDNI FROM Movilizadores WHERE DNI = '$dniMovilizador'";
$resultDirigente = $conn->query($queryDirigente);
$dirigenteDNI = $resultDirigente->fetch_assoc()['DirigenteDNI'];

// Obtener los datos del dirigente
$queryDirigenteInfo = "SELECT Apellido, Nombre FROM Dirigentes WHERE DNI = '$dirigenteDNI'";
$resultDirigenteInfo = $conn->query($queryDirigenteInfo);
$dirigenteInfo = $resultDirigenteInfo->fetch_assoc();
$dirigenteNombreCompleto = $dirigenteInfo['Apellido'] . " " . $dirigenteInfo['Nombre'];

// Procesar la búsqueda y registro de votantes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $dniVotante = $_POST['dni_votante'];
    $queryVotante = "SELECT * FROM PADRON WHERE DNI = '$dniVotante'";
    $resultVotante = $conn->query($queryVotante);

    if ($resultVotante->num_rows > 0) {
        $votante = $resultVotante->fetch_assoc();
    } else {
        $error = "El DNI no se encuentra en el padrón.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $dniVotante = $_POST['dni_votante'];
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];
    $circuito = $_POST['circuito'];
    $fechaHoraRegistro = date("Y-m-d H:i:s");

    // Verificar si el DNI ya está registrado en la tabla de votantes
    $queryCheck = "SELECT * FROM Votantes WHERE DNI = '$dniVotante'";
    $resultCheck = $conn->query($queryCheck);

    if ($resultCheck->num_rows == 0) {
        // Insertar nuevo votante
        $queryInsert = $conn->prepare("INSERT INTO Votantes (DNI, Apellido, Nombre, Circuito, FechaHoraRegistro, MovilizadorDNI, DirigenteApellidoNombre) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $queryInsert->bind_param("sssssss", $dniVotante, $apellido, $nombre, $circuito, $fechaHoraRegistro, $dniMovilizador, $dirigenteNombreCompleto);

        if ($queryInsert->execute()) {
            $success = "Votante registrado con éxito.";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "El DNI ya está registrado.";
    }
}

// Obtener la lista de votantes registrados por el movilizador
$queryVotantesRegistrados = "SELECT * FROM Votantes WHERE MovilizadorDNI = '$dniMovilizador'";
$resultVotantesRegistrados = $conn->query($queryVotantesRegistrados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Movilizador</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Movilizador Dashboard</h1>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>

    <div class="container mt-4">
        <h2>Buscar y Registrar Votantes</h2>

        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="dni_votante">DNI del Votante:</label>
                <input type="text" class="form-control" id="dni_votante" name="dni_votante" required>
            </div>
            <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
        </form>

        <?php if (isset($votante)): ?>
            <form method="post">
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" class="form-control" id="dni" name="dni_votante" value="<?php echo $votante['DNI']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $votante['Apellido']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $votante['Nombre']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="circuito">Circuito:</label>
                    <input type="text" class="form-control" id="circuito" name="circuito" value="<?php echo $votante['Circuito']; ?>" readonly>
                </div>
                <button type="submit" name="registrar" class="btn btn-success">Registrar Votante</button>
            </form>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-4" role="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success mt-4" role="alert"><?php echo $success; ?></div>
        <?php endif; ?>

        <h2 class="mt-5">Votantes Registrados</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DNI</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Circuito</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultVotantesRegistrados->num_rows > 0) {
                    $i = 1;
                    while ($row = $resultVotantesRegistrados->fetch_assoc()) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$row['DNI']}</td>
                                <td>{$row['Apellido']}</td>
                                <td>{$row['Nombre']}</td>
                                <td>{$row['Circuito']}</td>
                              </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No hay votantes registrados</td></tr>";
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
