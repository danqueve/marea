<?php
session_start();
include('conexion.php');

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
    $apodo = $_POST['apodo'];
    $celular = $_POST['celular'];
    $horaRegistro = date('Y-m-d H:i:s');

    // Verificación DNI existente
    $queryCheck = "SELECT * FROM Dirigentes WHERE DNI = ?";
    $stmtCheck = $conn->prepare($queryCheck);
    $stmtCheck->bind_param("s", $dni);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        $error = "El DNI ya está registrado como dirigente.";
    } else {
        // Inserción con nuevos campos
        $queryInsert = "INSERT INTO Dirigentes (DNI, Apellido, Nombre, Circuito, Apodo, Celular, HoraRegistro, SupervisorDNI, Clave) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bind_param("sssssssss", $dni, $apellido, $nombre, $circuito, $apodo, $celular, $horaRegistro, $dniSupervisor, $dni);
        
        if ($stmtInsert->execute()) {
            header('Location: supervisor_dashboard.php');
            exit();
        } else {
            $error = "Error al registrar el dirigente: " . $conn->error;
        }
        $stmtInsert->close();
    }
    $stmtCheck->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Dirigente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<header class="bg-primary text-white text-center p-3">
    <h1>Supervisor Dashboard</h1>
    <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
</header>
<div class="container">
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
                <div class="input-group">
                    <input type="text" class="form-control" id="dni" name="dni" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary" onclick="buscarDNI()">Buscar en Padrón</button>
                    </div>
                </div>
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
            <div class="form-group">
    <label for="apodo">Apodo (Opcional)</label>
    <input type="text" class="form-control" id="apodo" name="apodo">
</div>
<div class="form-group">
    <label for="celular">Celular</label>
    <input type="tel" class="form-control" id="celular" name="celular" required>
</div>
            <button type="submit" class="btn btn-success">Registrar Dirigente</button>
        </form>
    </div>
</div>

<script>
function buscarDNI() {
    const dni = $('#dni').val();
    if (!dni) {
        alert('Por favor ingrese un DNI');
        return;
    }

    $.ajax({
        url: 'buscar_dni.php',
        method: 'GET',
        data: { dni: dni },
        dataType: 'json',
        success: function(response) {
            if (response.encontrado) {
                $('#apellido').val(response.apellido);
                $('#nombre').val(response.nombre);
                $('#circuito').val(response.circuito);
            } else {
                alert('DNI no encontrado en el padrón');
                $('#apellido').val('');
                $('#nombre').val('');
                $('#circuito').val('');
            }
        },
        error: function() {
            alert('Error en la búsqueda');
        }
    });
}
</script>
</body>
</html>