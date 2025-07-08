<?php include 'Header.php'; ?>
<main class="main-content">
  <!-- formulaire_pret_template.html -->
  <div class="form-container">
    <div class="form-header">
      <h3>Simulation de pret</h3>
    </div>

    <div id="pret-modal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="fermerModal()">&times;</span>
        <div class="form-container">
          <div class="form-header">
            <h3>Créer ou modifier un prêt</h3>
            <p>Veuillez remplir les informations nécessaires ci-dessous</p>
          </div>
          <!-- Creation pret simulé -->
          <form onsubmit="event.preventDefault(); ajouterPret();">
            <!-- Champ caché pour modification -->
            <div class="form-row">
              <div class="form-group">
                <label for="clientId">ID Client *</label>
                <input type="number" id="clientId" class="form-control-custom" placeholder="Ex: 101" required />
              </div>

            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="type_pret_id">Type de prêt *</label>
                <select id="type_pret_id" class="form-control-custom" required>
                  <option value="">-- Choisir un type de prêt --</option>
                </select>
              </div>
              <div class="form-group">
                <label for="montant_emprunt">Montant emprunté *</label>
                <input type="number" id="montant_emprunt" class="form-control-custom" placeholder="Ex: 200000" required />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="date_debut">Date de début *</label>
                <input type="date" id="date_debut" class="form-control-custom" required />
              </div>
              <div class="form-group">
                <label for="date_fin">Date de fin *</label>
                <input type="date" id="date_fin" class="form-control-custom" required />
              </div>
            </div>

            <div class="form-actions">
              <button type="reset" class="btn btn-outline-secondary">Annuler</button>
              <button type="submit" class="btn-primary-custom">Ajouter / Modifier</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Liste des emprunts -->
  <div class="table-container">
    <div class="table-header">
      <h3>Liste des prêts simulés</h3>
      <!-- <button class="btn-primary-custom" onclick="resetForcm()">Nouveau Prêt</button> -->
      <button class="btn-primary-custom" id="boutton-ajout-pret" onclick="ouvrirModal()">Nouveau Prêt</button>

    </div>
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Montant</th>
            <th>Type</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="table-prets-simules">
          <tr>
            <td colspan="6">Chargement...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- filtre par date -->
  <div class="form-row form-filtre align-center">
    <div class="form-group">
      <label for="mois_debut">Date début :</label>
      <input type="date" id="mois_debut" class="form-control-custom" />
    </div>
    <div class="form-group">
      <label for="mois_fin">Date fin :</label>
      <input type="date" id="mois_fin" class="form-control-custom" />
    </div>

    <div class="form-group">
      <label for="client_id">Client a filtrer</label>
      <select id="client_id" class="form-control-custom" required>
        <option value="">-- Choisir un client --</option>
      </select>
    </div>

    <div class="form-group filtre-btn">
      <button class="btn-primary-custom" onclick="chargerDonnees()">Filtrer</button>
    </div>
  </div>

  <!-- Tableau des revenu et interet -->
  <!-- Tableau des résultats -->
  <div class="table-container">
    <div class="table-header">
      <h3>Tableau des revenus simulés</h3>
    </div>
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

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Statistique des revenu -->
  <!-- Graphique -->
  <div class="mt-4 p-4">
    <canvas id="graph-interets" height="100"></canvas>
  </div>

  <!-- Bouton retour -->
  <div class="form-actions mt-4">
    <button class="btn btn-outline-secondary" onclick="window.history.back()">← Retour</button>
  </div>

  <script>
    const apiBase = "http://localhost:8000/ws";

    function ajax(method, url, data, callback) {
      const xhr = new XMLHttpRequest();
      xhr.open(method, apiBase + url, true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
          callback(JSON.parse(xhr.responseText));
        }
      };
      xhr.send(data);
    }

    // ---------- Formulaire Creation pret
    function chargerSelects() {
      ajax("GET", "/type_pret", null, (types) => {
        const select = document.getElementById("type_pret_id");
        select.innerHTML =
          '<option value="">-- Choisir un type de prêt --</option>';
        types.forEach((t) => {
          const option = document.createElement("option");
          option.value = t.id;
          option.text = `${t.nom} - Max ${t.montant_max_pres} Ar`;
          select.appendChild(option);
        });
      });

      ajax("GET", "/clients", null, (types) => {
        const select = document.getElementById("client_id");
        select.innerHTML =
          '<option value="">-- Choisir un client --</option>';
        types.forEach((t) => {
          const option = document.createElement("option");
          option.value = t.id;
          option.text = `${t.nom} + id : ${t.id}`;
          select.appendChild(option);
        });
      });
    }

    // Post creation pret simulé
    function ajouterPret() {
      const clientId = document.getElementById("clientId").value;
      const typePretId = document.getElementById("type_pret_id").value;
      const montant = document.getElementById("montant_emprunt").value;
      const dateDebut = document.getElementById("date_debut").value;
      const dateFin = document.getElementById("date_fin").value;

      const data = `client=${encodeURIComponent(clientId)}&type_pret_id=${encodeURIComponent(typePretId)}&montant_emprunt=${encodeURIComponent(montant)}&date_debut=${encodeURIComponent(dateDebut)}&date_fin=${encodeURIComponent(dateFin)}&is_pret_simulation=${1}`;

      ajax("POST", "/creation_pret", data, (response) => {
        alert(response.message);
        chargerDonnees();
        chargerPretSimule();
        resetForm();
        liste_pret_tableau();
      });
    }

    function resetForm() {
      document.getElementById("clientId").value = "";
      document.getElementById("type_pret_id").value = "";
      document.getElementById("montant_emprunt").value = "";
      document.getElementById("date_debut").value = "";
      document.getElementById("boutton-ajout-pret").innerHTML = "Ajouter Prêt";
    }

    // ---------- Liste de tous les pret avec crud
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

    // Charger la liste des prêts simulés
    function liste_pret_tableau() {
      fetch(apiBase + "/AllpretsSimuler")
        .then(res => res.json())
        .then(prets => {
          const tbody = document.getElementById("table-prets-simules");
          tbody.innerHTML = "";

          if (prets.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6">Aucun prêt simulé</td></tr>`;
          } else {
            prets.forEach(p => {
              tbody.insertAdjacentHTML("beforeend", `
          <tr>
            <td>${p.id}</td>
            <td>${p.client}</td>
            <td>${p.montant_emprunt.toLocaleString()} Ar</td>
            <td>${p.type_pret_id}</td>
            <td>${new Date(p.date_debut).toLocaleDateString()}</td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="chargerPretSimule(${p.id})">✏️</button>
              <button class="btn btn-sm btn-outline-danger" onclick="supprimerPret(${p.id})">🗑️</button>
            </td>
          </tr>
        `);
            });
          }
        });
    }

    function chargerDonnees() {
      fetch(apiBase + "/allPret/interet_EF")
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById("table-interets");
          tbody.innerHTML = "";

          const idClient = document.getElementById("client_id").value;

          const debutVal = document.getElementById("mois_debut").value;
          const finVal = document.getElementById("mois_fin").value;

          const dateDebut = debutVal ? new Date(debutVal) : null;
          const dateFin = finVal ? new Date(finVal) : null;

          if (dateDebut && dateFin && dateDebut > dateFin) {
            alert("La date de début doit être antérieure à la date de fin.");
            return;
          }

          let dataFiltrée = [];

          if (idClient) {
            dataFiltrée = data.filter(item => {
              const datePaiement = new Date(item.date_mois);
              const idClientPret = item.client;
              return (!dateDebut || datePaiement >= dateDebut) &&
                (!dateFin || datePaiement <= dateFin) && (idClientPret == idClient);
            });
          } else {
            dataFiltrée = data.filter(item => {
              const datePaiement = new Date(item.date_mois);
              return (!dateDebut || datePaiement >= dateDebut) &&
                (!dateFin || datePaiement <= dateFin);
            });
          }

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

    function supprimerPret(id) {
      fetch(`${apiBase}/pret_simule/${id}`, {
          method: "DELETE"
        })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          chargerSelects();
          chargerPretSimule();
          chargerDonnees();
        });
    }

    // PUT (modifier) prêt simulé
    function modifierPret(id) {
      let bouttonajoutpret = document.getElementById("boutton-ajout-pret");
      bouttonajoutpret.innerHTML = "modifier pret";

      const data = new URLSearchParams({
        client: 1,
        type_pret_id: 2,
        montant_emprunt: 500000,
        date_debut: '2025-07-10'
      });

      fetch(`${apiBase}/pret_simule/${id}`, {
          method: "PUT",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: data
        })
        .then(res => res.json())
        .then(data => {
          chargerSelects();
          chargerDonnees();
          chargerPretSimule();
          liste_pret_tableau();
          alert(data.message);
        });


    }

    function chargerPretSimule(id) {
      fetch(`${apiBase}/pret_simule/${id}`)
        .then(res => res.json())
        .then(data => {
          console.log("Données du prêt :", data);
          // Exemple de remplissage de formulaire
          document.getElementById("clientId").value = data.client;
          document.getElementById("type_pret_id").value = data.type_pret_id;
          document.getElementById("montant_emprunt").value = data.montant_emprunt;
          document.getElementById("date_debut").value = data.date_debut.substring(0, 10);
          document.getElementById("date_fin").value = data.date_fin.substring(0, 10);
        });

      document.getElementById("boutton-ajout-pret").innerHTML = "Modifier Prêt";
    }

    window.onload = () => {
      // Ajout dynamique idClient
      const urlParams = new URLSearchParams(window.location.search);
      const clientId = urlParams.get("clientId");
      if (clientId) {
        document.getElementById("clientId").value = clientId;
      }
      chargerSelects();
      chargerDonnees();
      liste_pret_tableau();
      chargerPretSimule();
    };
  </script>

  <!-- pop up -->
  <script>
    function ouvrirModal() {
      document.getElementById("pret-modal").style.display = "block";
    }

    function fermerModal() {
      document.getElementById("pret-modal").style.display = "none";
      resetForm();
    }

    window.onclick = function(event) {
      const modal = document.getElementById("pret-modal");
      if (event.target === modal) {
        fermerModal();
      }
    }
  </script>
  </div>
</main>