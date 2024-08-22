<?php
session_start();
include('conexion.php');
require('fpdf/fpdf.php');

// Verificar si el usuario actual es un supervisor
if ($_SESSION['usuario'] != 'supervisor') {
    header('Location: login.php');
    exit();
}

// Obtener el DNI del dirigente desde la URL
if (!isset($_GET['dni']) || empty($_GET['dni'])) {
    header('Location: supervisor_dashboard.php');
    exit();
}

$dniDirigente = $_GET['dni'];

// Consultar información del dirigente
$queryDirigente = "SELECT * FROM Dirigentes WHERE DNI = '$dniDirigente'";
$resultDirigente = $conn->query($queryDirigente);
$dirigente = $resultDirigente->fetch_assoc();

// Consultar movilizadores para el dirigente, ordenados por apellido
$queryMovilizadores = "
    SELECT m.DNI, m.Nombre, m.Apellido, m.Circuito, 
           (SELECT COUNT(*) FROM Votantes WHERE MovilizadorDNI = m.DNI) AS CantidadVotantes
    FROM Movilizadores m
    WHERE m.DirigenteDNI = '$dniDirigente'
    ORDER BY m.Apellido ASC";
$resultMovilizadores = $conn->query($queryMovilizadores);

// Consultar votantes registrados por los movilizadores, ordenados por apellido
$queryVotantes = "
    SELECT v.DNI, v.Nombre, v.Apellido, v.Circuito, m.Apellido AS MovilizadorApellido, m.Nombre AS MovilizadorNombre
    FROM Votantes v
    JOIN Movilizadores m ON v.MovilizadorDNI = m.DNI
    WHERE m.DirigenteDNI = '$dniDirigente'
    ORDER BY v.MovilizadorDNI ASC";
$resultVotantes = $conn->query($queryVotantes);

// Crear instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Título del PDF
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Detalles del Dirigente', 0, 1, 'C');
$pdf->Ln(10);

// Información del Dirigente
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 10, "Dirigente: " . $dirigente['Nombre'] . ' ' . $dirigente['Apellido'], 0, 1);
$pdf->Cell(0, 10, "DNI: " . $dirigente['DNI'], 0, 1);
$pdf->Cell(0, 10, "Circuito: " . $dirigente['Circuito'], 0, 1);
$pdf->Ln(10);

// Tabla de Movilizadores
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 10, 'Movilizadores', 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(30, 10, 'DNI', 1);
$pdf->Cell(50, 10, 'Apellido', 1);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(10, 10, 'Cto', 1);
$pdf->Cell(30, 10, 'Votantes', 1);
$pdf->Ln();

$totalVotantes = 0;

if ($resultMovilizadores->num_rows > 0) {
    while ($movilizador = $resultMovilizadores->fetch_assoc()) {
        $pdf->Cell(30, 10, $movilizador['DNI'], 1);
        $pdf->Cell(50, 10, $movilizador['Apellido'], 1);
        $pdf->Cell(50, 10, $movilizador['Nombre'], 1);
        $pdf->Cell(10, 10, $movilizador['Circuito'], 1);
        $pdf->Cell(30, 10, $movilizador['CantidadVotantes'], 1);
        $pdf->Ln();

        // Sumar al total de votantes
        $totalVotantes += $movilizador['CantidadVotantes'];
    }
} else {
    $pdf->Cell(0, 10, 'No hay movilizadores registrados', 1, 1, 'C');
}

// Mostrar total de votantes
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 10, "Total de Votantes Registrados: " . $totalVotantes, 0, 1, 'R');

$pdf->Ln(10);

// Tabla de Votantes
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Votantes Registrados', 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(25, 10, 'DNI', 1);
$pdf->Cell(40, 10, 'Apellido', 1);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(10, 10, 'Cto', 1);
$pdf->Cell(50, 10, 'Movilizador', 1);
$pdf->Ln();

if ($resultVotantes->num_rows > 0) {
    while ($votante = $resultVotantes->fetch_assoc()) {
        $pdf->Cell(25, 10, $votante['DNI'], 1);
        $pdf->Cell(40, 10, $votante['Apellido'], 1);
        $pdf->Cell(50, 10, $votante['Nombre'], 1);
        $pdf->Cell(10, 10, $votante['Circuito'], 1);
        $pdf->Cell(50, 10, $votante['MovilizadorApellido'] . ' ' . $votante['MovilizadorNombre'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No hay votantes registrados', 1, 1, 'C');
}

// Salida del PDF al navegador
$pdf->Output('I', 'Detalles_Dirigente.pdf');
