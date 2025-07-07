<?php include 'header.html'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Statistiques des intérêts</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style/style.css">
    <style>
        .main-content {
            padding: 40px 20px;
        }
        .form-header {
            margin-bottom: 30px;
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }
        .custom-table th,
        .custom-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .custom-table th {
            background-color: #f4f4f4;
        }
        canvas {
            margin-top: 40px;
        }
    </style>
</head>

<body>
<main class="main-content">
    <div class="container">
        <div class="form-header text-center">
            <h3>Tableau des intérêts mensuels</h3>
            <p>Statistiques des intérêts générés par mois</p>
        </div>

        <!-- Tableau stylisé -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Mois</th>
                            <th>Intérêt total (Ar)</th>
                            <th>Nombre de prêts</th>
                        </tr>
                    </thead>
                    <tbody id="table-interets">
                        <tr><td colspan="3">Chargement en cours...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Graphique -->
        <div class="mt-4 p-4">
            <canvas id="graph-interets" height="100"></canvas>
        </div>

        <!-- Bouton retour -->
        <div class="form-actions mt-4">
            <button class="btn btn-outline-secondary" onclick="window.history.back()">← Retour</button>
        </div>
    </div>
</main>

<script>
    const apiBase = "http://localhost:8000/ws";

    function regrouperParMois(data) {
        const resultat = {};
        data.forEach(entry => {
            const mois = new Date(entry.date_mois).toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long'
            });
            if (!resultat[mois]) {
                resultat[mois] = { interet_total: 0, nb_pret: 0 };
            }
            resultat[mois].interet_total += entry.interet;
            resultat[mois].nb_pret += 1;
        });
        return resultat;
    }

    function chargerDonnees() {
        fetch(apiBase + "/interet_EF")
            .then(res => res.json())
            .then(data => {
                const groupé = regrouperParMois(data);
                const tbody = document.getElementById("table-interets");
                tbody.innerHTML = "";

                Object.keys(groupé).forEach(mois => {
                    const ligne = `
                        <tr>
                            <td>${mois}</td>
                            <td>${groupé[mois].interet_total.toLocaleString()} Ar</td>
                            <td>${groupé[mois].nb_pret}</td>
                        </tr>`;
                    tbody.insertAdjacentHTML("beforeend", ligne);
                });

                const ctx = document.getElementById("graph-interets").getContext("2d");
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(groupé),
                        datasets: [{
                            label: 'Intérêts mensuels (Ar)',
                            data: Object.values(groupé).map(val => val.interet_total),
                            fill: true,
                            borderColor: '#88c417',
                            backgroundColor: 'rgba(136, 196, 23, 0.15)',
                            tension: 0.4,
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => value.toLocaleString() + ' Ar'
                                }
                            }
                        }
                    }
                });
            })
            .catch(err => {
                console.error("Erreur de chargement :", err);
                document.getElementById("table-interets").innerHTML = `<tr><td colspan="3">Erreur de chargement</td></tr>`;
            });
    }

    window.onload = chargerDonnees;
</script>
</body>
</html>
