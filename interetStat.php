<?php 
    $activePage = 'interets';
    include 'Header.php'; 
?>
<main class="main-content">
    <div class="container">
        <div class="form-header text-center mb-4">
            <h3>Tableau des intérêts mensuels</h3>
            <p>Statistiques des intérêts générés par période</p>
        </div>

        <div class="form-row form-filtre align-center">
            <div class="form-group">
                <label for="mois_debut">Date début :</label>
                <input type="date" id="mois_debut" class="form-control-custom" />
            </div>
            <div class="form-group">
                <label for="mois_fin">Date fin :</label>
                <input type="date" id="mois_fin" class="form-control-custom" />
            </div>
            <div class="form-group filtre-btn">
                <button class="btn-primary-custom" onclick="chargerDonnees()">Filtrer</button>
            </div>
        </div>



        <!-- Tableau des résultats -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID Prêt</th>
                            <th>ID Client</th>
                            <th>Date</th>
                            <th>Mensualité</th>
                            <th>Intérêt</th>
                            <th>Amortissement</th>
                            <th>Capital Restant</th>
                        </tr>
                    </thead>
                    <tbody id="table-interets">
                        <tr>
                            <td colspan="7">Chargement en cours...</td>
                        </tr>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const apiBase = "http://localhost:8000/ws";
    let chartInstance = null;

    function regrouperParMois(data) {
        const resultat = {};
        data.forEach(entry => {
            const date = new Date(entry.date_mois);
            const cle = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}`;
            if (!resultat[cle]) {
                resultat[cle] = {
                    interet_total: 0,
                    nb_pret: 0
                };
            }
            resultat[cle].interet_total += entry.interet;
            resultat[cle].nb_pret += 1;
        });
        return resultat;
    }

    function chargerDonnees() {
        fetch(apiBase + "/interet_EF")
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById("table-interets");
                tbody.innerHTML = "";

                const debutVal = document.getElementById("mois_debut").value;
                const finVal = document.getElementById("mois_fin").value;

                const dateDebut = debutVal ? new Date(debutVal) : null;
                const dateFin = finVal ? new Date(finVal) : null;

                if (dateDebut && dateFin && dateDebut > dateFin) {
                    alert("La date de début doit être antérieure à la date de fin.");
                    return;
                }

                const dataFiltrée = data.filter(item => {
                    const datePaiement = new Date(item.date_mois);
                    return (!dateDebut || datePaiement >= dateDebut) &&
                        (!dateFin || datePaiement <= dateFin);
                });

                if (dataFiltrée.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7">Aucune donnée trouvée pour cette période</td></tr>`;
                } else {
                    dataFiltrée.forEach(item => {
                        tbody.insertAdjacentHTML("beforeend", `
                            <tr>
                                <td>${item.pret_id}</td>
                                <td>${item.client_id}</td>
                                <td>${new Date(item.date_mois).toLocaleDateString()}</td>
                                <td>${item.mensualite.toLocaleString()} Ar</td>
                                <td>${item.interet.toLocaleString()} Ar</td>
                                <td>${item.amortissement.toLocaleString()} Ar</td>
                                <td>${item.capital_restant.toLocaleString()} Ar</td>
                            </tr>
                        `);
                    });
                }

                // Mise à jour du graphique
                const groupé = regrouperParMois(dataFiltrée);
                const ctx = document.getElementById("graph-interets").getContext("2d");

                if (chartInstance) chartInstance.destroy();

                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(groupé),
                        datasets: [{
                            label: 'Intérêts mensuels (Ar)',
                            data: Object.values(groupé).map(v => v.interet_total),
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
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: val => val.toLocaleString() + ' Ar'
                                }
                            }
                        }
                    }
                });
            });
    }

    window.onload = chargerDonnees;
</script>