<?php
session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST['dni'];
    $clave = $_POST['clave'];

    $query = "SELECT * FROM Supervisores WHERE DNI = '$dni'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $supervisor = $result->fetch_assoc();
        if (password_verify($clave, $supervisor['Clave'])) {
            $_SESSION['usuario'] = 'supervisor';
            $_SESSION['dni'] = $dni;
            header('Location: supervisor_dashboard.php');
            exit();
        } else {
            echo "<div class='alert alert-danger' role='alert'>Clave incorrecta.</div>";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>DNI no registrado.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Iniciar Sesión</h1>
    </header>

    <div class="container mt-4">
        <form method="post">
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" class="form-control" id="dni" name="dni" required>
            </div>
            <div class="form-group">
                <label for="clave">Clave:</label>
                <input type="password" class="form-control" id="clave" name="clave" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
