<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST['dni'];
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];

    // Verificar si el DNI ya está registrado como supervisor
    $queryCheckSupervisor = "SELECT * FROM Supervisores WHERE DNI = '$dni'";
    $resultCheckSupervisor = $conn->query($queryCheckSupervisor);

    if ($resultCheckSupervisor->num_rows > 0) {
        $error = "El DNI ya está registrado como supervisor.";
    } else {
        // Insertar el nuevo supervisor con la misma contraseña (DNI)
        $queryInsertSupervisor = "INSERT INTO Supervisores (DNI, Apellido, Nombre, Clave) VALUES ('$dni', '$apellido', '$nombre', '$dni')";
        if ($conn->query($queryInsertSupervisor) === TRUE) {
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error = "Error al registrar el supervisor: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Supervisor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Registrar Supervisor</h2>
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
            <button type="submit" class="btn btn-success">Registrar Supervisor</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
