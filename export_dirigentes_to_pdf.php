<?php
require('fpdf/fpdf.php');
include('conexion.php');

// Consultar todos los dirigentes
$queryDirigentes = "
    SELECT d.DNI, d.Nombre, d.Apellido, d.Circuito,
           (SELECT COUNT(*) FROM Movilizadores WHERE DirigenteDNI = d.DNI) AS CantidadMovilizadores,
           (SELECT COUNT(*) FROM Votantes v 
             JOIN Movilizadores m ON v.MovilizadorDNI = m.DNI 
             WHERE m.DirigenteDNI = d.DNI) AS CantidadVotantes
    FROM Dirigentes d
";
$resultDirigentes = $conn->query($queryDirigentes);

// Crear el PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Listado de Dirigentes', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 7, 'DNI', 1);
        $this->Cell(40, 7, 'Nombre', 1);
        $this->Cell(40, 7, 'Apellido', 1);
        $this->Cell(10, 7, 'Cto', 1);
        $this->Cell(30, 7, 'Movilizadores', 1);
        $this->Cell(35, 7, 'Votantes', 1);
        $this->Ln();
    }

    function TableBody($data)
    {
        $this->SetFont('Arial', '', 10);
        $totalMovilizadores = 0; // Variable para contar el total de movilizadores
        $totalVotantes = 0; // Variable para contar el total de votantes
        foreach ($data as $row) {
            $this->Cell(30, 6, $row['DNI'], 1);
            $this->Cell(40, 6, $row['Nombre'], 1);
            $this->Cell(40, 6, $row['Apellido'], 1);
            $this->Cell(10, 6, $row['Circuito'], 1);
            $this->Cell(30, 6, $row['CantidadMovilizadores'], 1);
            $this->Cell(35, 6, $row['CantidadVotantes'], 1);
            $this->Ln();
            $totalMovilizadores += $row['CantidadMovilizadores']; // Sumar la cantidad de movilizadores
            $totalVotantes += $row['CantidadVotantes']; // Sumar la cantidad de votantes
        }
        // Mostrar el total de movilizadores y votantes
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(120, 7, 'Total de Movilizadores', 1);
        $this->Cell(30, 7, $totalMovilizadores, 1);
        $this->Ln();
        $this->Cell(150, 7, 'Total de Votantes', 1);
        $this->Cell(35, 7, $totalVotantes, 1);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->TableHeader();

$dirigentes = [];
if ($resultDirigentes->num_rows > 0) {
    while ($row = $resultDirigentes->fetch_assoc()) {
        $dirigentes[] = $row;
    }
}

$pdf->TableBody($dirigentes);
$pdf->Output();
?>
