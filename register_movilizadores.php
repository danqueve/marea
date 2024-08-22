<?php
session_start();
include('conexion.php');

// Verificar si el usuario actual es un dirigente
if ($_SESSION['usuario'] != 'dirigente') {
    header('Location: login.php');
    exit();
}

$dniDirigente = $_SESSION['dni'];
$error = '';
$success = '';
$movilizadorData = null;

// Manejo del formulario de búsqueda
if (isset($_POST['search'])) {
    $dniMovilizador = $_POST['dni'];

    // Buscar en PADRON
    $queryPadron = "SELECT * FROM PADRON WHERE DNI = '$dniMovilizador'";
    $resultPadron = $conn->query($queryPadron);
    
    if ($resultPadron->num_rows > 0) {
        $movilizadorData = $resultPadron->fetch_assoc();
    } else {
        $error = 'El DNI no está registrado en PADRON.';
    }
}

// Manejo del formulario de registro
if (isset($_POST['register'])) {
    $dniMovilizador = $_POST['dni'];
    $nombreMovilizador = $_POST['nombre'];
    $apellidoMovilizador = $_POST['apellido'];
    $circuitoMovilizador = $_POST['circuito'];
    
    // Verificar si el DNI ya está registrado en PADRON
    $queryPadron = "SELECT * FROM PADRON WHERE DNI = '$dniMovilizador'";
    $resultPadron = $conn->query($queryPadron);
    
    if ($resultPadron->num_rows > 0) {
        // Verificar si el movilizador ya está registrado
        $queryMovilizador = "SELECT * FROM Movilizadores WHERE DNI = '$dniMovilizador'";
        $resultMovilizador = $conn->query($queryMovilizador);

        if ($resultMovilizador->num_rows == 0) {
            // Insertar el nuevo movilizador con la clave igual al DNI
            $fechaHoraRegistro = date('Y-m-d H:i:s');
            $insertMovilizador = "INSERT INTO Movilizadores (DNI, Nombre, Apellido, Circuito, FechaHoraRegistro, DirigenteDNI, Clave) 
                                  VALUES ('$dniMovilizador', '$nombreMovilizador', '$apellidoMovilizador', '$circuitoMovilizador', '$fechaHoraRegistro', '$dniDirigente', '$dniMovilizador')";
            if ($conn->query($insertMovilizador) === TRUE) {
                $success = 'Movilizador registrado con éxito.';
                $movilizadorData = null; // Limpiar los datos de movilizador después de registrar
            } else {
                $error = 'Error al registrar movilizador: ' . $conn->error;
            }
        } else {
            $error = 'El DNI ya está registrado como movilizador.';
        }
    } else {
        $error = 'El DNI no está registrado en PADRON.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Movilizador</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Registrar Movilizador</h1>
        <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
    </header>

    <div class="container mt-4">
        <a href="dirigente_dashboard.php" class="btn btn-secondary mb-3">Volver al Dashboard</a>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de búsqueda -->
        <form method="post" action="">
            <div class="form-group">
                <label for="dni">Buscar DNI en PADRON</label>
                <input type="text" class="form-control" id="dni" name="dni" required>
            </div>
            <button type="submit" name="search" class="btn btn-primary">Buscar</button>
        </form>

        <?php if ($movilizadorData): ?>
            <!-- Formulario de registro de movilizador -->
            <form method="post" action="" class="mt-4">
                <input type="hidden" name="dni" value="<?php echo $movilizadorData['DNI']; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $movilizadorData['Nombre']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $movilizadorData['Apellido']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="circuito">Circuito</label>
                    <input type="text" class="form-control" id="circuito" name="circuito" value="<?php echo $movilizadorData['Circuito']; ?>" required>
                </div>
                <button type="submit" name="register" class="btn btn-success">Registrar Movilizador</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
