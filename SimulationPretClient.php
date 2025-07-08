<?php include 'header.html'; ?>
<main class="main-content">
    <div class="container mt-5">
        <h3 class="mb-4">Simulation de prêt</h3>
        <!-- Formulaire -->
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="montant">Montant à emprunter (Ar)</label>
                <input type="number" class="form-control-custom" id="montant" placeholder="ex: 1 200 000" />
            </div>
            <div class="form-group col-md-6">
                <label for="duree">Durée (en mois)</label>
                <input type="number" class="form-control-custom" id="duree" placeholder="ex: 12" />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="taux">Taux d'intérêt annuel (%)</label>
                <input type="number" class="form-control-custom" id="taux" step="0.01" placeholder="ex: 10" />
            </div>
            <div class="form-group col-md-6">
                <label for="assurance">Assurance (% du capital)</label>
                <input type="number" class="form-control-custom" id="assurance" step="0.01" placeholder="ex: 1.2" />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="date_debut">Date de début du prêt</label>
                <input type="date" class="form-control-custom" id="date_debut" />
            </div>
        </div>

        <!-- 🎯 FILTRE DE DATE -->
        <div class="form-row form-filtre align-center mt-4">
            <div class="form-group">
                <label for="filtre_debut">Mois/Année Début</label>
                <input type="month" class="form-control-custom" id="filtre_debut">
            </div>

            <div class="form-group">
                <label for="filtre_fin">Mois/Année Fin</label>
                <input type="month" class="form-control-custom" id="filtre_fin">
            </div>

            <div class="form-group filtre-btn">
                <button class="btn-primary-custom" onclick="filtrerResultat()">Filtrée</button>
            </div>
        </div>

        <div id="resultat-simulation" class="mt-5"></div>

        <div class="mt-5">
            <canvas id="graph-interets" height="100"></canvas>
        </div>

        <button class="btn btn-primary mt-4" onclick="simulerPret()">⚙️ Simuler le prêt</button>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartSimu = null;
        let simulationData = null;

        function simulerPret() {
            const montant = parseFloat(document.getElementById('montant').value);
            const duree = parseInt(document.getElementById('duree').value);
            const taux = parseFloat(document.getElementById('taux').value);
            const assurance = parseFloat(document.getElementById('assurance').value) || 0;
            const dateDebut = document.getElementById('date_debut').value;

            if (!montant || !duree || !taux || !dateDebut) {
                alert("Veuillez remplir tous les champs !");
                return;
            }

            fetch("http://localhost:8000/ws/simuler_pret", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        montant: montant,
                        duree_mois: duree,
                        taux_annuel: taux,
                        date_debut: dateDebut,
                        assurance: assurance
                    })
                })
                .then(res => res.json())
                .then(data => {
                    simulationData = data; // sauvegarde pour enregistrement
                    const conteneur = document.getElementById("resultat-simulation");

                    conteneur.innerHTML = `
                <h5>Résumé</h5>
                <p><strong>Mensualité :</strong> ${data.mensualite.toLocaleString()} Ar</p>
                <p><strong>Assurance mensuelle :</strong> ${data.assurance_mensuelle.toLocaleString()} Ar</p>
                <p><strong>Total intérêts :</strong> ${data.total_interet.toLocaleString()} Ar</p>
                <p><strong>Total assurances :</strong> ${data.total_assurance.toLocaleString()} Ar</p>
                <p><strong>Montant total remboursé :</strong> ${data.total_rembourse.toLocaleString()} Ar</p>

                <button class="btn btn-success mt-4" onclick='enregistrer_pret_simuler(simulationData)'>
                    💾 Enregistrer cette simulation
                </button>

                <h6 class="mt-4">Détails mensuels</h6>
                <div class="table-responsive">
                    <table class="table custom-table">
                    <thead>
                        <tr>
                        <th>Mois</th>
                        <th>Date</th>
                        <th>Mensualité</th>
                        <th>Assurance</th>
                        <th>Total Paiement</th>
                        <th>Intérêt</th>
                        <th>Amortissement</th>
                        <th>Capital restant</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.details.map((item, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${new Date(item.date_mois).toLocaleDateString()}</td>
                            <td>${item.mensualite.toLocaleString()} Ar</td>
                            <td>${item.assurance.toLocaleString()} Ar</td>
                            <td>${item.total_paiement.toLocaleString()} Ar</td>
                            <td>${item.interet.toLocaleString()} Ar</td>
                            <td>${item.amortissement.toLocaleString()} Ar</td>
                            <td>${item.capital_restant.toLocaleString()} Ar</td>
                        </tr>
                        `).join('')}
                    </tbody>
                    </table>
                </div>
            `;

                    // Graphique
                    const ctx = document.getElementById("graph-interets").getContext("2d");
                    const labels = data.details.map(item => new Date(item.date_mois).toLocaleDateString());
                    const interets = data.details.map(item => item.interet);
                    const amortissements = data.details.map(item => item.amortissement);
                    const assurances = data.details.map(item => item.assurance);

                    if (chartSimu) chartSimu.destroy();

                    chartSimu = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: "Intérêt",
                                    data: interets,
                                    borderColor: "#e74c3c",
                                    backgroundColor: "rgba(231, 76, 60, 0.1)",
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: "Amortissement",
                                    data: amortissements,
                                    borderColor: "#3498db",
                                    backgroundColor: "rgba(52, 152, 219, 0.1)",
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: "Assurance",
                                    data: assurances,
                                    borderColor: "#f1c40f",
                                    backgroundColor: "rgba(241, 196, 15, 0.1)",
                                    tension: 0.4,
                                    fill: true
                                }
                            ]
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
                                        callback: value => value.toLocaleString() + " Ar"
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(err => {
                    console.error("Erreur simulation :", err);
                    alert("Erreur lors de la simulation");
                });
        }

        function enregistrer_pret_simuler(data) {
            const montant = parseFloat(document.getElementById('montant').value);
            const dateDebut = document.getElementById('date_debut').value;

            fetch("http://localhost:8000/ws/prets_comparaison", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        client: 1, // remplace par l'ID réel du client connecté
                        type_pret_id: 1, // à adapter
                        montant_emprunt: montant,
                        date_debut: dateDebut,
                        date_fin: data.details.at(-1)?.date_mois,
                        id_etat_validation: 1,
                        mensualite: data.mensualite,
                        assurance_mensuelle: data.assurance_mensuelle,
                        total_interets: data.total_interet,
                        total_assurances: data.total_assurance,
                        montant_total_rembourse: data.total_rembourse
                    })
                })
                .then(res => res.json())
                .then(response => {
                    console.log("✅ Simulation enregistrée :", response);
                    alert("Simulation enregistrée avec succès !");
                })
                .catch(err => {
                    console.error("❌ Erreur d'enregistrement :", err);
                    alert("Erreur lors de l'enregistrement !");
                });
        }

        function filtrerResultat() {
            const debut = document.getElementById("filtre_debut").value;
            const fin = document.getElementById("filtre_fin").value;

            if (!debut && !fin) {
                simulerPret(); // recharge tout
                return;
            }

            const table = document.querySelector("#resultat-simulation tbody");
            const rows = table.querySelectorAll("tr");
            const newLabels = [];
            const newInterets = [];
            const newAmorts = [];
            const newAssurances = [];

            rows.forEach(row => {
                const dateStr = row.children[1].innerText;
                const date = new Date(dateStr);
                const moisActuel = date.toISOString().slice(0, 7); // YYYY-MM

                const isInRange = (!debut || moisActuel >= debut) && (!fin || moisActuel <= fin);
                row.style.display = isInRange ? "" : "none";

                if (isInRange) {
                    newLabels.push(dateStr);
                    newInterets.push(parseFloat(row.children[5].innerText.replace(/[^0-9]/g, "")));
                    newAmorts.push(parseFloat(row.children[6].innerText.replace(/[^0-9]/g, "")));
                    newAssurances.push(parseFloat(row.children[3].innerText.replace(/[^0-9]/g, "")));
                }
            });

            if (chartSimu) {
                chartSimu.data.labels = newLabels;
                chartSimu.data.datasets[0].data = newInterets;
                chartSimu.data.datasets[1].data = newAmorts;
                chartSimu.data.datasets[2].data = newAssurances;
                chartSimu.update();
            }
        }
    </script>

</main>