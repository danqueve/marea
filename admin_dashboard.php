<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un administrador
if ($_SESSION['usuario'] != 'administrador') {
    header('Location: login.php');
    exit();
}

// Obtener la lista de supervisores y la cantidad de dirigentes registrados
$querySupervisores = "SELECT S.DNI, S.Apellido, S.Nombre, COUNT(D.DNI) AS total_dirigentes
                      FROM Supervisores S
                      LEFT JOIN Dirigentes D ON S.DNI = D.SupervisorDNI
                      GROUP BY S.DNI, S.Apellido, S.Nombre";
$resultSupervisores = $conn->query($querySupervisores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Administrador Dashboard</h1>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>

    <div class="container mt-4">
        <a href="register_supervisor.php" class="btn btn-success mb-3">Registrar Supervisor</a>

        <h2>Supervisores Registrados</h2>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DNI</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Cantidad de Dirigentes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultSupervisores->num_rows > 0) {
                    $i = 1;
                    while ($row = $resultSupervisores->fetch_assoc()) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$row['DNI']}</td>
                                <td>{$row['Apellido']}</td>
                                <td>{$row['Nombre']}</td>
                                <td>{$row['total_dirigentes']}</td>
                              </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No hay supervisores registrados</td></tr>";
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
