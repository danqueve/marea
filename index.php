<?php
session_start();
include('conexion.php');

if (isset($_POST['login'])) {
    $dni = $_POST['dni'];
    $password = $_POST['password'];

    // Inicializar variable para el resultado
    $stmtAdmin = $stmtSupervisor = $stmtDirigente = $stmtMovilizador = null;

    // Consultar la tabla de Administradores
    $queryAdmin = "SELECT * FROM Administradores WHERE DNI = ? AND Clave = ?";
    if ($stmtAdmin = $conn->prepare($queryAdmin)) {
        $stmtAdmin->bind_param('ss', $dni, $password);
        $stmtAdmin->execute();
        $resultAdmin = $stmtAdmin->get_result();

        if ($resultAdmin->num_rows > 0) {
            $_SESSION['usuario'] = 'administrador';
            $_SESSION['dni'] = $dni;
            header('Location: admin_dashboard.php');
            exit();
        }
    } else {
        die("Error preparando la consulta de Administradores: " . $conn->error);
    }

    // Consultar la tabla de Supervisores
    $querySupervisor = "SELECT * FROM Supervisores WHERE DNI = ? AND Clave = ?";
    if ($stmtSupervisor = $conn->prepare($querySupervisor)) {
        $stmtSupervisor->bind_param('ss', $dni, $password);
        $stmtSupervisor->execute();
        $resultSupervisor = $stmtSupervisor->get_result();

        if ($resultSupervisor->num_rows > 0) {
            $_SESSION['usuario'] = 'supervisor';
            $_SESSION['dni'] = $dni;
            header('Location: supervisor_dashboard.php');
            exit();
        }
    } else {
        die("Error preparando la consulta de Supervisores: " . $conn->error);
    }

    // Consultar la tabla de Dirigentes
    $queryDirigente = "SELECT * FROM Dirigentes WHERE DNI = ? AND Clave = ?";
    if ($stmtDirigente = $conn->prepare($queryDirigente)) {
        $stmtDirigente->bind_param('ss', $dni, $password);
        $stmtDirigente->execute();
        $resultDirigente = $stmtDirigente->get_result();

        if ($resultDirigente->num_rows > 0) {
            $_SESSION['usuario'] = 'dirigente';
            $_SESSION['dni'] = $dni;
            header('Location: dirigente_dashboard.php');
            exit();
        }
    } else {
        die("Error preparando la consulta de Dirigentes: " . $conn->error);
    }

    // Consultar la tabla de Movilizadores
    $queryMovilizador = "SELECT * FROM Movilizadores WHERE DNI = ? AND Clave = ?";
    if ($stmtMovilizador = $conn->prepare($queryMovilizador)) {
        $stmtMovilizador->bind_param('ss', $dni, $password);
        $stmtMovilizador->execute();
        $resultMovilizador = $stmtMovilizador->get_result();

        if ($resultMovilizador->num_rows > 0) {
            $_SESSION['usuario'] = 'movilizador';
            $_SESSION['dni'] = $dni;
            header('Location: movilizador_dashboard.php');
            exit();
        }
    } else {
        die("Error preparando la consulta de Movilizadores: " . $conn->error);
    }

    // Si no se encontró ningún usuario
    $error = "DNI o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Login</h2>
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
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
