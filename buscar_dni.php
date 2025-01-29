<?php
include('conexion.php');

$response = ['encontrado' => false];

if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];
    
    $query = "SELECT apellido, nombre, circuito FROM padrones3 WHERE dni = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            'encontrado' => true,
            'apellido' => $row['apellido'],
            'nombre' => $row['nombre'],
            'circuito' => $row['circuito']
        ];
    }
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>