<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un supervisor
if ($_SESSION['usuario'] != 'supervisor') {
    header('Location: login.php');
    exit();
}

$dniSupervisor = $_SESSION['dni'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST['dni'];
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];
    $circuito = $_POST['circuito'];
    $horaRegistro = date('Y-m-d H:i:s');

    // Verificar si el DNI ya está registrado
    $queryCheck = "SELECT * FROM Dirigentes WHERE DNI = '$dni'";
    $resultCheck = $conn->query($queryCheck);

    if ($resultCheck->num_rows > 0) {
        $error = "El DNI ya está registrado como dirigente.";
    } else {
        // Insertar el nuevo dirigente
        $queryInsert = "INSERT INTO Dirigentes (DNI, Apellido, Nombre, Circuito, HoraRegistro, SupervisorDNI, Clave) 
                        VALUES ('$dni', '$apellido', '$nombre', '$circuito', '$horaRegistro', '$dniSupervisor', '$dni')";
        if ($conn->query($queryInsert) === TRUE) {
            header('Location: supervisor_dashboard.php');
            exit();
        } else {
            $error = "Error al registrar el dirigente: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Dirigente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Registrar Dirigente</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" class="form-control" id="dni" name="dni" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="circuito">Circuito</label>
                <input type="text" class="form-control" id="circuito" name="circuito" required>
            </div>
            <button type="submit" class="btn btn-success">Registrar Dirigente</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
