<?php include 'Header.html'; ?>
<main class="main-content">
    <div class="container">
        <div class="form-header text-center mb-4">
            <h3>Tableau des intérêts mensuels</h3>
            <p>Statistiques des intérêts générés par mois</p>
        </div>
        <main class="main-content">
            <div class="container">

                <div class="row mb-4">
                    <div class="col">
                        <label for="mois_debut">Mois début :</label>
                        <input type="month" id="mois_debut" class="form-control">
                    </div>
                    <div class="col">
                        <label for="mois_fin">Mois fin :</label>
                        <input type="month" id="mois_fin" class="form-control">
                    </div>
                    <div class="col d-flex align-items-end">
                        <button class="btn btn-primary" onclick="chargerDonnees()">Filtrer</button>
                    </div>
                </div>


                <!-- Tableau stylisé -->
                <!-- Nouveau tableau détaillé -->
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const apiBase = "http://localhost:8000/ws";
            let chartInstance = null; // Variable globale pour stocker l'instance du graphique

            function regrouperParMois(data) {
                const resultat = {};
                data.forEach(entry => {
                    const mois = new Date(entry.date_mois).toLocaleDateString('fr-FR', {
                        year: 'numeric',
                        month: 'long'
                    });
                    if (!resultat[mois]) {
                        resultat[mois] = {
                            interet_total: 0,
                            nb_pret: 0
                        };
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
                        const moisDebut = document.getElementById("mois_debut").value;
                        const moisFin = document.getElementById("mois_fin").value;

                        let dateDebut = moisDebut ? new Date(moisDebut + "-01") : null;
                        let dateFin = moisFin ? new Date(moisFin + "-01") : null;
                        if (dateFin) {
                            // Ajouter un mois pour inclure tout le mois sélectionné
                            dateFin.setMonth(dateFin.getMonth() + 1);
                        }

                        // Filtrer les données selon la période choisie
                        const dataFiltrée = data.filter(item => {
                            const datePaiement = new Date(item.date_mois);
                            if (dateDebut && datePaiement < dateDebut) return false;
                            if (dateFin && datePaiement >= dateFin) return false;
                            return true;
                        });

                        // Mettre à jour le tableau
                        const tbody = document.getElementById("table-interets");
                        tbody.innerHTML = "";

                        if (dataFiltrée.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="7">Aucune donnée trouvée pour cette période</td></tr>`;
                        } else {
                            dataFiltrée.forEach(item => {
                                const ligne = `
                            <tr>
                                <td>${item.pret_id}</td>
                                <td>${item.client_id}</td>
                                <td>${new Date(item.date_mois).toLocaleDateString()}</td>
                                <td>${item.mensualite.toLocaleString()} Ar</td>
                                <td>${item.interet.toLocaleString()} Ar</td>
                                <td>${item.amortissement.toLocaleString()} Ar</td>
                                <td>${item.capital_restant.toLocaleString()} Ar</td>
                            </tr>`;
                                tbody.insertAdjacentHTML("beforeend", ligne);
                            });
                        }

                        // Regrouper les données filtrées pour le graphique
                        const groupé = regrouperParMois(dataFiltrée);
                        const ctx = document.getElementById("graph-interets").getContext("2d");

                        // Détruire l'ancien graphique s'il existe
                        if (chartInstance) {
                            chartInstance.destroy();
                        }

                        // Créer un nouveau graphique avec les données filtrées
                        chartInstance = new Chart(ctx, {
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
                                    legend: {
                                        display: true
                                    }
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
                    });
            }

            window.onload = chargerDonnees;
        </script>