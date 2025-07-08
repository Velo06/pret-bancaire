<!-- statistiques_bars.html -->
<!-- tableau.html -->
<?php include 'Header.php'; ?>
<main class="main-content">
    <div class="container">

        <div class="form-container">
            <div class="form-header">
                <h3>Statistiques des prêts par type</h3>
                <p>Nombre de prêts octroyés par catégorie</p>
            </div>

            <div style="padding: 1rem;">
                <canvas id="statistiquesPretChart" height="150"></canvas>
            </div>

            <div class="form-actions">
                <button class="btn btn-outline-secondary" onclick="resetGraph()">Annuler</button>
            </div>
        </div>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const ctx = document.getElementById('statistiquesPretChart').getContext('2d');
            const statistiquesPretChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Personnel', 'Auto', 'Immobilier', 'Étudiant', 'Business'],
                    datasets: [{
                        label: 'Nombre de prêts',
                        data: [12, 19, 7, 5, 9],
                        backgroundColor: [
                            '#88c417', '#70a516', '#578215', '#4b7010', '#2e4d0a'
                        ],
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#223060',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5
                            },
                            grid: {
                                color: '#e4e4e4'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            function resetGraph() {
                statistiquesPretChart.destroy();
                alert("Graphique supprimé.");
            }
        </script>
    </div>
</main>