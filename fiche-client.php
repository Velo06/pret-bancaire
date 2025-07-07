<?php include 'header.html'; ?>
<main class="main-content">
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h3>Fiche client</h3>
                <p>Informations personnelles et liste des prêts</p>
            </div>

            <div id="client-details">
                <p>Chargement en cours...</p>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>Liste des prêts</h3>
                <button class="btn-primary-custom" onclick="nouveauPret()">+ Nouveau prêt</button>
            </div>

            <div class="table-responsive">
                <table class="custom-table" id="prets-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Taux</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8">Chargement en cours...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-actions mt-4">
            <button class="btn btn-outline-secondary" onclick="window.location.href='liste-client.html'">← Retour à la liste</button>
        </div>

        <script>
            const apiBase = "http://localhost:8000/ws";
            const clientId = new URLSearchParams(window.location.search).get('id');

            async function chargerDetailsClient() {
                if (!clientId) return showError("Client non trouvé");

                try {
                    await Promise.all([chargerInfosClient(), chargerPretsClient()]);
                } catch (e) {
                    showError("Erreur lors du chargement des données");
                }
            }

            async function chargerInfosClient() {
                const res = await fetch(`${apiBase}/clients/${clientId}`);
                const client = await res.json();
                renderClientDetails(client);
            }

            async function chargerPretsClient() {
                const res = await fetch(`${apiBase}/clients/${clientId}/prets`);
                const prets = await res.json();
                renderPretsTable(prets);
            }

            function renderClientDetails(c) {
                document.getElementById("client-details").innerHTML = `
      <div class="info-field"><strong>ID :</strong> ${c.id}</div>
      <div class="info-field"><strong>Nom :</strong> ${c.nom}</div>
      <div class="info-field"><strong>Nom d'utilisateur :</strong> ${c.username}</div>
      <div class="info-field"><strong>Email :</strong> ${c.email}</div>
      <div class="info-field"><strong>Téléphone :</strong> ${c.telephone}</div>
      <div class="info-field"><strong>Rôle :</strong> ${c.role_nom}</div>
      <div class="info-field"><strong>Statut :</strong> ${c.statut_nom}</div>
      <div class="info-field"><strong>Date inscription :</strong> ${formatDate(c.date_inscription)}</div>
    `;
            }

            function renderPretsTable(prets) {
                const tbody = document.querySelector("#prets-table tbody");
                if (!prets.length) {
                    tbody.innerHTML = `<tr><td colspan="8">Aucun prêt enregistré</td></tr>`;
                    return;
                }

                tbody.innerHTML = prets.map(p => `
      <tr>
        <td>${p.id}</td>
        <td>${p.type_pret_nom}</td>
        <td class="montant">${formatMoney(p.montant_emprunt)} Ar</td>
        <td>${(p.taux_interet_annuel * 100).toFixed(2)}%</td>
        <td>${formatDate(p.date_debut)}</td>
        <td>${p.date_fin ? formatDate(p.date_fin) : 'N/A'}</td>
        <td><span class="status-badge ${getStatusClass(p.etat_validation)}">${p.etat_validation}</span></td>
        <td>
          <button class="btn btn-sm btn-outline-primary me-1" onclick="voirDetailsPret(${p.id})">
            <i class="fa fa-eye"></i>
          </button>
          ${p.id_etat_validation === 1
            ? `<button class="btn btn-sm btn-outline-success" onclick="modifierPret(${p.id})"><i class="fa fa-edit"></i></button>`
            : ''}
        </td>
      </tr>
    `).join('');
            }

            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function formatMoney(val) {
                return new Intl.NumberFormat('fr-FR').format(val);
            }

            function getStatusClass(status) {
                return {
                    "en attente": "status-pending",
                    "validé": "status-approved",
                    "rejeté": "status-rejected"
                } [status.toLowerCase()] || "";
            }

            function showError(msg) {
                document.getElementById("client-details").innerHTML = `<div class="alert alert-danger">${msg}</div>`;
            }

            function nouveauPret() {
                window.location.href = `nouveau-pret.html?clientId=${clientId}`;
            }

            function voirDetailsPret(id) {
                window.location.href = `details-pret.html?id=${id}`;
            }

            function modifierPret(id) {
                window.location.href = `modifier-pret.html?id=${id}`;
            }

            window.onload = chargerDetailsClient;
        </script>
    </div>
</main>