<?php
session_start();
include('conexion.php');

$dniDirigente = $_SESSION['dni'];

// Obtener la lista de movilizadores registrados por el dirigente
$query = "SELECT * FROM Movilizadores WHERE DirigenteDNI = '$dniDirigente'";
$movilizadores = $conn->query($query);

while ($row = $movilizadores->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['DNI'] . "</td>";
    echo "<td>" . $row['Apellido'] . "</td>";
    echo "<td>" . $row['Nombre'] . "</td>";
    echo "<td>" . $row['Circuito'] . "</td>";
    echo "<td>" . $row['FechaHoraRegistro'] . "</td>";
    echo "</tr>";
}
?>
