<?php
session_start();
include('conexion.php');

if (isset($_POST['login'])) {
    $dni = $_POST['dni'];

    // Consultar la tabla de Administradores solo con el DNI
    $queryAdmin = "SELECT * FROM Administradores WHERE DNI = ?";
    $stmt = $conn->prepare($queryAdmin);
    $stmt->bind_param('s', $dni);
    $stmt->execute();
    $resultAdmin = $stmt->get_result();

    if ($resultAdmin->num_rows > 0) {
        $_SESSION['usuario'] = 'administrador';
        $_SESSION['dni'] = $dni;
        header('Location: admin_dashboard.php');
        exit();
    } else {
        // Si no se encontró ningún administrador con el DNI
        $error = "DNI incorrecto";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Login Administrador</h2>
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
