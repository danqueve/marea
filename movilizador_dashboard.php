<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un movilizador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] != 'movilizador') {
    header('Location: movilizador_login.php');
    exit();
}

$dniMovilizador = $_SESSION['dni'];

// Obtener el nombre del movilizador
$queryMovilizador = "SELECT Apellido, Nombre FROM Movilizadores WHERE DNI = ?";
$stmtMovilizador = $conn->prepare($queryMovilizador);
$stmtMovilizador->bind_param("s", $dniMovilizador);
$stmtMovilizador->execute();
$resultMovilizador = $stmtMovilizador->get_result();
$movilizadorNombreCompleto = "No asignado";
if ($resultMovilizador->num_rows > 0) {
    $movilizadorInfo = $resultMovilizador->fetch_assoc();
    $movilizadorNombreCompleto = $movilizadorInfo['Apellido'] . " " . $movilizadorInfo['Nombre'];
}

// Obtener el dirigente vinculado al movilizador
$queryDirigente = "SELECT DirigenteDNI FROM Movilizadores WHERE DNI = ?";
$stmtDirigente = $conn->prepare($queryDirigente);
$stmtDirigente->bind_param("s", $dniMovilizador);
$stmtDirigente->execute();
$resultDirigente = $stmtDirigente->get_result();
$dirigenteDNI = $resultDirigente->num_rows > 0 ? $resultDirigente->fetch_assoc()['DirigenteDNI'] : null;

// Obtener el nombre del dirigente
$dirigenteNombreCompleto = "No asignado";
if ($dirigenteDNI) {
    $queryDirigenteInfo = "SELECT Apellido, Nombre FROM Dirigentes WHERE DNI = ?";
    $stmtDirigenteInfo = $conn->prepare($queryDirigenteInfo);
    $stmtDirigenteInfo->bind_param("s", $dirigenteDNI);
    $stmtDirigenteInfo->execute();
    $resultDirigenteInfo = $stmtDirigenteInfo->get_result();
    if ($resultDirigenteInfo->num_rows > 0) {
        $dirigenteInfo = $resultDirigenteInfo->fetch_assoc();
        $dirigenteNombreCompleto = $dirigenteInfo['Apellido'] . " " . $dirigenteInfo['Nombre'];
    }
}

// Variables para la búsqueda y registro
$votante = [];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $dniVotante = $_POST['dni_votante'];

    // Buscar el votante en PADRONES3
    $queryVotante = "SELECT DNI, Apellido, Nombre, Direccion, Circuito FROM PADRONES3 WHERE DNI = ?";
    $stmtVotante = $conn->prepare($queryVotante);
    $stmtVotante->bind_param("s", $dniVotante);
    $stmtVotante->execute();
    $resultVotante = $stmtVotante->get_result();

    if ($resultVotante->num_rows > 0) {
        $votante = $resultVotante->fetch_assoc();
    } else {
        $error = "El DNI no se encuentra en el padrón.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $dniVotante = $_POST['dni_votante'];

    // Verificar si el DNI ya está registrado en la tabla de votantes
    $queryCheck = "SELECT * FROM Votantes WHERE DNI = ?";
    $stmtCheck = $conn->prepare($queryCheck);
    $stmtCheck->bind_param("s", $dniVotante);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows == 0) {
        $apellido = $_POST['apellido'];
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $circuito = $_POST['circuito'];
        $fechaHoraRegistro = date("Y-m-d H:i:s");

        // Insertar nuevo votante
        $queryInsert = "INSERT INTO Votantes (DNI, Apellido, Nombre, Direccion, Circuito, FechaHoraRegistro, MovilizadorDNI, DirigenteApellidoNombre, DirigenteDNI) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bind_param("ssssssss", $dniVotante, $apellido, $nombre, $direccion, $circuito, $fechaHoraRegistro, $dniMovilizador, $dirigenteNombreCompleto, $dirigenteDNI);

        if ($stmtInsert->execute()) {
            $success = "Votante registrado con éxito.";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "El DNI ya está registrado por otro movilizador.";
    }
}

// Obtener la lista de votantes registrados por el movilizador
$queryVotantesRegistrados = "SELECT * FROM Votantes WHERE MovilizadorDNI = ?";
$stmtVotantesRegistrados = $conn->prepare($queryVotantesRegistrados);
$stmtVotantesRegistrados->bind_param("s", $dniMovilizador);
$stmtVotantesRegistrados->execute();
$resultVotantesRegistrados = $stmtVotantesRegistrados->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Movilizador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Movilizador: <?= htmlspecialchars($movilizadorNombreCompleto) ?></h1>
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

        <?php if (!empty($votante)): ?>
            <form method="post">
                <?php foreach (['DNI', 'Apellido', 'Nombre', 'Direccion', 'Circuito'] as $campo): ?>
                    <div class="form-group">
                        <label for="<?= strtolower($campo) ?>"><?= ucfirst(strtolower($campo)) ?>:</label>
                        <input type="text" class="form-control" id="<?= strtolower($campo) ?>" name="<?= strtolower($campo) ?>" 
                               value="<?= htmlspecialchars($votante[$campo] ?? '') ?>" readonly>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="registrar" class="btn btn-success">Registrar Votante</button>
            </form>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-4"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mt-4"><?= $success ?></div>
        <?php endif; ?>

        <h2 class="mt-5">Votantes Registrados</h2>
        <table class="table table-striped">
            <thead>
                <tr><th>#</th><th>DNI</th><th>Apellido</th><th>Nombre</th><th>Dirección</th><th>Circuito</th></tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $resultVotantesRegistrados->fetch_assoc()): ?>
                    <tr><td><?= $i++ ?></td><td><?= $row['DNI'] ?></td><td><?= $row['Apellido'] ?></td><td><?= $row['Nombre'] ?></td><td><?= $row['Direccion'] ?></td><td><?= $row['Circuito'] ?></td></tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
