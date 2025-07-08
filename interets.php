<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Intérêts Mensuels - Établissement Financier</title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .filters { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .filters label { margin-right: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: right; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .chart-container { margin-top: 40px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Intérêts Mensuels</h1>
    
    <div class="filters">
        <h3>Filtrer par période :</h3>
        <label>De :
            <input type="month" id="date-debut" value="<?= date('Y-m', strtotime('-1 year')) ?>">
        </label>
        <label>À :
            <input type="month" id="date-fin" value="<?= date('Y-m') ?>">
        </label>
        <button id="btn-filtrer">Appliquer</button>
        <span id="error-msg" class="error"></span>
    </div>
    
    <div id="results">
        <h2>Résultats</h2>
        <div id="table-container"></div>
        <div class="chart-container">
            <canvas id="chartInterets"></canvas>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        let chart = null;

        loadData();

        $('#btn-filtrer').click(function() {
            loadData();
        });

        function loadData() {
            const debut = $('#date-debut').val();
            const fin = $('#date-fin').val();

            if (debut > fin) {
                $('#error-msg').text('La date de début doit être antérieure ou égale à la date de fin.');
                return;
            }

            $('#error-msg').text('');

            $.ajax({
                url: 'ws/mensuels',
                data: { debut, fin },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderTable(response.data);
                        renderChart(response.data);
                    } else {
                        $('#error-msg').text('Aucune donnée trouvée.');
                    }
                },
                error: function() {
                    $('#error-msg').text('Erreur lors du chargement des données.');
                }
            });
        }

        function renderTable(data) {
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th style="text-align:left;">Mois/Année</th>
                            <th>Intérêts (Ar)</th>
                            <th>Nombre de prêts</th>
                        </tr>
                    </thead>
                    <tbody>`;

            let totalInterets = 0;
            let totalPrets = 0;

            data.forEach(row => {
                const moisFormat = formatDate(row.mois_annee);
                const interet = parseFloat(row.interets_mensuels);
                const nombre = parseInt(row.nombre_prets);

                totalInterets += interet;
                totalPrets += nombre;

                html += `
                    <tr>
                        <td style="text-align:left;">${moisFormat}</td>
                        <td>${formatNumber(interet)}</td>
                        <td>${nombre}</td>
                    </tr>`;
            });

            html += `
                    <tr style="font-weight:bold;">
                        <td style="text-align:left;">TOTAL</td>
                        <td>${formatNumber(totalInterets)}</td>
                        <td>${totalPrets}</td>
                    </tr>
                </tbody>
                </table>`;

            $('#table-container').html(html);
        }

        function renderChart(data) {
            const ctx = document.getElementById('chartInterets').getContext('2d');

            // Détruire le graphique précédent
            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(row => formatDate(row.mois_annee)),
                    datasets: [{
                        label: 'Intérêts mensuels (Ar)',
                        data: data.map(row => row.interets_mensuels),
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Montant (Ariary)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mois/Année'
                            }
                        }
                    }
                }
            });
        }

        function formatDate(monthYear) {
            const [year, month] = monthYear.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }
    });
    </script>
</body>
</html>
