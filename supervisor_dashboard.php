<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un supervisor
if ($_SESSION['usuario'] != 'supervisor') {
    header('Location: login.php');
    exit();
}

// Obtener información del supervisor actual
$dniSupervisor = $_SESSION['dni'];

// Consultar los dirigentes registrados por el supervisor
$queryDirigentes = "SELECT * FROM Dirigentes ";
$resultDirigentes = $conn->query($queryDirigentes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supervisor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Supervisor Dashboard</h1>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>
    <div class="container">
    <div class="table-responsive">
    <div class="container mt-4">
    <a href="register_dirigente.php" class="btn btn-danger mb-3">Nuevos Dirigentes</a>
        <a href="dirigentes_list.php" class="btn btn-primary mb-3">Ver Dirigentes</a>
    </div>
    <br>
    <div class="container">
        <h2>Dirigentes Registrados</h2>
        
        <table class="table table-bordered table-hover table-striped">
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
                if ($resultDirigentes->num_rows > 0) {
                    $i = 1;
                    while ($row = $resultDirigentes->fetch_assoc()) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$row['DNI']}</td>
                                <td>{$row['Apellido']}</td>
                                <td>{$row['Nombre']}</td>
                                <td>{$row['Circuito']}</td>
                                <td><a href='dirigente_details.php?dni={$row['DNI']}' class='btn btn-info'>Ver Detalles</a></td>
                              </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No hay dirigentes registrados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </div>
</body>
</html>
