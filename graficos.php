<?php
session_start();
include('conexion.php');

// Verificar sesión activa
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Consultar la cantidad de votantes por circuito
$queryVotantesPorCircuito = "SELECT Circuito, COUNT(*) AS Total FROM Votantes GROUP BY Circuito";
$resultVotantesPorCircuito = $conn->query($queryVotantesPorCircuito);

$circuitos = [];
$votantesPorCircuito = [];
$totalVotantes = 0;

while ($row = $resultVotantesPorCircuito->fetch_assoc()) {
    $circuitos[] = $row['Circuito'];
    $votantesPorCircuito[] = $row['Total'];
    $totalVotantes += $row['Total'];
}

// Consultar la cantidad total de movilizadores y dirigentes
$queryMovDir = "
    SELECT 
        (SELECT COUNT(*) FROM Movilizadores) AS TotalMovilizadores,
        (SELECT COUNT(*) FROM Dirigentes) AS TotalDirigentes
";
$resultMovDir = $conn->query($queryMovDir);
$dataMovDir = $resultMovDir->fetch_assoc();

$totalMovilizadores = $dataMovDir['TotalMovilizadores'];
$totalDirigentes = $dataMovDir['TotalDirigentes'];
$totalMovDir = $totalMovilizadores + $totalDirigentes;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Gráficos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Dashboard de Gráficos</h1>

        <div class="row mt-5">
            <!-- Gráfico de Distribución Total de Votantes por Circuitos -->
            <div class="col-md-6">
                <h3 class="text-center">Distribución Total de Votantes por Circuitos</h3>
                <canvas id="votantesCircuitosChart"></canvas>
                <p class="text-center mt-3">
                    <strong>Total Votantes: <?= $totalVotantes ?></strong>
                </p>
            </div>

            <!-- Gráfico de Movilizadores y Dirigentes -->
            <div class="col-md-6">
                <h3 class="text-center">Movilizadores y Dirigentes</h3>
                <canvas id="movDirChart"></canvas>
                <p class="text-center mt-3">
                    <strong>Total Movilizadores: <?= $totalMovilizadores ?></strong><br>
                    <strong>Total Dirigentes: <?= $totalDirigentes ?></strong><br>
                    <strong>Total General: <?= $totalMovDir ?></strong>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Datos para el gráfico de votantes por circuito
        const circuitos = <?= json_encode($circuitos) ?>;
        const votantesPorCircuito = <?= json_encode($votantesPorCircuito) ?>;
        const totalVotantes = <?= $totalVotantes ?>;

        // Gráfico de Distribución Total de Votantes por Circuitos
        const votantesCircuitosCtx = document.getElementById('votantesCircuitosChart').getContext('2d');
        new Chart(votantesCircuitosCtx, {
            type: 'doughnut',
            data: {
                labels: circuitos,
                datasets: [{
                    data: votantesPorCircuito,
                    backgroundColor: [
                        '#e74c3c', '#3498db', '#2ecc71', '#f1c40f', '#9b59b6', '#e67e22', '#1abc9c', '#34495e'
                    ],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percentage = ((value / totalVotantes) * 100).toFixed(2);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Datos para el gráfico de Movilizadores y Dirigentes
        const movDirCtx = document.getElementById('movDirChart').getContext('2d');
        new Chart(movDirCtx, {
            type: 'doughnut',
            data: {
                labels: ['Movilizadores', 'Dirigentes'],
                datasets: [{
                    data: [<?= $totalMovilizadores ?>, <?= $totalDirigentes ?>],
                    backgroundColor: ['#3498db', '#2ecc71'],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
